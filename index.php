<!DOCTYPE html>
<lang="en">
    <?php
    require_once 'includes/config_session.inc.php';
    print_r($_SESSION);



    ?>



    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">

        <title>Clinic</title>

        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/layout.css">

    </head>

    <body>


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

                    <script>
                        <?php if (!isset($_SESSION['first_time'])) {
                            $_SESSION['first_time'] = true; ?>

                            window.onload = function () {
                                document.getElementById('disclaimer-popup').style.display = 'block';
                            };

                            document.getElementById('accept-btn').onclick = function () {
                                document.getElementById('disclaimer-popup').style.display = 'none';
                                localStorage.setItem('disclaimeraccepted', 'true');
                            };

                            document.getElementById('decline-btn').onclick = function () {
                                alert('You need to accept the disclaimer to proceed.');
                                window.location.href = 'https://www.google.com';
                            };
                        <?php } else { ?>
                            document.getElementById('disclaimer-popup').style.display = 'none';
                        <?php } ?>
                    </script>
                </div>
            </div>
        </div>
        <nav>
            <div class="logo">Cinlic</div>
            <div class="nav-links">
                <a href="#">Home</a>
                <a href="search.php">Services</a>
                <a href="about.php">About</a>
            </div>
            <?php
            if (!isset($_SESSION['user_session_id'])) {
                echo '
            <a href="signin_up.php"><button class="register-btn">Register</button></a>';
=======
            if (!isset($_SESSION['login_user_id'])) {
                echo '<a href="signin_up.php"><button class="register-btn">Register</button></a>';

            } else {
                echo '<a href="user.php"><button class="register-btn">Profile</button></a>
                     <a href="logout.php"><button class="register-btn">Logout</button></a>';
            }


            ?>



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

        <script>
            function check_session_id() {
                var session_id = '<?php echo $_SESSION["user_session_id"] ?>';
                fetch('includes/check_login.inc.php').then(function (response) {
                    return response.json();

                }).then(function (responseData) {
                    if (responseData.output == 'logout') {
                        alert("A Login from other device was detected");
                        window.location.href = 'logout.php';
                    }
                })
            }
            setInterval(function () {
                check_session_id();

            }, 1000)
        </script>

    </body>


    </html>