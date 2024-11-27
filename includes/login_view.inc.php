<?php


function check_login_errors()
{
    if (isset($_SESSION["errors_login"])) {
        $errors = $_SESSION["errors_login"];

        echo '<br>';

        foreach ($errors as $err) {
            echo "<div class='error'><p> . $err . </p></div>";
        }
        unset($_SESSION["errors_login"]);
    } elseif (isset($_GET["login"]) && $_GET["login"] === "success") {

    }
}