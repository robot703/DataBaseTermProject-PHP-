<?php
session_start();

function connectDB()
{
    $conn = new mysqli("172.27.64.121:4567", "minjae", "1234", "CommunityPlatform");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

$loggedInUserID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_id'])) {
    $commentID = $_POST['comment_id'];

    $conn = connectDB();

    $sql = "SELECT Likes FROM Comments WHERE CommentID = $commentID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentLikes = $row['Likes'];

        $newLikes = ($currentLikes == 0) ? 1 : 0;

        $updateSQL = "UPDATE Comments SET Likes = $newLikes WHERE CommentID = $commentID";
        $conn->query($updateSQL);

    
            $checkLikeSQL = "SELECT * FROM likes WHERE CommentID = $commentID AND UserID = '$loggedInUserID'";
            $checkLikeResult = $conn->query($checkLikeSQL);

            if ($checkLikeResult->num_rows > 0) {
                $deleteLikeSQL = "DELETE FROM likes WHERE CommentID = $commentID AND UserID = '$loggedInUserID'";
                $conn->query($deleteLikeSQL);
            } else {
                $insertLikeSQL = "INSERT INTO likes (CommentID, UserID) VALUES ($commentID, '$loggedInUserID')";
                $conn->query($insertLikeSQL);
            }

            $conn->query($updateUserLikesSQL);
        }

        $conn->close();
    }

?>
