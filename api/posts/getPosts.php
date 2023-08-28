<?php
// Database connection details
$host = "us-cdbr-east-06.cleardb.net";
$username = "b7043b1d1bcfd0";
$password = "dfeaeec3";
$database = "heroku_bdf8357b4c12d85";
session_start();
$localUserId = $_SESSION["id"];

// Number of posts per page
$postsPerPage = 5;

// Get the current page number from the request
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the starting offset for the query
$offset = ($page - 1) * $postsPerPage;

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);
//DŮLEŽITÝ - když se smaže, dosere se asi 1000 věcí💀
$conn->set_charset("utf8mb4");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve posts with user details and likes
$query = "
    SELECT 
        p.id AS post_id, 
        p.media, 
        p.time, 
        p.descriptiion,
        u.username AS author_username,
        u.display_name AS author_display_name,
        u.pfp AS author_pfp,
        COUNT(l.id) AS like_count,
        CASE WHEN l.user_id = $localUserId THEN 'true' ELSE 'false' END AS user_liked
    FROM posts p
    JOIN users u ON p.author = u.id
    LEFT JOIN likes l ON p.id = l.post_id
    GROUP BY p.id
    ORDER BY p.time DESC
    LIMIT $offset, $postsPerPage;
";

$result = $conn->query($query);

if ($result) {
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $post = [
            "post_id" => $row["post_id"],
            "media" => $row["media"],
            "time" => $row["time"],
            "author_username" => $row["author_username"],
            "author_display_name" => $row["author_display_name"],
            "author_pfp" => $row["author_pfp"],
            "like_count" => $row["like_count"],
            "desc" => $row["descriptiion"],
            "user_liked" => $row["user_liked"],
            "comments" => []
        ];

        // Query to retrieve comments for the current post
        $commentsQuery = "
            SELECT
                c.user_id,
                u.username,
                u.pfp,
                c.content
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = " . $row["post_id"] . "
            ORDER BY c.id DESC;
        ";

        $commentsResult = $conn->query($commentsQuery);
        if ($commentsResult) {
            while ($commentRow = $commentsResult->fetch_assoc()) {
                $comment = [
                    "user_id" => $commentRow["user_id"],
                    "username" => $commentRow["username"],
                    "pfp" => $commentRow["pfp"],
                    "content" => $commentRow["content"]
                ];
                $post["comments"][] = $comment;
            }
        }

        $posts[] = $post;
    }

    // Return JSON response
    header("Content-Type: application/json");
    echo json_encode($posts);
} else {
    echo "Error executing query: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
