<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="/css/schedule.css">
    <link rel="stylesheet" href="/css/doctor.css">
    <link rel="shortcut icon" href="/public/favicon.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

</head>

<body>
    <nav>
        <div class="logo">Clinic.io</div>
        <div class="nav-links">
            <a href="/">Home</a>
            <a href="/appointment.php">Services</a>
            <a href="/about.php">About</a>
        </div>
        <button class="register-btn">Log In</button>
    </nav>
    <div class="calendar-wrapper">

        <div class="fixed-header">
            <h1 class="current-month"></h1>
            <div class="calendar" id="calendar-header"></div>
            <button id="add-event-btn"><i class="bx bx-plus"></i></button>
            <!-- <button id="theme-toggle">
                <i class="bx bx-sun"></i>
            </button> -->
        </div>
        <div class="calendar-container">
            <div class="calendar" id="calendar-body"></div>
        </div>

    </div>
    <!-- <footer>
        &copy; 2024 clinic.io. All rights reserved.
    </footer> -->
    <script type="module" src="/js/schedule.js"></script>
</body>

</html>