<?php
echo '<pre>';
print_r($_POST); // 또는 var_dump($_POST);
echo '</pre>';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    $deleted_comment_id = $_POST['comment_id'];
    $user_id = $_SESSION['user_id'];
    $conn = connectDB();

    $sql = "SELECT * FROM Comments WHERE CommentID = $deleted_comment_id AND UserID = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $CommentID = $row['CommentID'];

    if ($CommentID > 0) {
        $sql1 = "DELETE FROM Comments WHERE CommentID = $CommentID";
        $result1 = mysqli_query($conn, $sql1);

        if ($result1) {
            header('Location: post_detail.php');
            exit(); // Ensure that no other output is sent before header()
        } else {
            echo "error during delete";
        }
    } else {
        // User is not the author, return an error
        echo "error: Comment not found or user is not the author";
    }

    $conn->close();
} else {
    echo 'fail';
}

function connectDB()
{
    $conn = new mysqli("127.0.0.1", "root", "cho7031105*", "CommunityPlatform");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
