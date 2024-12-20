<?php
require_once 'includes/config_session.inc.php';

require_once 'includes/dbh.inc.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

if (isset($_SESSION['success'])) {
    echo '  <script>alert("Request was sent successfully.")</script>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['applied'])) {
    echo '<script>alert("Request was already made and is pending approval ") </script>';
    unset($_SESSION['applied']);
}
require_once 'includes/user_model.inc.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">

    <title>Clinic</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/layout.css">
    <style>
        .error-message {
            top: 0;

        }
    </style>
</head>

<body>

    <?php if (!isset($_SESSION['first_time'])): ?>
        <div id="disclaimer-popup">
            <div class="popup-content">
                <h2>Disclaimer</h2>
                <p>The information provided on this website is for educational purposes only. While we strive for
                    accuracy, this content is not intended to be used as professional advice.</p>
                <p>By proceeding, you acknowledge that you are using the information at your own discretion and risk.
                </p>
                <p>Please read and accept the disclaimer before proceeding.</p>
                <div class="popup-buttons">
                    <button id="accept-btn">I Understand </button>
                    <button id="decline-btn">Leave Website</button>

                    <?php $_SESSION['first_time'] = true; ?>

                    <script>
                        window.onload = function() {
                            document.getElementById('disclaimer-popup').style.display = 'block';

                            document.getElementById('accept-btn').onclick = function() {
                                document.getElementById('disclaimer-popup').style.display = 'none';
                                <?php $_SESSION['first_time'] = true; ?>
                            };

                            document.getElementById('decline-btn').onclick = function() {
                                alert('You need to accept the disclaimer to proceed.');
                                window.location.href = 'https://www.google.com';
                            };
                        };
                    </script>
                </div>
            </div>
        <?php endif; ?>
        </div>
        <nav>
            <div class="logo">Cinlic</div>
            <div class="nav-links">
                <a href="#" class="active">Home</a>
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

        <div class="hero">
            <div class="hero-content">
                <h1 class="hero-title">Need a <br> monthly <br>check up?</h1>
                <p class="hero-subtitle">We're here to help. Talk to someone today.</p>
            </div>
            <div class="illustration">
                <img src="public/DrawKit Vector Illustration Health & Medical (3).svg"></img>
            </div>
        </div>
        <footer>
            &copy; 2024 clinic.io. All rights reserved.
        </footer>


</body>


</html>
