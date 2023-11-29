<?php
session_start();

$conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 로그인 확인
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // 사용자 이름으로 바꾸기
    $user_stmt = $conn->prepare("SELECT Username FROM Users WHERE UserID = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($user_result->num_rows > 0) {
        $user_row = $user_result->fetch_assoc();
        $username = $user_row['Username'];
    }
    $user_stmt->close();
} else {
    $user_id = null;
    $username = null;
}

// 게시물 조회
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // 게시물 정보 조회
    $post_stmt = $conn->prepare("SELECT Posts.*, Users.Username FROM Posts
                                LEFT JOIN Users ON Posts.UserID = Users.UserID
                                WHERE PostID = ?");
    $post_stmt->bind_param("i", $post_id);
    $post_stmt->execute();
    $post_result = $post_stmt->get_result();

    if ($post_result->num_rows > 0) {
        $post_row = $post_result->fetch_assoc();
    } else {
        // 게시물이 없을 경우 처리 (예: 404 페이지로 리다이렉션)
        header("Location: 404.php");
        exit();
    }

    // 댓글 조회
    $comment_stmt = $conn->prepare("SELECT Comments.*, Users.Username FROM Comments
                                    LEFT JOIN Users ON Comments.UserID = Users.UserID
                                    WHERE PostID = ?
                                    ORDER BY CreatedAt ASC");
    $comment_stmt->bind_param("i", $post_id);
    $comment_stmt->execute();
    $comment_result = $comment_stmt->get_result();
} else {
    // post_id가 없을 경우 처리 (예: 404 페이지로 리다이렉션)
    header("Location: 404.php");
    exit();
}

// 댓글 작성
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_content'])) {
    $comment_content = $_POST['comment_content'];

    if ($user_id) {
        $comment_insert_stmt = $conn->prepare("INSERT INTO Comments (PostID, UserID, Content) VALUES (?, ?, ?)");
        $comment_insert_stmt->bind_param("iis", $post_id, $user_id, $comment_content);
        $comment_insert_stmt->execute();
        $comment_insert_stmt->close();

        // 댓글 작성 후, 현재 페이지 리로드 (새로운 댓글이 표시되도록)
        header("Location: $_SERVER[PHP_SELF]?post_id=$post_id");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post_row['Title']; ?> - 게시물 상세 페이지</title>
    <!-- 이전과 동일한 스타일 코드 유지 -->
</head>
<body>
    <div class="container">
        <h1><?php echo $post_row['Title']; ?></h1>
        <p><?php echo $post_row['Content']; ?></p>
        <small>작성자: <?php echo $post_row['Username']; ?>, 작성일: <?php echo $post_row['CreatedAt']; ?></small>

        <?php if ($user_id): ?>
            <!-- 사용자가 로그인한 경우 -->
            <div class="user-info">
                <p>안녕하세요, <?php echo $username; ?>님! <a class="logout-btn" href="logout.php">로그아웃</a></p>
            </div>
            <form method="post">
                <label for="comment_content">댓글 작성:</label>
                <textarea name="comment_content" required></textarea>
                <br>
                <input type="submit" value="댓글 작성">
            </form>
        <?php else: ?>
            <!-- 사용자가 로그인하지 않은 경우 -->
            <p><a href="login.php">로그인</a> | <a href="register.php">회원가입</a></p>
        <?php endif; ?>

        <?php
        if ($comment_result->num_rows > 0) {
            while ($comment_row = $comment_result->fetch_assoc()) {
                echo "<div class='comment'>";
                echo "<p>{$comment_row['Content']}</p>";
                echo "<small>작성자: {$comment_row['Username']}, 작성일: {$comment_row['CreatedAt']}</small>";
                echo "</div>";
            }
        } else {
            echo "<p>댓글이 없습니다.</p>";
        }
        ?>
    </div>
</body>
</html>
