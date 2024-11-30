<?php
require_once 'dbh.inc.php';
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $requestid = $_POST['requestid'];
        $action = $_POST['action'];

        // Validate the action
        if ($action != 'accept' && $action != 'reject') {
            throw new Exception('Invalid action');
        }

        // Update the status
        $status = ($action == 'accept') ? 'Accepted' : 'Rejected';

        $stmt = $pdo->prepare("UPDATE requests SET status = :status WHERE requestid = :requestid");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':requestid', $requestid);
        $stmt->execute();

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