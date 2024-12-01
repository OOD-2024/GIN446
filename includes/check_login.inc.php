<?php
require_once 'config_session.inc.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    $query = "SELECT * from patient WHERE ID = :id ;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":ID", $email);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($_SESSION["user_session_id"] != $result['Session_ID']) {
        $data['output'] = 'logout';
    } else {
        $data['output'] = 'login';
    }
    echo json_encode($data);

} catch (PDOException $e) {
    die("Query Failed" . $e->getMessage());
}