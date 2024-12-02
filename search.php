<?php
require_once 'includes/dbh.inc.php';
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $specialty_query = "SELECT speciality_Name FROM specialties ORDER BY speciality_Name";
    $stmt = $pdo->prepare($specialty_query);
    $stmt->execute();
    $specialties = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$where_conditions = [];
$params = [];

if (!empty($_GET['name'])) {
    $name = $_GET['name'];
    $where_conditions[] = "(p.First_Name LIKE :name OR p.Last_Name LIKE :name2)";
    $params[':name'] = "%$name%";
    $params[':name2'] = "%$name%";
}

if (!empty($_GET['specialty'])) {
    $specialty = $_GET['specialty'];
    $where_conditions[] = "s.Specialty_Name LIKE :specialty";
    $params[':specialty'] = "%$specialty%";
}

if (!empty($_GET['country'])) {
    $country = $_GET['country'];
    $where_conditions[] = "l.Country LIKE :country";
    $params[':country'] = "%$country%";
}

if (!empty($_GET['city'])) {
    $city = $_GET['city'];
    $where_conditions[] = "l.City LIKE :city";
    $params[':city'] = "%$city%";
}
require_once 'includes/search_model.inc.php';

$results = findDoctors($pdo, $params, $where_conditions);

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">
    <title>Find a Doctor - Clinic.io</title>

</head>

<body>
    <nav>
        <div class="logo">Cinlic</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="#">Services</a>
            <a href="about.php">About</a>
        </div>
        <button class="register-btn">Register</button>
    </nav>

    <main>
        <div class="search-container">
            <h1 style="margin-bottom: 1.5rem; color: #111827; font-size: 1.5rem; font-weight: 600;">Find a Doctor</h1>
            <form method="GET">
                <div class="search-grid">
                    <div class="search-field">
                        <label>Doctor Name</label>
                        <input type="text" name="name" value="<?= h($_GET['name'] ?? '') ?>">
                    </div>

                    <div class="search-field">
                        <label>Specialty</label>
                        <div class="custom-select">
                            <input type="text" name="specialty" id="specialty-input"
                                value="<?= h($_GET['specialty'] ?? '') ?>"
                                autocomplete="off">
                            <div id="specialty-dropdown" class="dropdown-list">
                                <?php foreach ($specialties as $specialty): ?>
                                    <div class="dropdown-item" data-value="<?= h($specialty) ?>">
                                        <?= h($specialty) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="search-field">
                        <label>Country</label>
                        <input type="text" name="country" value="<?= h($_GET['country'] ?? '') ?>">
                    </div>
                    <div class="search-field">
                        <label>City</label>
                        <input type="text" name="city" value="<?= h($_GET['city'] ?? '') ?>">
                    </div>
                </div>
                <div class="button-group">
                    <button type="submit" class="search-btn">Search</button>
                    <a href="<?= h($_SERVER['PHP_SELF']) ?>" class="reset-btn" style="text-decoration: none; ">Reset</a>

                </div>
            </form>
        </div>

        <table class="results-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Specialties</th>
                    <th>Locations</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= h($row['First_Name'] . ' ' . $row['Last_Name']) ?></td>
                            <td><?= h($row['Specialties'] ?? 'Not specified') ?></td>
                            <td><?= h($row['Locations'] ?? 'Not specified') ?></td>
                            <td>
                                <a href="appointment.php?doctor_id=<?= h($row['ID']) ?>" class="book-btn">
                                    Book Appointment
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-results">
                            No doctors found matching your criteria
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer>
        &copy; 2024 clinic.io. All rights reserved.
    </footer>
    <script type="module" src="js/search.js"></script>
</body>

</html>