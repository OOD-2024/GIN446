<?php
require_once 'includes/dbh.inc.php';
require_once 'includes/schedule_view.inc.php';
try {

    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}

try {
    require_once 'includes/config_session.inc.php';
    if (!isset($_SESSION['login_user_id'])) {
        http_response_code(403);
        die();
    }
    $userId = $_SESSION['login_user_id'];
    $is_doctor = isset($_SESSION['Doctor_ID']);

    require_once 'includes/user_model.inc.php';
    $user = getPatient_from_id($pdo, $userId);
    $locations = getDoctorLocations($pdo, $userId);

    if (!$user) {
        http_response_code(403);
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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/appointment_summary.css">
    <link rel="stylesheet" href="css/schedule.css">
    <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <title><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?> - Profile</title>
    <style>
        <?php
        if (!isset($_SESSION['Doctor_ID'])) {

            echo '.accept-button,
        .reject-button {
            display: none;
        }     ';
        }
        ?>#book-event {
            display: none;
        }

        #error-notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            width: 300px;
        }

        .error-card {
            background-color: #ff4d4d;
            color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            animation: slideIn 0.3s ease-out, fadeOut 0.5s ease-in forwards;
            animation-delay: 0s, 5s;
        }

        .error-card .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .error-card .close-btn:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
    </style>
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
    <main>

        <div class="profile-container">
            <div class="profile-header">
                <div id="profile-avatar">
                    <?php echo strtoupper(substr($user['First_Name'], 0, 1));
                    echo strtoupper(substr($user['Last_Name'], 0, 1)); ?>
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
        <?php
        if ($records) {
            echo '<div class="diagnosis-list">
            <h2>Diagnosis List</h2>';

            foreach ($records as $rec) {

                echo '<div class="diagnosis-item"> 
                <div class ="diagnosis-title " >Diagnosis: ' . $rec['Diagnosis'] . '</div>
                <div class ="diagnosis-title " >DiagnosisDate: ' . $rec['DiagnosisDate'] . '</div>
                <div class ="diagnosis-title " >Treatment: ' . $rec['Treatment'] . '</div>
                <div class ="diagnosis-title " >Notes: ' . $rec['Notes'] . '</div></div>';
            }
            echo '</div>';
        } else {
            echo '<div class="diagnosis-list">
            <h2>Diagnosis List</h2> <div class="diagnosis-item">You are healthy as a horse </div>';
        }
        ?>
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
                <div class="appointment-cards ">
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
        </div>
        <div class="calendar-wrapper">

            <div class="fixed-header">
                <h1 class="current-month"></h1>
                <div class="calendar" id="calendar-header"></div>

                <?php if ($is_doctor): ?>
                    <button id="add-event-btn"><i class="bx bx-plus"></i></button>
                <?php endif; ?>

            </div>
            <div class="calendar-container">
                <div class="calendar" id="calendar-body"></div>
            </div>

        </div>
        <?php if ($is_doctor): ?>
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
        <?php endif; ?>
    </main>


    <script>
        class ErrorNotification {
            static init() {
                // Create container if it doesn't exist
                if (!document.getElementById('error-notification-container')) {
                    const container = document.createElement('div');
                    container.id = 'error-notification-container';
                    document.body.appendChild(container);
                }
            }

            static show(message, type = 'error') {
                // Ensure container exists
                this.init();

                const container = document.getElementById('error-notification-container');

                // Create error card
                const errorCard = document.createElement('div');
                errorCard.className = `error-card ${type}-card`;

                // Create close button
                const closeBtn = document.createElement('button');
                closeBtn.className = 'close-btn';
                closeBtn.innerHTML = '&times;';
                closeBtn.addEventListener('click', () => {
                    errorCard.remove();
                });

                // Set card content
                errorCard.innerHTML = `
            <p>${message}</p>
        `;
                errorCard.appendChild(closeBtn);

                // Add to container
                container.appendChild(errorCard);

                // Automatically remove after animation
                setTimeout(() => {
                    if (errorCard.parentNode) {
                        errorCard.remove();
                    }
                }, 5500); // Slightly longer than CSS animation
            }

            static handleFetchErrors(response) {
                if (!response.ok) {
                    this.show(`HTTP error! status: ${response.status}`);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response;
            }
        }
    </script>
    <script>
        const eventsJson = <?php echo $eventsJson; ?>;
    </script>
    <script type="module" src="./js/schedule.js"> </script>
</body>

</html>