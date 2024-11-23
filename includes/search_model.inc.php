<?php

require_once 'dbh.inc.php';

function findDoctors($pdo, array $params, array $where_conditions)
{

    $query = "
    SELECT DISTINCT 
        p.ID,
        p.First_Name,
        p.Last_Name,
        GROUP_CONCAT(DISTINCT s.Specialty_Name) as Specialties,
        GROUP_CONCAT(DISTINCT CONCAT(l.Country, ', ', l.City)) as Locations
    FROM patient p
    INNER JOIN doctor d ON p.ID = d.ID
    LEFT JOIN specialty s ON d.ID = s.DoctorID
    LEFT JOIN appointment a ON d.ID = a.DoctorID
    LEFT JOIN location l ON a.LocationID = l.ID
";

    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }

    $query .= " GROUP BY p.ID, p.First_Name, p.Last_Name";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return  $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Query failed: " . $e->getMessage());
        return [];
    }
}
