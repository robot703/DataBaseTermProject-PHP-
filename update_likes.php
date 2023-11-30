<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_comment_id'])) {
    $liked_comment_id = $_POST['like_comment_id'];
    $user_id = $_SESSION['user_id'];

    $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user has already liked the comment
    $check_like_stmt = $conn->prepare("SELECT * FROM likes WHERE CommentID = ? AND UserID = ?");
    $check_like_stmt->bind_param("ii", $liked_comment_id, $user_id);
    $check_like_stmt->execute();
    $check_like_result = $check_like_stmt->get_result();

    if ($check_like_result->num_rows > 0) {
        // User already liked the comment, remove the like
        $remove_like_stmt = $conn->prepare("DELETE FROM likes WHERE CommentID = ? AND UserID = ?");
        $remove_like_stmt->bind_param("ii", $liked_comment_id, $user_id);
        $remove_like_stmt->execute();

        // Decrease the Likes count
        $likes_count = getLikesCount($conn, $liked_comment_id) - 1;
    } else {
        // User hasn't liked the comment, add the like
        $add_like_stmt = $conn->prepare("INSERT INTO likes (CommentID, UserID) VALUES (?, ?)");
        $add_like_stmt->bind_param("ii", $liked_comment_id, $user_id);
        $add_like_stmt->execute();

        // Increase the Likes count
        $likes_count = getLikesCount($conn, $liked_comment_id) + 1;
    }

    // Update the Likes column in the Comments table
    $update_likes_count_stmt = $conn->prepare("UPDATE Comments SET Likes = ? WHERE CommentID = ?");
    $update_likes_count_stmt->bind_param("ii", $likes_count, $liked_comment_id);
    $update_likes_count_stmt->execute();

    echo $check_like_result->num_rows > 0 ? "unliked|$likes_count" : "liked|$likes_count";

    $check_like_stmt->close();
    $conn->close();
} else {
    echo 'fail';
}

function getLikesCount($conn, $comment_id) {
    $get_likes_stmt = $conn->prepare("SELECT COUNT(*) as LikeCount FROM likes WHERE CommentID = ?");
    $get_likes_stmt->bind_param("i", $comment_id);
    $get_likes_stmt->execute();
    $get_likes_result = $get_likes_stmt->get_result();

    if ($get_likes_result->num_rows > 0) {
        $likes_row = $get_likes_result->fetch_assoc();
        return $likes_row['LikeCount'];
    }

    return 0;
}
?>
