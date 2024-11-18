<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST["username"];
    $pwd = $_POST["pwd"];
    $email = strtolower($_POST["email"]);

    try {
        require_once "dbh.inc.php";
        require_once "login_model.inc.php";
        require_once "login_contr.inc.php";
        require_once "login_view.inc.php";

        // ERRORS HANDLERS

        $errors = [];

        if (is_empty_input($username, $pwd)) {
            $errors["empty_inputs"] = "Fill in all fields";
        }
        $result = get_user($pdo, $username);
        if (is_username_wrong($result)) {
            $errors["login_incorrect"] = "Incorrect Login info!";
        }
        if (!is_username_wrong($result) && is_password_wrong($pwd, $result["pwd"])) {
            $errors["login_incorrect"] = "Incorrect Login info!";
        }

        require 'config_session.inc.php';

        if ($errors) {
            $_SESSION["errors_signup"] = $errors;

            header('Location: ../index.php');
            die();
        }
        $newSessionId = session_create_id();
        $sessionId = $newSessionId . '_' . $result['id'];
        $session_id($sessionId);

        $_SESSION['user_id'] = $result["id"];
        $_SESSION["user_username"] = htmlspecialchars($result["username"]);

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
