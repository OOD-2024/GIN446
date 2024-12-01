<?php
require_once 'includes/dbh.inc.php';
try {

    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}

try {
    require_once 'includes/config_session.inc.php';
    // $userId = isset($_SESSION['login_user_id']) ? (int) $_SESSION['login_user_id'] : -1;
    $userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($userId === false || $userId === null) {
        header('HTTP/1.1 403 Forbidden');
        exit('Invalid user ID');
    }
    // echo $userId;
    require_once 'includes/user_model.inc.php';
    $user = getPatient_from_id($pdo, $userId);
    $locations = getDoctorLocations($pdo, $userId);

    if (!$user) {
        header("404.php");
        exit();
    }
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}


$dob = new DateTime($user['DOB']);
$today = new DateTime();
$age = $dob->diff($today)->y;

$createdDate = new DateTime($user['Created_AT']);
$formattedDate = $createdDate->format('F j, Y');


require_once 'includes/user_controller.inc.php';
$events = getAllAppointmentEvents($pdo, $userId);
$eventsJson = json_encode($events);
$records = getrecords($pdo, $userId);
$recordsJson = json_encode($records);
// print_r($_SESSION)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/user.css ">
    <link rel="stylesheet" href="css/schedule.css">
    <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        .diagnosis-list {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }

        .diagnosis-item {
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f5f5f5;
            border-left: 4px solid #2196f3;
            border-radius: 4px;
        }


        .diagnosis-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
    </style>
    <title><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?> - Profile</title>

</head>

<body>
    <nav>
        <div class="logo">Clinic.io</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="search.php">Services</a>
            <a href="about.php">About</a>
        </div>
        <a href="logout.php"><button class="register-btn">Logout</button></a>
    </nav>

    <main>

        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['First_Name'], 0, 1)); ?>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name">
                        <?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?>
                    </h1>
                    <p hidden>Patient ID: <?php echo htmlspecialchars($userId); ?></p>
                </div>
            </div>

            <div class="profile-details">
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['Email']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['phoneNum']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Age</div>
                    <div class="detail-value"><?php echo $age; ?> years</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Gender</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['gender']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Blood Type</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['BloodType']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Member Since</div>
                    <div class="detail-value"><?php echo $formattedDate; ?></div>
                </div>
            </div>
        </div>
        <div class='diagnosis-list'>
            <h2>Diagnosis List</h2>
            <?php

            foreach ($records as $rec) {

                echo '<div class="diagnosis-item"> 
                <div class ="diagnosis-title " >' . $rec['diagnosis'] . '</div> </div>';
            }
            ?>
        </div>
        <div class="calendar-wrapper">

            <div class="fixed-header">
                <h1 class="current-month"></h1>
                <div class="calendar" id="calendar-header"></div>
                <button id="add-event-btn"><i class="bx bx-plus"></i></button>

            </div>
            <div class="calendar-container">
                <div class="calendar" id="calendar-body"></div>
            </div>

        </div>
        <form id="add-event-form" style="display: none">
            <h2>Schedule New Appointment</h2>
            <input type="hidden" id="doctor-id" name="doctor_id" value="<?php echo $userId; ?>">
            <input type="hidden" id="doctor-id" name="patient_id" value="<?php echo $userId; ?>">

            <label for="appointment-date">Appointment Date:</label>
            <input type="date" id="appointment-date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"

                name="appointment_date" required>

            <label for="appointment-start">Start Time:</label>
            <input type="time" id="appointment-start" name="start_time" required>

            <label for="appointment-end">End Time:</label>
            <input type="time" id="appointment-end" name="end_time" required>

            <label for="location">Location:</label>

            <select id="location" name="location_id" required>
                <option value="">Select Location</option>
                <?php
                if (empty($locations)) {
                    echo "<option value=''>No locations available</option>";
                    return;
                }

                foreach ($locations as $row) {
                    $location_description = implode(", ", array_filter([
                        $row['Building'],
                        $row['Street'],
                        $row['City'],
                        $row['Country']
                    ]));
                    echo "<option value='" . htmlspecialchars($row['ID']) . "'>" .
                        htmlspecialchars($location_description) . "</option>";
                }

                ?>
            </select>

            <div class="form-buttons">
                <button type="button" id="cancel-add-event">Cancel</button>
                <button type="submit">Schedule Appointment</button>
            </div>
        </form>
    </main>



    <footer>
        &copy; 2024 clinic.io. All rights reserved.
    </footer>
    <script>
        const events = <?php echo $eventsJson; ?>;
    </script>
    <script type="module" src="/js/schedule.js"> </script>
    <script type="module" src="/js/events.js"> </script>
</body>

</html>