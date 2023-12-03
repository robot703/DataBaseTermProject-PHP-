<?php
session_start();

// 사용자가 로그인하지 않은 경우 로그인 페이지로 리디렉션
function connectDB()
{
    $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// 세션에서 로그인한 사용자 ID를 검색
$loggedInUserID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_id'])) {
    $commentID = $_POST['comment_id'];

    // 여기에서 데이터베이스 업데이트 수행
    $conn = connectDB();

    // 현재 좋아요 상태를 가져옴
    $sql = "SELECT Likes FROM Comments WHERE CommentID = $commentID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentLikes = $row['Likes'];

        // 좋아요 토글
        $newLikes = ($currentLikes == 0) ? 1 : 0;

        // 좋아요 개수 업데이트
        $updateSQL = "UPDATE Comments SET Likes = $newLikes WHERE CommentID = $commentID";
        $conn->query($updateSQL);

    
            // 사용자가 이미 댓글을 좋아했는지 확인
            $checkLikeSQL = "SELECT * FROM likes WHERE CommentID = $commentID AND UserID = '$loggedInUserID'";
            $checkLikeResult = $conn->query($checkLikeSQL);

            if ($checkLikeResult->num_rows > 0) {
                // 사용자가 이미 댓글을 좋아했으므로 '좋아요 취소'
                $deleteLikeSQL = "DELETE FROM likes WHERE CommentID = $commentID AND UserID = '$loggedInUserID'";
                $conn->query($deleteLikeSQL);
            } else {
                // 사용자가 댓글을 좋아하지 않았으므로 '좋아요 등록'
                $insertLikeSQL = "INSERT INTO likes (CommentID, UserID) VALUES ($commentID, '$loggedInUserID')";
                $conn->query($insertLikeSQL);
            }

            $conn->query($updateUserLikesSQL);
        }

        $conn->close();
    }

?>
