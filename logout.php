<?php


session_id($_SESSION["guest_id"]);
session_start();
session_destroy();
header("Location:index.php");