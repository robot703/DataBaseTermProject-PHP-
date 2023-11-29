<?php
session_start();

$conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_comment_id'])) {
    $liked_comment_id = $_POST['like_comment_id'];
    $user_id = $_SESSION['user_id'];

    // 사용자가 이미 댓글에 추천을 눌렀는지 확인
    $check_like_stmt = $conn->prepare("SELECT * FROM likes WHERE CommentID = ? AND UserID = ?");
    $check_like_stmt->bind_param("ii", $liked_comment_id, $user_id);
    $check_like_stmt->execute();
    $check_like_result = $check_like_stmt->get_result();

    if ($check_like_result->num_rows > 0) {
        // 사용자가 이미 댓글에 추천을 눌렀다면, 추천 취소
        $remove_like_stmt = $conn->prepare("DELETE FROM likes WHERE CommentID = ? AND UserID = ?");
        $remove_like_stmt->bind_param("ii", $liked_comment_id, $user_id);
        $remove_like_stmt->execute();
        echo 'unliked';
    } else {
        // 사용자가 댓글에 추천을 누르지 않았다면, 추천 추가
        $add_like_stmt = $conn->prepare("INSERT INTO likes (CommentID, UserID) VALUES (?, ?)");
        $add_like_stmt->bind_param("ii", $liked_comment_id, $user_id);
        $add_like_stmt->execute();
        echo 'liked';
    }

    $check_like_stmt->close();
    $conn->close();
} else {
    echo 'fail';
}
?>
