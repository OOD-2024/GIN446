<?php

declare(strict_types=1);


function is_empty_input($email, $pwd, ): bool
{
    if (empty($pwd) || empty($email)) {
        return true;
    }
    return false;
}

function is_invalid_email($email): bool
{
    if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    return false;
}

function is_registered_email(object $pdo, string $email): bool
{
    if (get_email($pdo, $email)) {
        return true;
    }
    return false;
}
