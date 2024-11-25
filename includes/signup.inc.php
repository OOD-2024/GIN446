<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $Fname = htmlspecialchars($_POST["Fname"]);
    $Lname = htmlspecialchars($_POST["Lname"]);
    $Email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $pwd = $_POST["pwd"];
    $phonenum = $_POST["phone"];
    $DOB = $_POST["DOB"];
    $gender = $_POST["gender"];
    $BloodType = $_POST["BloodType"];

    try {
        require_once 'dbh.inc.php';

        require_once 'signup_model.inc.php';
        require_once 'signup_controller.inc.php';
        require_once 'signup_view.inc.php';

        // ERRORS HANDLERS

        $errors = [];

        if (is_empty_input($Fname, $pwd, $Email)) {
            $errors["empty_inputs"] = "Fill in all fields";
        }
        if (is_invalid_email($Email)) {
            $errors["invalid_email"] = "Invalid Email";
        }
        if (is_registered_email($pdo, $Email)) {
            $errors["email_used"] = "The Email is in use";
        }


        require 'config_session.inc.php';

        if ($errors) {
            $_SESSION["errors_signup"] = $errors;
            echo "Eroor";
            header('Location: ../index.php');
            die();
        }

        create_user($pdo, $Fname, $Lname, $Email, $pwd, $phonenum, $DOB, $gender, $BloodType);
        header('Location: ../index.php');
    } catch (PDOException $e) {
        die("Query has failed: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    die();
}