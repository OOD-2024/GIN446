<?php
require_once 'includes/dbh.inc.php';
require_once 'includes/config_session.inc.php';

// Ensure session is started and user is authenticated
if (!isset($_SESSION['login_user_id'])) {
    header('Location: login.php');
    exit();
}

// Authorized admin IDs (consider moving to a configuration file)
$admins_ID = [117, 137, 6];

// Check if current user is an admin
if (!in_array($_SESSION['login_user_id'], $admins_ID)) {
    http_response_code(403);
    header('Location: 404.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Approval</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/docapprove.css">
</head>

<body>
    <nav>
        <div class="logo">Cinlic</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="search.php">Services</a>
            <a href="about.php">About</a>
        </div>
    </nav>

    <main>
        <?php
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();

            // Improved SQL query with proper JOIN syntax and added table aliases
            $stmt = $pdo->prepare('
         SELECT * from requests join patient join specialties WHERE patient_id = ID and speciality = speciality_id;
            ');
            $stmt->execute();
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($requests)) {
                echo '<div class="no-requests"><h1>No Pending Requests</h1></div>';
            } else {
                foreach ($requests as $request) {
                    $statusClass = 'status-' . strtolower($request['status']);

                    // Fetch PDFs for this request
                    $pdfStmt = $pdo->prepare('SELECT * FROM CV_PDF WHERE patient_id = :id');
                    $pdfStmt->bindParam(":id", $request['patient_id'], PDO::PARAM_INT);
                    $pdfStmt->execute();
                    $pdfs = $pdfStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
                    <div class="request-item">
                        <div class="request-details">
                            <h3>Request #<?= htmlspecialchars($request['requestid']) ?></h3>
                            <p><strong>Name:</strong> <?= htmlspecialchars($request['fullname']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($request['Email']) ?></p>
                            <p><strong>Phone Number:</strong> <?= htmlspecialchars($request['phoneNum']) ?></p>
                            <p><strong>Speciality:</strong> <?= htmlspecialchars($request['speciality_Name']) ?></p>
                            <p><strong>Experience:</strong> <?= htmlspecialchars($request['experience']) ?> years</p>
                            <p><strong>Status:</strong> <span class="<?= $statusClass ?>"><?= htmlspecialchars($request['status']) ?></span></p>

                            <div class="container">
                                <header>
                                    <h1>CV PDF</h1>
                                    <p>View and Download CV</p>
                                </header>

                                <?php if (empty($pdfs)): ?>
                                    <div class="no-pdfs">
                                        <p>No CV was submitted</p>
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
                                                    <div class="pdf-name"><?= htmlspecialchars($pdf['filename']) ?></div>
                                                    <div class="pdf-date">
                                                        Uploaded: <?= date('F j, Y', strtotime($pdf['upload_date'])) ?>
                                                    </div>
                                                    <div class="pdf-actions">
                                                        <a href="<?= htmlspecialchars($pdf['filepath']) ?>" class="btn btn-view" target="_blank">View</a>
                                                        <a href="<?= htmlspecialchars($pdf['filepath']) ?>" class="btn btn-download" download>Download</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (strtolower($request['status']) == 'pending'): ?>
                                <div class="button-group">
                                    <form method="POST" action="includes/requestapprove.php">
                                        <input type="hidden" name="requestid" value="<?= $request['requestid'] ?>">
                                        <input type="hidden" name="action" value="accept">
                                        <button type="submit" class="accept-btn">Accept</button>
                                    </form>
                                    <form method="POST" action="includes/requestapprove.php">
                                        <input type="hidden" name="requestid" value="<?= $request['requestid'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="reject-btn">Reject</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
        <?php
                }
            }
        } catch (PDOException $e) {
            // Log the error instead of displaying it
            error_log('Database Error: ' . $e->getMessage());
            echo '<div class="error">An error occurred while fetching requests.</div>';
        }
        ?>
    </main>
</body>

</html>