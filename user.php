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

        $userId = isset($_GET['id']) ? (int)$_GET['id'] : -1;

        require_once 'includes/user_model.inc.php';
        $user = getPatient_from_id($pdo, $userId);

        if (!$user) {
            header("Location: /404.php");
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
    $events = getAppointmentEvents($pdo, $userId);
    $eventsJson = json_encode($events);

    ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="/css/layout.css">
     <link rel="stylesheet" href="/css/user.css ">
     <link rel="stylesheet" href="/css/schedule.css">
     <link rel="shortcut icon" href="/public/favicon.png" type="image/x-icon">
     <title><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?> - Profile</title>

 </head>

 <body>
     <nav>
         <div class="logo">Clinic.io</div>
         <div class="nav-links">
             <a href="/">Home</a>
             <a href="/search.php">Services</a>
             <a href="/about.php">About</a>
         </div>
         <button class="register-btn">Register</button>
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
                     <p>Patient ID: <?php echo htmlspecialchars($userId); ?></p>
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
         </div>
     </main>

     <footer>
         &copy; 2024 clinic.io. All rights reserved.
     </footer>
     <script>
         const events = <?php echo $eventsJson; ?>;
         //  const events = [{
         //      name: "ECO350",
         //      days: [1, 5],
         //      startTime: "09:30",
         //      endTime: "10:45",
         //      location: "",
         //  }];
     </script>
     <script type="module" src="/js/schedule.js"> </script>
 </body>

 </html>