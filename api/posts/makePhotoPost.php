<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["photo"])) {
    $uploadedFile = $_FILES["photo"];

    // Check for errors during upload
    if ($uploadedFile["error"] === UPLOAD_ERR_OK) {
        $originalFileName = $uploadedFile["name"];
        $tempFilePath = $uploadedFile["tmp_name"];
        
        // Generate a unique name for the file
        $uniqueFileName = uniqid() . '_' . $originalFileName;

        // Specify the directory to save the uploaded file
        $targetDirectory = "../../user-content/posts/";
        $targetPath = $targetDirectory . $uniqueFileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($tempFilePath, $targetPath)) {
            //upload ok
            // Extract database credentials from the provided URL
            $dbHost = "us-cdbr-east-06.cleardb.net";
            $dbUser = "b7043b1d1bcfd0";
            $dbPass = "dfeaeec3";
            $dbName = "heroku_bdf8357b4c12d85";

            session_start();
            // Create a database connection
            $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

            //DŮLEŽITÝ - když se smaže, dosere se asi 1000 věcí💀
            $conn->set_charset("utf8mb4");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Handle login
            $description = $_POST['description'];
            $localID = $_SESSION["id"];

            // SQL query with a placeholder
            $sql = "INSERT INTO posts (author, media, descriptiion) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // Bind the variable to the prepared statement
            $saveDir = "user-content/posts/" . $uniqueFileName;
            $stmt->bind_param("sss", $localID, $saveDir , $description);

            // Execute the query
            $stmt->execute();
            // Close the statement and connection
            $stmt->close();
            $conn->close();
            echo "upload ok";

        } else {
            echo "Error occurred while moving the file.";
        }
    } else {
        echo "Error during file upload: " . $uploadedFile["error"];
    }
}
?>



<?php

?>
