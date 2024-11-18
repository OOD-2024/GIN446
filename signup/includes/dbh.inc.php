<?php

$host = "localhost";
$dbname = "clinic";
$dbusername = "root";
$dbpassword = "pass";

try {
    $pdo = new PDO("mysql:host=$host;port=3308;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("" . $e->getMessage());
}
