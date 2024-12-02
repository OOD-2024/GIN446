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

    $userId = isset($_SESSION['login_user_id']) ? $_SESSION['login_user_id'] : -1;
    // $userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($userId === false || $userId === null) {
        header('HTTP/1.1 403 Forbidden');
        exit('Invalid user ID');
    }
    echo $userId;

    if (!isset($_SESSION['login_user_id'])) {
        http_response_code(403);
        die();
    }
    $userId = $_SESSION['login_user_id'];
    $is_doctor = isset($_SESSION['Doctor_ID']);
    // $userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    // if ($userId === false || $userId === null) {
    //     header('HTTP/1.1 403 Forbidden');
    //     exit('Invalid user ID');
    // }
    // echo $userId;

    require_once 'includes/user_model.inc.php';
    $user = getPatient_from_id($pdo, $userId);
    $locations = getDoctorLocations($pdo, $userId);

    if (!$user) {
        http_response_code(403);
        exit();
    }


    $stmt = $pdo->prepare("SELECT speciality_id, speciality_Name FROM specialties ORDER BY speciality_Name");
    $stmt->execute();
    $specialties = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt = $pdo->prepare("Select * from requests where patient_id = :pid");
    $stmt->bindParam(":pid", $_SESSION['login_user_id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('select * from requests join specialties join patient where speciality = Speciality_id and patient_id=id and patient_id = :id;');
    $stmt->bindParam(":id", $_SESSION["login_user_id"]);
    $stmt->execute();
    $request = $stmt->fetch(PDO::FETCH_ASSOC);






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
print_r($_SESSION)
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

    <title><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?> - Profile</title>

</head>

<body>
    <nav>
        <div class="logo">Cinlic</div>
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
            <h2>Diagnosis List</h2> <div class="diagnosis-item">You are healthy as a horse </div>'
            ;
        }
        ?>
        </div>
        <?php if (!isset($_SESSION['Doctor_ID'])) { ?>
            <div>
                <?php if ($result) {
                    echo '              
                <div class="request-display" id="request-display">';

                    $statusClass = "status-" . strtolower($request['status']);
                    echo "<div class='request-item'>";
                    echo "<div class='request-details'>";
                    echo "<h3>Request #" . htmlspecialchars($request['requestid']) . "</h3>";
                    echo "<p><strong>Name:</strong> " . htmlspecialchars($request['fullname']) . "</p>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($request['Email']) . "</p>";
                    echo "<p><strong>Phone Number:</strong> " . htmlspecialchars($request['phoneNum']) . "</p>";
                    echo "<p><strong>Speciality:</strong> " . htmlspecialchars($request['speciality_Name']) . "</p>";
                    echo "<p><strong>Experience:</strong> " . htmlspecialchars($request['experience']) . " years</p>";
                    echo "<p><strong>Status:</strong> <span class='" . $statusClass . "'>" . htmlspecialchars($request['status']) . "</span></p>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";



                } else { ?>
                    <div class="form-container" id="form-container">
                        <h2>Apply to join us as a doctor</h2>
                        <input type="hidden" name="id" value="$_SESSION['Patient_ID']">
                        <form action="includes/process_request.php" method="POST" id="specialtyForm"
                            enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="specialty">Choose a Specialty:</label>
                                <select name="specialty" id="specialty" required>
                                    <option value="">Select a specialty...</option>
                                    <?php foreach ($specialties as $specialty): ?>
                                        <option value="<?php echo htmlspecialchars($specialty["speciality_id"]); ?>">
                                            <?php echo htmlspecialchars($specialty["speciality_Name"]); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label> Years of experience </label>
                                <input type="number" name="experience" required>
                                Upload your CV(PDF).
                                <input type="file" name="pdfFile" accept=".pdf" required>
                            </div>
                            <button type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            <?php }
        } ?>


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



    <footer>
        &copy; 2024 clinic.io. All rights reserved.
    </footer>
    <script type="module" src="js/schedule.js"> </script>
    <script>
        const eventsJson = <?php echo $eventsJson; ?>;

        document.getElementById('specialtyForm').addEventListener('submit', function (e) {
            const specialty = document.getElementById('specialty').value;
            if (!specialty) {
                e.preventDefault();
                alert('Please select a specialty');
            }
        });
    </script>

    =======
    console.log(eventsJson);
    </script>
    <script type="module" src="./js/schedule.js"> </script>

</body>

</html>