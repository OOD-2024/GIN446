<?php
require_once 'dbh.inc.php';
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $requestid = $_POST['requestid'];
        $action = $_POST['action'];
        $stmt = $pdo->prepare("select * from requests where requestid = :requestid");
        $stmt->bindParam(':requestid', $requestid);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $patientid = $result['patient_id'];

        // Validate the action
        if ($action != 'accept' && $action != 'reject') {
            throw new Exception('Invalid action');
        }
        if ($action == 'accept') {
            // Update the status
            $status = 'Accepted';

            $stmt = $pdo->prepare("UPDATE requests SET status = :status WHERE requestid = :requestid");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':requestid', $requestid);
            $stmt->execute();

            $stmt = $pdo->prepare("insert into doctor(ID) values (:id)");
            $stmt->bindParam(':id', $patientid);
            $stmt->execute();

        } else {
            $status = 'Rejected';
            $stmt = $pdo->prepare("UPDATE requests SET status = :status WHERE requestid = :requestid");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':requestid', $requestid);
            $stmt->execute();
        }
        // Redirect back to the main page
        header("Location:../doctorapprove.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>