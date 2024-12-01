<?php
// Include necessary files
require_once 'dbh.inc.php';
$db = Database::getInstance();
$pdo = $db->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $appointmentDate = $_POST['appointment_date'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $locationId = $_POST['location_id'];
        $doctorId = $_POST['doctor_id'];
        $patientId = $_POST['patient_id'];

        // Validate the input data
        if (
            !is_string($appointmentDate) || !is_string($startTime) || !is_string($endTime) ||
            !is_numeric($locationId) || !is_numeric($doctorId) || !is_numeric($patientId)
        ) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            exit;
        }

        // Check if locationID, doctorId, and patientId exist in the database
        $validationQueries = [
            'location' => "SELECT COUNT(*) FROM location WHERE ID = :id",
            'doctor' => "SELECT COUNT(*) FROM doctor WHERE ID = :id",
            'patient' => "SELECT COUNT(*) FROM patient WHERE ID = :id"
        ];

        foreach ($validationQueries as $type => $query) {
            $stmt = $pdo->prepare($query);
            $id = $type === 'location' ? $locationId : ($type === 'doctor' ? $doctorId : $patientId);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetchColumn() == 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Invalid $type ID"]);
                exit;
            }
        }

        // Check for existing appointments with time conflicts
        $conflictQuery = "
            SELECT COUNT(*) FROM appointment 
            WHERE DoctorID = :doctor_id 
            AND Appointment_Date = :appointment_date 
            AND (
                (StartTime < :end_time AND EndTime > :start_time)
                OR (StartTime = :start_time AND EndTime = :end_time)
            )
            AND Appointment_Status NOT IN ('Cancelled', 'Rejected')
        ";
        $conflictStmt = $pdo->prepare($conflictQuery);
        $conflictStmt->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
        $conflictStmt->bindParam(':appointment_date', $appointmentDate, PDO::PARAM_STR);
        $conflictStmt->bindParam(':start_time', $startTime, PDO::PARAM_STR);
        $conflictStmt->bindParam(':end_time', $endTime, PDO::PARAM_STR);
        $conflictStmt->execute();

        if ($conflictStmt->fetchColumn() > 0) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Appointment time conflicts with existing appointments']);
            exit;
        }

        // Save the appointment to the database
        $query = "INSERT INTO appointment (
            DoctorID, PatientID, Appointment_Date, LocationID, 
            StartTime, EndTime, Appointment_Status
        ) VALUES (
            :doctor_id, :patient_id, :appointment_date, :location_id, 
            :start_time, :end_time, 'Available'
        )";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':doctor_id' => $doctorId,
            ':patient_id' => $patientId,
            ':appointment_date' => $appointmentDate,
            ':location_id' => $locationId,
            ':start_time' => $startTime,
            ':end_time' => $endTime
        ]);

        // Return a success response
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Appointment scheduled successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error scheduling appointment: ' . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);

    try {
        // Validate input
        if (!isset($input['appointmentId']) || !isset($input['action'])) {
            throw new Exception('Missing required parameters');
        }

        $appointmentId = $input['appointmentId'];
        $action = $input['action'];

        // Start a transaction
        $pdo->beginTransaction();

        // Prepare different update queries based on action
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

        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            $pdo->commit();
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
        // Rollback the transaction in case of error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // Log the error
        error_log("Appointment update error: " . $e->getMessage());

        // Send error response
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
