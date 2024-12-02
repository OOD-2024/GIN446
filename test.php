<?php
// Database connection configuration

try {
    // Create database connection
    require_once 'includes/dbh.inc.php';
    require_once 'includes/config_session.inc.php';
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdfFile"])) {
        $file = $_FILES["pdfFile"];
        $fileName = $file["name"];
        $fileTmpName = $file["tmp_name"];
        $fileError = $file["error"];
        $fileSize = $file["size"];

        // Validate file
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExt != "pdf") {
            throw new Exception("Only PDF files are allowed!");
        }

        if ($fileError !== 0) {
            throw new Exception("Error uploading file!");
        }

        // Set file size limit (e.g., 5MB)
        if ($fileSize > 5000000) {
            throw new Exception("File is too large! Maximum size is 5MB.");
        }

        // Create unique filename to prevent overwrites
        $newFileName = uniqid() . '_' . $fileName;
        $uploadPath = "pdfs/" . $newFileName;

        // Create directory if it doesn't exist
        if (!is_dir("pdfs")) {
            mkdir("pdfs", 0755, true);
        }

        // Move uploaded file to destination
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            // Prepare and execute database insert
            $sql = "INSERT INTO CV_PDF ( patient_id, filename, filepath, upload_date) VALUES (? , ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([$_SESSION['login_user_id'], $fileName, $uploadPath]);

            echo "File uploaded successfully and stored in database!";
        } else {
            throw new Exception("Failed to move uploaded file!");
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<html>

<head>
    <title>PDF Upload Form</title>
</head>

<body>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="pdfFile" accept=".pdf" required>
        <input type="submit" value="Upload PDF">
    </form>
</body>

</html>