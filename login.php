<?php
session_start(); // Start a new session or resume the existing session
if(isset($_SESSION["username"])){
    header("Location: feed.html");
    exit(); // Add an exit here to prevent further execution
}
else{
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // Extract database credentials from the provided URL
        $dbHost = "us-cdbr-east-06.cleardb.net";
        $dbUser = "b7043b1d1bcfd0";
        $dbPass = "dfeaeec3";
        $dbName = "heroku_bdf8357b4c12d85";

        // Create a database connection
        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Handle login
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Validate user using prepared statements to prevent SQL injection
        $sql = "SELECT * FROM users WHERE username = '" . $username . "' AND password = '" . $password . "'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION["username"] = $username;
            $_SESSION["id"] = $row["id"];
            header("Location: feed.html");
        } 
        
        echo "<script>console.log('nespravne udaje');</script>";

        $stmt->close();
        $conn->close();
    }
    else{
        echo "<script>console.log('neni post');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lunar Chat</title>
    <link rel="manifest" href="site.webmanifest" />
    <link rel="stylesheet" href="global.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-rounded {
            font-variation-settings:
                'FILL' 1,
                'wght' 400,
                'GRAD' 0,
                'opsz' 48
        }
    </style>
</head>

<body>
    <form method="post" action="">
        <input name="username" type="text" placeholder="uzivatelske jmeno">
        <input name="password" type="password" placeholder="heslo">
        <button id="login">prihlasit</button>
    </form>
    <button onclick="logout()">odhlasit</button>
</body>
<script>
    function logout(){
        $.ajax({
                type: "POST",
                url: "api/logout.php",
                success: function (response) {
                    console.log(response);
                }
            });
    }

</script>

</html>