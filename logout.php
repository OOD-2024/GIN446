<?php

require_once 'includes/config_session.inc.php';
$first_time = $_SESSION['first_time'] ?? null;
session_unset();
session_destroy();
session_start();
if ($first_time !== null) {
    $_SESSION['first_time'] = $first_time;
}
header("Location:index.php");
exit();
