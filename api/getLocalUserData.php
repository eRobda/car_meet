<?php
session_start();
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
if (isset($_SESSION["id"])) {

    // SQL query with a placeholder
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);

    $id = $_SESSION["id"];
    // Bind the variable to the prepared statement
    $stmt->bind_param("i", $id);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            $data = array(
                "id" => $row["id"],
                "username" => $row["username"],
                "displayName" => $row["display_name"],
                "pfp" => $row["pfp"]
            );
            $json = json_encode($data);
            header("Content-Type: application/json");
            echo $json;
        }
    } else {
        echo "404";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
