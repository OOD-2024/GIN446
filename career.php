<?php
require_once 'includes/config_session.inc.php';
require_once 'includes/dbh.inc.php';

try {
    // Create database connection
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch specialties from database
    $stmt = $pdo->prepare("SELECT speciality_id, speciality_Name FROM specialties ORDER BY speciality_Name");
    $stmt->execute();
    $specialties = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt = $pdo->prepare("Select * from requests where patient_id = :pid");
    $stmt->bindParam(":pid", $_SESSION['login_user_id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!isset($_SESSION['Patient_ID'])) {
        header('Location:404.php');
    } else {
        if ($result) {
            $_SESSION['applied'] = 'true';
            header('Location:index.php');
        }
    }
} catch (PDOException $e) {
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Select Specialty</title>
    <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f7fa;
        }

        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            color: #2d3748;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
        }

        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 1rem;
            color: #2d3748;
            background-color: #fff;
            cursor: pointer;
            transition: all 0.2s;
        }

        select:hover {
            border-color: #cbd5e0;
        }

        select:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #4299e1;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #3182ce;
        }

        button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.4);
        }

        .error-message {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .request-list {
            max-width: 800px;
            margin: 20px auto;
        }

        .request-item {
            border: 1px solid #ddd;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .request-details {
            margin-bottom: 10px;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .accept-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .reject-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .status-pending {
            color: #ff9800;
        }

        .status-accepted {
            color: #4CAF50;
        }

        .status-rejected {
            color: #f44336;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Select Medical Specialty</h2>
        <input type="hidden" name="id" value="<?php $_SESSION['Patient_ID'] ?>">
        <form action="includes/process_request.php" method="POST" id="specialtyForm">
            <div class="form-group">
                <label for="specialty">Choose a Specialty:</label>
                <select name="specialty" id="specialty" required>
                    <option value="">Select a specialty...</option>
                    <?php foreach ($specialties as $specialty): ?>
                        <option value="<?php echo htmlspecialchars($specialty['speciality_id']); ?>">
                            <?php echo htmlspecialchars($specialty['speciality_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label> Years of experience </label>
                <input type="number" name="experience">
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        document.getElementById('specialtyForm').addEventListener('submit', function(e) {
            const specialty = document.getElementById('specialty').value;
            if (!specialty) {
                e.preventDefault();
                alert('Please select a specialty');
            }
        });
    </script>
</body>

</html>