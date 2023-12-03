<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id']) && isset($_POST['edited_content'])) {
    $edited_comment_id = $_POST['comment_id'];
    $edited_content = $_POST['edited_content'];
    $user_id = $_SESSION['user_id'];

    $conn = connectDB();

    // Check if the user is the author of the comment
    $check_author_stmt = $conn->prepare("SELECT * FROM Comments WHERE CommentID = ? AND UserID = ?");
    $check_author_stmt->bind_param("ii", $edited_comment_id, $user_id);
    $check_author_stmt->execute();
    $check_author_result = $check_author_stmt->get_result();

    if ($check_author_result->num_rows > 0) {
        // User is the author, update the comment
        $update_comment_stmt = $conn->prepare("UPDATE Comments SET Content = ? WHERE CommentID = ?");
        $update_comment_stmt->bind_param("si", $edited_content, $edited_comment_id);
        $update_comment_stmt->execute();

        echo "success";
    } else {
        // User is not the author, return an error
        echo "error";
    }

    $check_author_stmt->close();
    $conn->close();
} else {
    echo 'fail';
}
?>
