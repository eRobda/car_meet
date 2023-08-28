<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Extract database credentials from the provided URL
$dbHost = "us-cdbr-east-06.cleardb.net";
$dbUser = "b7043b1d1bcfd0";
$dbPass = "dfeaeec3";
$dbName = "heroku_bdf8357b4c12d85"; 

$otherUserId = strval($_GET["id"]);

// Create a database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

//DŮLEŽITÝ - když se smaže, dosere se asi 1000 věcí💀
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$localUserId = $_SESSION["id"];
$sql = 'SELECT * FROM messages WHERE (sender = ? AND reciver = ?) OR (sender = ? AND reciver = ?)';
$stmt = $conn->prepare($sql);

// Bind the variable to the prepared statement
$stmt->bind_param("ssss", $localUserId, $otherUserId, $otherUserId, $localUserId);
$stmt->execute();
$result = $stmt->get_result();

$messages = array();

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    header('Content-Type: application/json');
    $jsonResponse = json_encode($messages);
    echo $jsonResponse;
} else {
    echo "404";
}
$stmt->close();
$conn->close();
?>
