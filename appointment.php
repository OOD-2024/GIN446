<?php
require_once 'includes/dbh.inc.php';

// Helper functions
require_once 'includes/appointment_controller.inc.php';
require_once 'includes/config_session.inc.php';
require_once 'includes/user_model.inc.php';

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
print_r($_SESSION);
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
    <link rel="stylesheet" href="css/schedule.css">
    <link rel="stylesheet" href="css/appointment.css">
    <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
    <nav style="position:relative;">
        <div class="logo">Cinlic</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="search.php">Services</a>
            <a href="about.php">About</a>
        </div>
        <ul id="drop" class="navdrop">
                <li>
                    <div class="profile-avatar">
                    <?php
                        if (!isset($_SESSION['login_user_id'])) {
                            echo 'Guest';
                        } else {
                            $user = getPatient_from_id($pdo, $_SESSION['login_user_id']);
                            echo strtoupper(substr($user['First_Name'], 0, 1));
                              echo strtoupper(substr($user['Last_Name'], 0, 1));
                        }
                        ?>
                    </div>
                    
                    <ul class="dropdown">
                        <?php
                        if (!isset($_SESSION['login_user_id'])) {
                            echo '<li><a href="signin_up.php"><button class="register-btn">Register</button></a></li>';
                        } else {
                            echo '<li><a href="user.php"><button class="register-btn">Profile</button></a></li>
                                <li><a href="logout.php"><button class="register-btn">Logout</button></a></li>';
                        }
                        ?>
                    </ul>
                </li>
            </ul>
    </nav>
    <?php if (isset($_SESSION['login_user_id'])) echo "<div id='login_id' hidden>" . $_SESSION['login_user_id'] . "</div>" ?>

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

    <?php if (!isset($error)): ?>
        <script>
            const eventsJson = <?php echo $eventsJson; ?>;
        </script>
        <script type="module" src="js/schedule.js"> </script>
    <?php endif; ?>
</body>

</html>