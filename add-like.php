<?php
session_start();

if (isset($_POST['comment_id']) && isset($_SESSION['user_id'])) {
    $commentID = $_POST['comment_id'];
    $userID = $_SESSION['user_id'];

    $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user has already liked the comment
    $checkQuery = "SELECT * FROM likes WHERE CommentID = '$commentID' AND UserID = '$userID'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows === 0) {
        // If not liked, insert a new like
        $insertQuery = "INSERT INTO likes (CommentID, UserID) VALUES ('$commentID', '$userID')";
        $result = $conn->query($insertQuery);

        if ($result) {
            // Update the comment's like count
            $updateQuery = "UPDATE Comments SET Likes = Likes + 1 WHERE CommentID = '$commentID'";
            $conn->query($updateQuery);

            echo "success";
        } else {
            echo "error";
        }
    } else {
        // If already liked, remove the like
        $deleteQuery = "DELETE FROM likes WHERE CommentID = '$commentID' AND UserID = '$userID'";
        $result = $conn->query($deleteQuery);

        if ($result) {
            // Update the comment's like count
            $updateQuery = "UPDATE Comments SET Likes = Likes - 1 WHERE CommentID = '$commentID'";
            $conn->query($updateQuery);

            echo "unliked";
        } else {
            echo "error";
        }
    }

    $conn->close();
} else {
    echo "error";
}
?>
