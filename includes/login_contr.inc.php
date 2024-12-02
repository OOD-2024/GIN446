<?php

declare(strict_types=1);
require_once 'signup_controller.inc.php';

function is_empty_inputL($Email, $pwd)
{

    if (empty($Email) || empty($pwd)) {
        return true;
    } else {
        return false;
    }
}


function is_doctor($pdo, $id)
{
    if (!$pdo || empty($id)) {
        return false;
    }

    return check_doctor($pdo, $id);
}
