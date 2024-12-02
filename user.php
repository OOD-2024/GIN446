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
    require_once 'includes/user_model.inc.php';
    $user = getPatient_from_id($pdo, $userId);
    $locations = getDoctorLocations($pdo, $userId);

    if (!$user) {
        header("404.php");
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


    if (!isset($_SESSION['Patient_ID'])) {
        header('Location:404.php');
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
            padding-left: 25%;
            font-family: Arial, sans-serif;
            height: auto;
            overflow: auto;


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

        .accept-button {
            display: none;
        }

        .request-display {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 700px;
        }

        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        select,
        input[type="number"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }

        button[type="submit"] {
            background-color: #4a90e2;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s;
        }

        button[type="submit"]:hover {
            background-color: #357abd;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        input:invalid {
            border-color: #dc3545;
        }

        .request-display {
            max-width: 800px;
            margin: 24px auto;
            padding: 20px;
        }

        /* Request Item Card */
        .request-item {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 20px;

            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .request-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }


        .request-details h3 {
            color: #2d3748;
            font-size: 1.25rem;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f7f7f7;
        }

        .request-details p {
            margin: 12px 0;
            color: #4a5568;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .request-details strong {
            color: #2d3748;
            font-weight: 600;
            min-width: 120px;
            display: inline-block;
        }

        /* Status Styles */
        [class^="status-"] {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background-color: #fff8e6;
            color: #b7791f;
        }

        .status-approved {
            background-color: #e6ffee;
            color: #047857;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }

        input[type="file"] {
            /* Reset default styles */
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;

            /* Container styling */
            display: inline-block;
            padding: 12px 20px;
            background-color: #f5f5f5;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;

            /* Text styling */
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;

            /* Hover state */
            transition: all 0.3s ease;
        }

        input[type="file"]:hover {
            background-color: #eee;
            border-color: #999;
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

</body>

</html>