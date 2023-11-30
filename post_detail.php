<?php
session_start();

if (isset($_GET['id'])) {
    $postID = $_GET['id'];

    $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM Posts WHERE PostID = $postID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();

        echo "<div class='container'>";
        echo "<h1>{$post['Title']}</h1>";
        echo "<p>{$post['Content']}</p>";

        echo "<h2>Comments</h2>";

        $sqlComments = "SELECT * FROM Comments WHERE PostID = $postID";
        $resultComments = $conn->query($sqlComments);

        if ($resultComments->num_rows > 0) {
            echo "<ul class='comments'>";
            while ($comment = $resultComments->fetch_assoc()) {
                echo "<li>";
                echo "<p>{$comment['Content']}</p>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No comments yet.</p>";
        }

        if (isset($_SESSION['user_id'])) {
            echo "<form method='post' action='add_comment.php'>";
            echo "<input type='hidden' name='post_id' value='{$postID}'>";
            echo "<textarea name='comment_content' placeholder='Add a comment'></textarea>";
            echo "<input type='submit' value='Add Comment'>";
            echo "</form>";
        } else {
            echo "<p>Login to add comments.</p>";
        }

        echo "</div>";
    } else {
        echo "Post not found.";
    }

    $conn->close();
} else {
    echo "Invalid post ID.";
}
?>
