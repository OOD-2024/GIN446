<?php

declare(strict_types=1);


function get_doctor(object $pdo, string $email, $pwd)
{
    $query = "SELECT * from patient NATURAL JOIN doctor WHERE Email = :email and pwd = :pwd;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":pwd", $pwd);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function check_doctor(object $pdo, $id)
{
    $query = "SELECT * from   doctor WHERE id = :id ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $result = $stmt->rowCount();
    return $result > 0;
}
function get_patient(object $pdo, string $email, $pwd)
{
    $query = "Select * FROM patient WHERE Email = :email and pwd = :pwd;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":pwd", $pwd);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function setsessionid($pdo, $email, $sessionId)
{
    $query = "UPDATE patient SET Session_ID = :sessionid WHERE Email = :email;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":sessionid", $sessionId);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

}
