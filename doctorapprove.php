<?php

require_once 'includes/dbh.inc.php';
require_once 'includes/config_session.inc.php';

?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/docapprove.css">
</head>

<body>
    <nav>
        <div class="logo">Cinlic</div>
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="search.php">Services</a>
            <a href="about.php">About</a>
        </div>



    </nav>

    <?php
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        $admins_ID = [117, 137, 69];


        if (!in_array($_SESSION['login_user_id'], $admins_ID)) {
            header('Location:404.php');
        }

        $stmt = $pdo->prepare('select * from requests join specialties join patient where speciality = Speciality_id and patient_id=id;');
        $stmt->execute();
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$requests) {
            echo '<h1> No pending Requests</h1>';
        } else {
            foreach ($requests as $request) {
                $statusClass = 'status-' . strtolower($request['status']);
                echo "<div class='request-item'>";
                echo "<div class='request-details'>";
                echo "<h3>Request #" . htmlspecialchars($request['requestid']) . "</h3>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($request['fullname']) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($request['Email']) . "</p>";
                echo "<p><strong>Phone Number:</strong> " . htmlspecialchars($request['phoneNum']) . "</p>";
                echo "<p><strong>Speciality:</strong> " . htmlspecialchars($request['speciality_Name']) . "</p>";
                echo "<p><strong>Experience:</strong> " . htmlspecialchars($request['experience']) . " years</p>";
                echo "<p><strong>Status:</strong> <span class='" . $statusClass . "'>" . htmlspecialchars($request['status']) . "</span></p>";
                $query = "SELECT * FROM CV_PDF where patient_id = :id ; ";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":id", $request['patient_id']);
                $stmt->execute();
                $pdfs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div class="container">
                    <header>
                        <h1>CV PDF</h1>
                        <p>View and Download CV </p>

                    </header>

                    <?php if (empty($pdfs)): ?>
                        <div class="no-pdfs">
                            <p>No CV was submitted </p>
                        </div>
                    <?php else: ?>
                        <div class="pdf-grid">
                            <?php foreach ($pdfs as $pdf): ?>
                                <div class="pdf-card">
                                    <svg class="pdf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                    <div class="pdf-info">
                                        <div class="pdf-name"><?php echo htmlspecialchars($pdf['filename']); ?></div>
                                        <div class="pdf-date">
                                            Uploaded: <?php echo date('F j, Y', strtotime($pdf['upload_date'])); ?>
                                        </div>
                                        <div class="pdf-actions">
                                            <a href="<?php echo htmlspecialchars($pdf['filepath']); ?>" class="btn btn-view"
                                                target="_blank">View</a>
                                            <a href="<?php echo htmlspecialchars($pdf['filepath']); ?>" class="btn btn-download"
                                                download>Download</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif;

                    echo " </div>";
                    echo "</div>";
                    echo "</div>"; ?>


                    <?php

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

                    echo "</div>";
            }
        }
    } catch (PDOException $e) {
        echo "" . $e->getMessage() . "";
    }


    ?>

</body>



</html>