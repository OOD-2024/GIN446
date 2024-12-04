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
    <link rel="stylesheet" href="css/appointment_summary.css">
    <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

</head>

<body>
    <nav>
        <div class="logo">Cinlic</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="search.php">Services</a>
            <a href="about.php">About</a>
        </div>

        <?php if (!isset($_SESSION['login_user_id'])) : ?>
            <a href="signin_up.php"><button class="register-btn">Register</button></a>
        <?php else : ?>
            <ul id="drop" class="navdrop">
                <li>
                    <div class="profile-avatar">
                        <?php
                        $user = getPatient_from_id($pdo, $_SESSION['login_user_id']);
                        echo strtoupper(substr($user['First_Name'], 0, 1));
                        echo strtoupper(substr($user['Last_Name'], 0, 1));
                        ?>
                    </div>
                    <ul class="dropdown">
                        <li><a href="user.php"><button class="register-btn">Profile</button></a></li>
                        <?php
                        if (!isset($_Session['Doctor_ID'])) {
                            echo '<li><a href="career.php"><button class="register-btn">Careers</button></a></li>';
                        }
                        ?>
                        <li><a href="logout.php"><button class="register-btn">Logout</button></a></li>
                    </ul>
                </li>
            </ul>
        <?php endif; ?>
    </nav>
    <?php if (isset($_SESSION['login_user_id'])) echo "<div id='login_id' hidden>" . $_SESSION['login_user_id'] . "</div>" ?>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <?= h($error) ?>
        </div>
    <?php else: ?>
        <div class="doctor-info">
            <h2>Dr. <?= h($doctor['First_Name']) ?> <?= h($doctor['Last_Name']) ?></h2>
            <p>Specialties: <?= h($doctor['specialties']) ?></p>
        </div>

        <div class="appointment-summary">
            <h2>Appointment Summary</h2>
            <!-- <div class="appointment-filters">
                <label for="sort-by">Sort by:</label>
                <select id="sort-by">
                    <option value="date">Date</option>
                    <option value="status">Status</option>
                </select>
            </div> -->
            <div class="appointment-list">
                <div class="appointment-cards">
                    <?php
                    usort($events, function ($a, $b) {
                        $dateA = strtotime($a['appointment_date']);
                        $dateB = strtotime($b['appointment_date']);
                        return $dateA - $dateB;
                    });

                    foreach ($events as $appointment) {
                        $status = $appointment['status'];
                        $statusClass = strtolower($status);
                        // $statusClass = '';
                        // if ($status == 'Completed') {
                        //     $statusClass = 'completed';
                        // } elseif ($status == 'Scheduled' || $status == 'In Progress') {
                        //     $statusClass = 'in-progress';
                        // } else {
                        //     $statusClass = 'pending';
                        // }
                        echo '<div class="appointment-card ' . $statusClass . '">';
                        echo '<h3>' . htmlspecialchars($appointment['name']) . '</h3>';
                        echo '<p>Date: ' . htmlspecialchars(date('Y-m-d', strtotime($appointment['appointment_date']))) . '</p>';
                        echo '<p>Time: ' . htmlspecialchars($appointment['startTime']) . ' - ' . htmlspecialchars($appointment['endTime']) . '</p>';
                        echo '<p>Doctor: ' . htmlspecialchars($appointment['doctor']) . '</p>';
                        echo '<p>Location: ' . htmlspecialchars($appointment['location']) . '</p>';
                        echo '<p>Note: ' . htmlspecialchars($appointment['note']) . '</p>';
                        echo '<div class="appointment-status">';
                        echo '<span class="status-label">' . htmlspecialchars($status) . '</span>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="pagination">
                    <button class="prev-btn"><i class='bx bx-chevron-left'></i></button>
                    <button class="next-btn"><i class='bx bx-chevron-right'></i></button>
                </div>
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
            <script type="module" src="js/account_summary.js"></script>
        <?php endif; ?>
</body>

</html>