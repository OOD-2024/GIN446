<?php

require_once 'includes/dbh.inc.php';

?>
<!DOCTYPE html>
<html>

<head>
    <style>
        .request-list {
            max-width: 800px;
            margin: 20px auto;
        }

        .request-item {
            border: 1px solid #ddd;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .request-details {
            margin-bottom: 10px;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .accept-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .reject-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .status-pending {
            color: #ff9800;
        }

        .status-accepted {
            color: #4CAF50;
        }

        .status-rejected {
            color: #f44336;
        }

        @media (max-width: 640px) {
            .request-card {
                grid-template-columns: 1fr;
            }

            .button-group {
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    <?php
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare('select * from requests join specialties where speciality = Speciality_id;');
        $stmt->execute();
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);


        foreach ($requests as $request) {
            $statusClass = 'status-' . strtolower($request['status']);
            echo "<div class='request-item'>";
            echo "<div class='request-details'>";
            echo "<h3>Request #" . htmlspecialchars($request['requestid']) . "</h3>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($request['fullname']) . "</p>";
            echo "<p><strong>Speciality:</strong> " . htmlspecialchars($request['speciality_Name']) . "</p>";
            echo "<p><strong>Experience:</strong> " . htmlspecialchars($request['experience']) . " years</p>";
            echo "<p><strong>Status:</strong> <span class='" . $statusClass . "'>" . htmlspecialchars($request['status']) . "</span></p>";
            echo "</div>";
            echo "</div>";

            // Only show buttons if status is pending
            if (strtolower($request['status']) == 'pending') {
                echo "<div class='button-group'>";
                echo "<form method='POST' action='includes/requestapprove.php' style='display: inline;'>";
                echo "<input type='hidden' name='requestid' value='" . $request['requestid'] . "'>";
                echo "<input type='hidden' name='action' value='accept'>";
                echo "<button type='submit' class='accept-btn'>Accept</button>";
                echo "</form>";

                echo "<form method='POST' action='includes/requestapprove.php' style='display: inline;'>";
                echo "<input type='hidden' name='requestid' value='" . $request['requestid'] . "'>";
                echo "<input type='hidden' name='action' value='reject'>";
                echo "<button type='submit' class='reject-btn'>Reject</button>";
                echo "</form>";
                echo "</div>";

            }
        }
        echo "</div>";
    } catch (PDOException $e) {
        echo "" . $e->getMessage() . "";
    }

    ?>
</body>

</html>