<?php
require_once 'dbh.inc.php';
require_once 'config_session.inc.php';
header('Content-Type: application/json');
require_once 'schedule_model.inc.php';


$db = Database::getInstance();
$pdo = $db->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Error handling function
function handleErrors($errors)
{
    if (!empty($errors)) {
        $_SESSION["errors_appointment"] = $errors;
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Failed'
        ]);
        die();
    }
}

function validateInput($input, $type)
{
    $errors = [];
    switch ($type) {
        case 'date':
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input)) {
                $errors[] = "Invalid date format";
            }
            break;
        case 'time':
            if (!preg_match('/^\d{2}:\d{2}$/', $input)) {
                $errors[] = "Invalid time format";
            }
            break;
        case 'id':
            if (!is_numeric($input) || $input <= 0) {
                $errors[] = "Invalid ID";
            }
            break;
    }
    return $errors;
}

function scheduleAppointment($pdo, $data)
{
    $errors = [];

    $errors = array_merge(
        $errors,
        validateInput($data['appointment_date'], 'date'),
        validateInput($data['start_time'], 'time'),
        validateInput($data['end_time'], 'time'),
        validateInput($data['location_id'], 'id'),
        validateInput($data['doctor_id'], 'id'),
        validateInput($data['patient_id'], 'id')
    );

    if (strtotime($data['start_time']) >= strtotime($data['end_time'])) {
        $errors[] = "Start time must be before end time";
    }

    $appointmentDateTime = new DateTime($data['appointment_date'] . ' ' . $data['start_time']);
    $now = new DateTime();
    if ($appointmentDateTime <= $now) {
        $errors[] = "Appointment must be in the future";
    }

    $validationQueries = [
        'location' => "SELECT COUNT(*) FROM location WHERE ID = :id",
        'doctor' => "SELECT COUNT(*) FROM doctor WHERE ID = :id",
        'patient' => "SELECT COUNT(*) FROM patient WHERE ID = :id"
    ];

    foreach ($validationQueries as $type => $query) {
        $stmt = $pdo->prepare($query);
        $id = match ($type) {
            'location' => $data['location_id'],
            'doctor' => $data['doctor_id'],
            'patient' => $data['patient_id']
        };
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $errors[] = "Invalid $type ID";
        }
    }

    if (empty($errors)) {
        $conflictQuery = "
            SELECT COUNT(*) FROM appointment
            WHERE 
                (DoctorID = :doctor_id OR PatientID = :patient_id)
                AND Appointment_Date = :appointment_date
                AND (
                    (StartTime < :end_time AND EndTime > :start_time)
                    OR (StartTime = :start_time AND EndTime = :end_time)
                )
                AND Appointment_Status NOT IN ('Cancelled', 'Rejected')
        ";
        $conflictStmt = $pdo->prepare($conflictQuery);
        $conflictStmt->bindParam(':doctor_id', $data['doctor_id'], PDO::PARAM_INT);
        $conflictStmt->bindParam(':patient_id', $data['patient_id'], PDO::PARAM_INT);
        $conflictStmt->bindParam(':appointment_date', $data['appointment_date'], PDO::PARAM_STR);
        $conflictStmt->bindParam(':start_time', $data['start_time'], PDO::PARAM_STR);
        $conflictStmt->bindParam(':end_time', $data['end_time'], PDO::PARAM_STR);
        $conflictStmt->execute();

        if ($conflictStmt->fetchColumn() > 0) {
            $errors[] = "Appointment time conflicts with existing appointments";
        }
    }

    if (empty($errors)) {
        try {
            $query = "INSERT INTO appointment (
                DoctorID, PatientID, Appointment_Date, LocationID,
                StartTime, EndTime, Appointment_Status
            ) VALUES (
                :doctor_id, :patient_id, :appointment_date, :location_id,
                :start_time, :end_time, 'Available'
            )";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':doctor_id' => $data['doctor_id'],
                ':patient_id' => $data['patient_id'],
                ':appointment_date' => $data['appointment_date'],
                ':location_id' => $data['location_id'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time']
            ]);

            $_SESSION['success_appointment'] = "Appointment scheduled successfully";
            return true;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {


        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $appointmentData = [
            'appointment_date' => htmlspecialchars(trim($_POST['appointment_date'] ?? '')),
            'start_time' => htmlspecialchars(trim($_POST['start_time'] ?? '')),
            'end_time' => htmlspecialchars(trim($_POST['end_time'] ?? '')),
            'location_id' => filter_input(INPUT_POST, 'location_id', FILTER_VALIDATE_INT),
            'doctor_id' => filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT),
            'patient_id' => filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT)
        ];

        if (!isset($_SESSION['login_user_id'])) {
            $errors[] = "You must be logged in to schedule an appointment";
            handleErrors($errors);
        }

        $result = scheduleAppointment($pdo, $appointmentData);

        if ($result === true) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'successful'
            ]);
            exit();
        } else {
            handleErrors($result);
        }
    } catch (Exception $e) {
        $errors[] = "Unexpected error: " . $e->getMessage();
        handleErrors($errors);
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);

    try {
        if (!isset($input['appointmentId']) || !isset($input['action'])) {
            throw new Exception('Missing required parameters');
        }

        $appointmentId = $input['appointmentId'];
        $action = $input['action'];

        $pdo->beginTransaction();

        switch ($action) {
            case 'book':
                if (!isset($input['patientId'])) {
                    throw new Exception('Patient ID is required for booking');
                }
                $patientId = $input['patientId'];

                $stmt = $pdo->prepare("
                    UPDATE appointment 
                    SET Appointment_Status = 'Pending', 
                        PatientID = :patient_id
                    WHERE AppointmentID = :appointmentId 
                    AND Appointment_Status = 'Available'
                ");
                $stmt->execute([
                    ':patient_id' => $patientId,
                    ':appointmentId' => $appointmentId
                ]);
                break;

            case 'accept':
                $stmt = $pdo->prepare("
                    UPDATE appointment 
                    SET Appointment_Status = 'Scheduled'
                    WHERE AppointmentID = :appointmentId 
                    AND Appointment_Status = 'Pending'
                ");
                $stmt->execute([':appointmentId' => $appointmentId]);
                break;

            case 'reject':
                $stmt = $pdo->prepare("
                    INSERT INTO rejected_appointments (AppointmentID, RejectionDate)
                    VALUES (:appointmentId, NOW());

                    UPDATE appointment 
                    SET Appointment_Status = 'Available',
                    PATIENTID = DOCTORID
                    WHERE AppointmentID = :appointmentId 
                    AND Appointment_Status = 'Pending'
                ");
                $stmt->execute([':appointmentId' => $appointmentId]);
                break;

            case 'cancel':
                $stmt = $pdo->prepare("
                    UPDATE appointment 
                    SET Appointment_Status = 'Cancelled'
                    WHERE AppointmentID = :appointmentId 
                    AND Appointment_Status = 'Scheduled'
                ");
                $stmt->execute([':appointmentId' => $appointmentId]);
                break;
            case 'confirm':
                $stmt = $pdo->prepare("
                    UPDATE appointment 
                    SET Appointment_Status = 'Completed'
                    WHERE AppointmentID = :appointmentId 
                    AND Appointment_Status = 'Scheduled'
                ");
                $stmt->execute([':appointmentId' => $appointmentId]);
                break;


            default:
                throw new Exception('Invalid action');
        }
        if ($stmt->rowCount() > 0) {
            $pdo->commit();
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Appointment status updated successfully'
            ]);
        } else {
            $pdo->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Unable to update appointment or appointment no longer available'
            ]);
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log("Appointment update error: " . $e->getMessage());

        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}
