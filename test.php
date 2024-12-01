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
    <title>Document</title>
    <style>
        .calendar-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .calendar-navigation button {
            background-color: transparent;
            color: #333333;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .calendar-navigation button:hover {
            color: #ff9999
        }

        .calendar-navigation button i {
            font-size: 1.2rem;
        }
    </style>
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


    <script type="module" src="/js/schedule1.js"> </script>
    <script type="module" src="/js/events.js"> </script>
</body>

</html>