<?php
require_once 'includes/config_session.inc.php';
require_once 'includes/dbh.inc.php';
require_once 'includes/user_model.inc.php';

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

    $stmt = $pdo->prepare('select * from requests join specialties join patient where speciality = Speciality_id and patient_id=id and patient_id = :id;');
    $stmt->bindParam(":id", $_SESSION["login_user_id"]);
    $stmt->execute();
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
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
            background-color: #ffe8e6;
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
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #ff6b3b;
        }

        button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.4);
        }
        #back{
            margin: 1rem 0;
            background-color: white;
            border: 1.5px solid #ff6b6b;
        }
        #back a{
            color:#ff6b6b;
            text-decoration: none;
        }
        
        .error-message {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .request-list {
            max-width: 900px;
            margin: 20px auto;
        }

        .request-item {
            border: 1px solid #ddd;
            margin: 10px 0;
            padding: 15px;
            border-radius: 20px;
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
                echo " <button id='back'><a href='index.php'>Return to Home</a></button>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
                echo "</div>";



            } else { ?>
                <div class="form-container" id="form-container">
                    <h2>Apply to Join us as a Doctor</h2>
                    <input type="hidden" name="id" value="$_SESSION['Patient_ID']">
                    <form action="includes/process_request.php" method="POST" id="specialtyForm" enctype="multipart/form-data">
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
                            <label>Years of experience: </label>
                            <input type="number" name="experience" required>
                            <label>Upload your CV(PDF):</label>
                            <input type="file" name="pdfFile" accept=".pdf">
                        </div>
                        <button type="submit">Submit</button>
                        <button id="back"><a href="index.php">Return to Home</a></button>
                    </form>
                </div>
            </div>
        <?php }
    } ?>
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
