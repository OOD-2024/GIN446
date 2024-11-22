<?php
declare(strict_types=1);

require_once 'config_session.inc.php';


function get_doctor(object $pdo, string $email)
{
    $query = "SELECT * from patient NATURAL JOIN doctor WHERE Email = :email;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}
function get_patient(object $pdo, string $email)
{
    $query = "Select * FROM patient WHERE Email = :email;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function setsessionid($pdo, $email)
{
    $query = "update patient set session_id = :sessionid , updated_at = :date where Email= $email";
    $stmt = @$pdo->prepare($query);
    $stmt->bindParam(":sessionid", $_SESSION['user_id']);
    $stmt->bindParam(':date', date('Y-m-d H:i:s'));
}
