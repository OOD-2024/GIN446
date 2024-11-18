<?php
declare(strict_types=1);


if (is_empty_input($username, $pwd)) {

    if (empty($username) || empty($pwd)) {
        return true;
    } else {
        return false;
    }
}

function is_username_wrong(bool|array $result)
{
    if (!$result) {
        return true;
    } else {
        return false;
    }
}

function is_password_wrong(string $pwd, string $hashedpwd)
{
    if (!password_verify($pwd, $hashedpwd)) {
        return true;
    } else {
        return false;
    }
}


