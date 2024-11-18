<?php

declare(strict_types=1);
require 'dbh.inc.php';

function get_username(object $pdo, string $username)
{
    $query = "SElect username from patient where username = :username;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result;
}


function get_email(object $pdo, string $email)
{
    $query = "Select email from patient where email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":email", $email);

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result;
}
function create_user(object $pdo, string $Fname, string $Lname, string $email, string $password, string $phonenum, $DOB, string $gender, string $BloodType)
{
    $query = "Insert into patient (Fname , Lname , email, pwd , phoneNum , DOB , gender , BloodType) values (:Fname ,:Lname , :email, :pwd , :phonenum , :DOB , :gender ,:BloodType);";
    $stmt = $pdo->prepare($query);

    $options = ['cost' => 12];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, $options);

    $stmt->bindParam('Fname', $Fname);
    $stmt->bindParam('Lname', $Lname);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $hashed_password);
    $stmt->bindParam("phoneNum", $phonenum);
    $stmt->bindParam("DOB", date("y-m-d", $DOB));
    $stmt->bindParam("gender", $gender);
    $stmt->bindParam("BloodType", $BloodType);

    $stmt->execute();
}
