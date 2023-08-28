<?php
// Extract database credentials from the provided URL
$dbHost = "us-cdbr-east-06.cleardb.net";
$dbUser = "b7043b1d1bcfd0";
$dbPass = "dfeaeec3";
$dbName = "heroku_bdf8357b4c12d85";

// Create a database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

//DŮLEŽITÝ - když se smaže, dosere se asi 1000 věcí💀
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if (isset($_POST['sender']) && isset($_POST['reciver']) && isset($_POST['content'])) {
    $sender = $_POST['sender'];
    $reciver = $_POST["reciver"];
    $content = $_POST["content"];

    // SQL query with a placeholder
    $sql = "INSERT INTO messages (sender, reciver, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind the variable to the prepared statement
    $stmt->bind_param("iis", $sender, $reciver, $content);

    // Execute the query
    $stmt->execute();
    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
