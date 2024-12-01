<?php
require_once("dbh.inc.php");
require_once("config_session.inc.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $speciality = $_POST['specialty'];
    $eperience = $_POST['experience'];
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("select * from patient where id =:id");
        $stmt->bindParam(":id", $_SESSION['login_user_id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        $fullname = $result['First_Name'] . ' ' . $result['Last_name'];


        $stmt = $pdo->prepare('insert into requests(patient_id , fullname , speciality , experience ) values(:id , :name , :speciality ,:experience )');
        $stmt->bindParam(':id', $_SESSION['login_user_id']);
        $stmt->bindParam(':name', $fullname);
        $stmt->bindParam(':speciality', $speciality);
        $stmt->bindParam(':experience', $eperience);

        $stmt->execute();
        $_SESSION['success'] = 'true';
        header("Location:../index.php");



    } catch (PDOException $e) {
        echo $e->getMessage();
    }

}