<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $postID = $_POST['post_id'];
        $userID = $_SESSION['user_id'];
        $content = $_POST['comment_content'];

        $conn = new mysqli("127.0.0.1", "root", "cho7031105*", "CommunityPlatform");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Comments (PostID, UserID, Content) VALUES ('$postID', '$userID', '$content')";
        $result = $conn->query($sql);

        if ($result) {
            header("Location: post_detail.php?id={$postID}");
            exit();
        } else {
            echo "Error adding comment: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Login to add comments.";
    }
}
?>
