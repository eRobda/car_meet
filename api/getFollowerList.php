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

// Create a database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

//DŮLEŽITÝ - když se smaže, dosere se asi 1000 věcí💀
$conn->set_charset("utf8mb4");



if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//ziskani id prihlaseneho uzivatele - musi se predelat <- pomalé
$localUserId = 0;
$username = $_SESSION["username"];
$sql = 'SELECT id FROM users WHERE username = "' . $username . '"';
$result = $conn->query($sql);
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $localUserId = $row["id"];
    }
}

// ziska list IDček všch lidi, se kterejma se uživatel sleduje
$sql = "SELECT * FROM follower_list WHERE user1 = $localUserId OR user2 = $localUserId";
$result = $conn->query($sql);

$followedUsers = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Determine which user is the follower (user1 or user2)
        $followerId = ($row['user1'] == $localUserId) ? $row['user2'] : $row['user1'];
        $followedUsers[] = $followerId;
    }
}


if (!empty($followedUsers)) {
    $followedUserIds = implode(',', $followedUsers);

    $sql = "SELECT id, username, display_name, pfp FROM users WHERE id IN ($followedUserIds)";
    $result = $conn->query($sql);

    $followedUsersDetails = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $followedUsersDetails[] = $row;
        }
    }

    //header('Content-Type: application/json');
    $jsonResponse = json_encode($followedUsersDetails);
    echo $jsonResponse;
} else {
    echo "No followed users found.";
}

$conn->close();
?>
