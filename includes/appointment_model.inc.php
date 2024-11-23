<?php
function getDoctor($pdo, $doctorId)
{
    $doctorQuery = "
    SELECT 
        p.First_Name,
        p.Last_Name,
        GROUP_CONCAT(DISTINCT s.Specialty_Name) as specialties
    FROM doctor d
    INNER JOIN patient p ON d.ID = p.ID
    LEFT JOIN specialty s ON d.ID = s.DoctorID
    WHERE d.ID = :doctorId
    GROUP BY p.First_Name, p.Last_Name
";

    $stmt = $pdo->prepare($doctorQuery);
    $stmt->execute([':doctorId' => $doctorId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAppointments($pdo, $doctorId)
{
    $appointmentQuery = "
    SELECT 
        a.AppointmentID,
        a.Appointment_Date,
        a.StartTime,
        a.EndTime,
        a.Note,
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
        ) as location
    FROM appointment a
    INNER JOIN doctor d ON a.DoctorID = d.ID
    INNER JOIN patient p ON d.ID = p.ID
    LEFT JOIN location l ON a.LocationID = l.ID
    WHERE a.DoctorID = :doctorId 
    AND a.Appointment_Status = 'Available'
    AND a.Appointment_Date >= CURRENT_DATE
    ORDER BY a.Appointment_Date ASC, a.StartTime ASC
";

    $stmt = $pdo->prepare($appointmentQuery);
    $stmt->execute([':doctorId' => $doctorId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
