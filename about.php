<?php
require_once 'includes/config_session.inc.php';
require_once 'includes/user_model.inc.php';
require_once 'includes/dbh.inc.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">
    <title>About</title>

</head>

<body>
    <nav>
        <div class="logo">Cinlic</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="search.php">Services</a>
            <a href="#" class="active">About</a>
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

    <main class="about-container">
        <!-- Project Introduction Section -->
        <section class="about-section">
            <h1>About Clinic.io</h1>
            <div class="section-content">
                <h2>Our Vision</h2>
                <p>Clinic.io is revolutionizing healthcare accessibility by providing a seamless platform where anyone,
                    regardless of their age or location, can connect with healthcare professionals. Our platform enables
                    users to search for doctors, book appointments, and complete payments online, all in one place.</p>
                <p>We believe that quality healthcare should be easily accessible to everyone, and our platform is
                    designed to bridge the gap between patients and healthcare providers through technology.</p>
            </div>
        </section>

        <!-- Team Section -->
        <section class="about-section">
            <h2>Meet Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <h3>Anthony Abisaid</h3>
                    <!-- <p>Full Stack Developer</p> -->
                </div>
                <div class="team-member">
                    <h3>Fares Maady</h3>
                    <!-- <p>Frontend Developer</p> -->
                </div>
                <div class="team-member">
                    <h3>Chadi Awar</h3>
                    <!-- <p>Backend Developer</p> -->
                </div>
            </div>
        </section>

        <!-- Academic Project Section -->
        <section class="about-section">
            <h2>Academic Project</h2>
            <div class="section-content">
                <p>This website was developed as part of the <strong>GIN 446 - Web Development</strong> course under the
                    supervision of <strong>Dr. Pascal Damien</strong>. It represents our commitment to learning and
                    applying modern web development technologies in creating practical solutions for real-world
                    problems.</p>
            </div>
        </section>

        <!-- Disclaimer Section -->
        <section class="about-section disclaimer">
            <h2>Disclaimer</h2>
            <div class="section-content">
                <p>This website was created for educational purposes only. While we strive to provide accurate
                    information, this platform is not intended for actual medical use. The appointments, doctors, and
                    services listed are simulated for demonstration purposes.</p>
                <p>For real medical assistance, please consult with licensed healthcare providers and use officially
                    recognized healthcare platforms.</p>
            </div>
        </section>
    </main>

    <footer>
        &copy; 2024 clinic.io. All rights reserved.
    </footer>


</body>

</html>