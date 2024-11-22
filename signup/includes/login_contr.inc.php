<?php
declare(strict_types=1);
require_once 'signup_controller.inc.php';

if (is_empty_input($Email, $pwd)) {

    if (empty($username) || empty($pwd)) {
        return true;
    } else {
        return false;
    }
}

function is_email_wrong(bool|array $result)
{
    if (!$result) {
        return true;
    } else {
        return false;
    }
}

function is_doctor(bool|array $result)
{
    if (!$result) {
        return false;
    } else {
        return true;
    }
}

function is_patient(bool|array $result)
{
    if (!$result) {
        return false;
    } else {
        return true;
    }
}

function is_password_wrong(string $pwd, string $resultpwd)
{
    if (!$pwd == $resultpwd) {
        return true;
    } else {
        return false;
    }
}


