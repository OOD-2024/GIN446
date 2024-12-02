<?php
require_once("dbh.inc.php");
require_once("config_session.inc.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $speciality = $_POST['specialty'];
    $eperience = $_POST['experience'];
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("select * from patient where id =:id");
        $stmt->bindParam(":id", $_SESSION['login_user_id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        $fullname = $result['First_Name'] . ' ' . $result['Last_Name'];


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
                mkdir("pdfs", 0777, true);
            }

            // Move uploaded file to destination
            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                $sql = "INSERT INTO CV_PDF ( patient_id, filename, filepath, upload_date) VALUES (? , ?, ?, NOW())";
                $stmt = $pdo->prepare($sql);

                $stmt->execute([$_SESSION['login_user_id'], $fileName, 'includes/' . $uploadPath]);


                $stmt = $pdo->prepare('insert into requests(patient_id , fullname , speciality , experience ) values(:id , :name , :speciality ,:experience )');
                $stmt->bindParam(':id', $_SESSION['login_user_id']);
                $stmt->bindParam(':name', $fullname);
                $stmt->bindParam(':speciality', $speciality);
                $stmt->bindParam(':experience', $eperience);

                $stmt->execute();

                // Prepare and execute database insert

                echo "File uploaded successfully and stored in database!";
            } else {
                throw new Exception("Failed to move uploaded file!");
            }
        }


        header("Location:../user.php");

    } catch (PDOException $e) {
        echo $e->getMessage();
    }

}