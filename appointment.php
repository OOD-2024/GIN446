<?php
require_once 'includes/dbh.inc.php';

// Helper functions
require_once 'includes/appointment_controller.inc.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validate doctor_id
    $doctorId = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);
    if (!$doctorId) {
        throw new Exception("Invalid doctor ID");
    }

    // Get doctor details
    require_once 'includes/appointment_model.inc.php';
    $doctor = getDoctor($pdo, $doctorId);

    if (!$doctor) {
        throw new Exception("Doctor not found");
    }


    $events = getAppointmentEvents($pdo, $doctorId);

    $eventsJson = json_encode($events);
} catch (Exception $e) {
    error_log("Error in appointment.php: " . $e->getMessage());
    $error = "An error occurred while loading the appointments. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($doctor) ? "Appointments - Dr. " . h($doctor['First_Name']) . " " . h($doctor['Last_Name']) : "Appointments" ?>
    </title>
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="/css/schedule.css">
    <link rel="stylesheet" href="/css/appointment.css">
    <link rel="shortcut icon" href="/public/favicon.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
    <nav>
        <div class="logo">Clinic.io</div>
        <div class="nav-links">
            <a href="/">Home</a>
            <a href="/search.php">Services</a>
            <a href="/about.php">About</a>
        </div>
        <button class="register-btn">Log In</button>
    </nav>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <?= h($error) ?>
        </div>
    <?php else: ?>
        <div class="doctor-info">
            <h2>Dr. <?= h($doctor['First_Name']) ?>     <?= h($doctor['Last_Name']) ?></h2>
            <p>Specialties: <?= h($doctor['specialties']) ?></p>
        </div>

        <div class="calendar-wrapper">
            <div class="fixed-header">
                <h1 class="current-month"></h1>
                <div class="calendar" id="calendar-header"></div>
            </div>
            <div class="calendar-container">
                <div class="calendar" id="calendar-body"></div>
            </div>
        </div>
    <?php endif; ?>

    <footer>
        &copy; 2024 clinic.io. All rights reserved.
    </footer>

    <?php if (!isset($error)): ?>
        <script>
            // Pass the appointments data to JavaScript
            const events = <?= $eventsJson ?>;
        </script>
        <script type="module" src="/js/schedule.js"></script>
    <?php endif; ?>
</body>

</html>