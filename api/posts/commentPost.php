<?php
// Extract database credentials from the provided URL
$dbHost = "us-cdbr-east-06.cleardb.net";
$dbUser = "b7043b1d1bcfd0";
$dbPass = "dfeaeec3";
$dbName = "heroku_bdf8357b4c12d85";
session_start();

if(!isset($_SESSION["id"])){
    echo "no id in session";
    header("Locastion: login.php");
}

// Create a database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

//DŮLEŽITÝ - když se smaže, dosere se asi 1000 věcí💀
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if (isset($_POST['post_id']) && isset($_POST["content"])) {
    $postId = $_POST["post_id"];
    $content = $_POST["content"];
    $localUserId = $_SESSION["id"];

    // SQL query with a placeholder
    $sql = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind the variable to the prepared statement
    $stmt->bind_param("sss", $postId, $localUserId, $content);

    // Execute the query
    $stmt->execute();
    // Close the statement and connection
    $stmt->close();
    $conn->close();
    echo "commented post successfully";
}
else{
    echo "no specified post";
}
?>
