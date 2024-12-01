<?php

declare(strict_types=1);

function getPatient_from_id($pdo, $patient_id)
{
    $query = "SELECT First_Name, Last_Name, Email, phoneNum, DOB, gender, BloodType, Created_AT FROM patient WHERE ID = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $patient_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}
function getAllAppointments($pdo, $patient_id)
{
    $startOfWeek = date('Y-m-d', strtotime('last Sunday'));
    $endOfWeek = date('Y-m-d', strtotime('next Saturday'));

    $query = "
  SELECT 
    a.AppointmentID,
    a.Appointment_Date,
    a.StartTime,
    a.EndTime,
    a.Note,
    a.Appointment_Status as status,
    CONCAT(p.First_Name, ' ', p.Last_Name) as doctor_name,
    CONCAT(
        l.City, ', ', 
        l.Country,
        CASE 
            WHEN l.Building IS NOT NULL THEN CONCAT(', ', l.Building)
            ELSE ''
        END,
        CASE 
            WHEN l.Street IS NOT NULL THEN CONCAT(', ', l.Street)
            ELSE ''
        END
    ) as location,
    (SELECT GROUP_CONCAT(s.Specialty_Name SEPARATOR ', ') 
     FROM specialty s WHERE s.DoctorID = d.ID) as specialty
FROM appointment a
INNER JOIN doctor d ON a.DoctorID = d.ID
INNER JOIN patient p ON d.ID = p.ID
LEFT JOIN location l ON a.LocationID = l.ID
WHERE (a.patientid = :id OR a.doctorid = :id)
ORDER BY a.Appointment_Date

    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':id' => $patient_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAppointments: " . $e->getMessage());
        return [];
    }
}
function get_medical_records($pdo, $patient_id)
{
    $query = "Select * from medical_record  where PatientId = :ID";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':ID', $patient_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in get_medical_records: " . $e->getMessage());
        return [];
    }
}

function getDoctorLocations($pdo, $user_id)
{
    $query = "SELECT ID, Country, City, Building, Street FROM location l where exists (
    Select * from doctor_locations dl where dl.doctorid = :id and dl.LocationID = l.id
    );";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createAppointment($pdo, $array)
{
    try {

        $appointmentDate = $array['appointment_date'];
        $startTime = $array['start_time'];
        $endTime = $array['end_time'];
        $locationId = $array['location_id'];
        $doctorId = $array['doctor_id'];
        $patientId = $array['patient_id'];

        $query = "INSERT INTO appointment (DoctorID, PatientID, Appointment_Date, LocationID, StartTime, EndTime, Appointment_Status) 
        VALUES (:doctor_id, :patient_id, :appointment_date, :location_id, :start_time, :end_time, 'Scheduled')";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->bindParam(':appointment_date', $appointmentDate, PDO::PARAM_STR);
        $stmt->bindParam(':location_id', $locationId, PDO::PARAM_INT);
        $stmt->bindParam(':start_time', $startTime, PDO::PARAM_STR);
        $stmt->bindParam(':end_time', $endTime, PDO::PARAM_STR);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
