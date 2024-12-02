<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $Email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    $pwd = filter_var($_POST["pwd"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $email = strtolower($_POST["email"]);
    try {
        require_once "dbh.inc.php";
        require_once "login_model.inc.php";
        require_once "login_contr.inc.php";
        require_once "login_view.inc.php";
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        // ERRORS HANDLERS
        $errors = [];
        $success = [];

        if (is_empty_inputL($Email, $pwd)) {
            $errors["empty_inputs"] = "Fill in all fields";
        }
        require 'config_session.inc.php';

        $result = get_doctor($pdo, $Email, $pwd);

        if (is_doctor($pdo, $result['ID'])) {

            //go to doctor page
            $_SESSION["Doctor_ID"] = true;
            // $errors["login_correct"] = "Login Successfull!";


            // if (!is_email_wrong($result) && !is_password_wrong($pwd, $result["pwd"])) {
            //     $errors["login_incorrect"] = "Incorrect Login info!";
            // }
        } else {
            $result = get_patient($pdo, $Email, $pwd);



            if ($result == false) {
                $errors["errors_login"] = "Inavalid Info";
            }
        }
        if ($errors) {
            $_SESSION["errors_login"] = $errors;

            header('Location: ../signin_up.php');
            die();
        }

        regenerate_session_id_loggedin($pdo, $Email);
        $user_session_id = session_id();
        $_SESSION['user_session_id'] = $user_session_id;
        setsessionid($pdo, $Email, $user_session_id);

        // regenerate_session_id_loggedin($pdo, $Email);
        // $user_session_id = session_id();
        // setsessionid($pdo, $Email, $user_session_id);
        // $_SESSION['user_session_id'] = $user_session_id;

        if (is_doctor($pdo, $result['ID'])) {
            $_SESSION['Doctor_ID'] = true;
        }

        $_SESSION['login_user_id'] = $result['ID'];




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
