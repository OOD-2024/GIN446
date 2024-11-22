<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $Email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    $pwd = $_POST["pwd"];

    $email = strtolower($_POST["email"]);
    try {
        require_once "dbh.inc.php";
        require_once "login_model.inc.php";
        require_once "login_contr.inc.php";
        require_once "login_view.inc.php";

        // ERRORS HANDLERS

        $errors = [];

        if (is_empty_input($Email, $pwd)) {
            $errors["empty_inputs"] = "Fill in all fields";
        }
        $result = get_doctor($pdo, $Email);
        if (is_doctor($result)) {
            if (!is_password_wrong($pwd, $result["pwd"])) {
                //go to doctor page
                $errors["login_correct"] = "Login Successfull!";

            } else {
                $errors["login_incorrect"] = "Incorrect Login info";
            }
        } else {
            $result = get_patient($pdo, $Email);
            if (is_patient($result)) {
                if (!is_password_wrong($pwd, $result["pwd"])) {
                    //got to patient page
                    $errors["login_correct"] = "Login Successfull";

                }
            }

            if (!is_email_wrong($result) && is_password_wrong($pwd, $result["pwd"])) {
                $errors["login_incorrect"] = "Incorrect Login info!";
            }
        }

        require 'config_session.inc.php';

        if ($errors) {
            $_SESSION["errors_login"] = $errors;

            header('Location: ../index.php');
            die();
        }
        $newSessionId = session_create_id();
        $sessionId = $newSessionId . '_' . $result['ID'];
        $_SESSION['user_id'] = $sessionId;
        setsessionid($pdo, $Email);
        $_SESSION['user_id'] = $result["id"];
        $_SESSION["user_Name"] = htmlspecialchars($result["First_Name" . " " . "Last_Name"]);

        $_SESSION["last_regeneration"] = time();
        header("Location:../index.php?login=success");
        $pdo = null;
        $statement = null;

        die();
    } catch (PDOException $e) {
        die("Query Failed" . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    die();
}
