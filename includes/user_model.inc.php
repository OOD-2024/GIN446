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
function getAppointments($pdo, $patient_id)
{
    $startOfWeek = date('Y-m-d', strtotime('last Sunday'));
    $endOfWeek = date('Y-m-d', strtotime('next Saturday'));

    $query = "
    SELECT 
    a.AppointmentId,
    CONCAT('Dr. ', p.First_Name, ' ', p.Last_Name) as doctor_name,
    a.Appointment_Date,
    a.StartTime,
    a.EndTime,
    CONCAT(l.Building, ', ', l.Street, ', ', l.City, ', ', l.Country) as location,
    a.Note
    FROM appointment a
    JOIN doctor d ON a.DoctorID = d.ID
    JOIN patient p ON d.ID = p.ID
    LEFT JOIN location l ON a.LocationID = l.ID
    WHERE a.PatientID = :id
    AND a.Appointment_Date BETWEEN :start AND :end
    AND a.Appointment_Status != 'Cancelled';
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':id' => $patient_id,
            ':start' => $startOfWeek,
            ':end' => $endOfWeek
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
