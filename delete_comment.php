<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    $deleted_comment_id = $_POST['comment_id'];
    $user_id = $_SESSION['user_id'];

    $conn = connectDB();

    // Check if the user is the author of the comment
    $check_author_stmt = $conn->prepare("SELECT * FROM Comments WHERE CommentID = ? AND UserID = ?");
    $check_author_stmt->bind_param("ii", $deleted_comment_id, $user_id);
    $check_author_stmt->execute();
    $check_author_result = $check_author_stmt->get_result();

    if ($check_author_result->num_rows > 0) {
        // User is the author, delete the comment
        $delete_comment_stmt = $conn->prepare("DELETE FROM Comments WHERE CommentID = ?");
        $delete_comment_stmt->bind_param("i", $deleted_comment_id);
        $delete_comment_stmt->execute();

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
