<?php
session_start();

$conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 로그인 확인
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = null;
}

// 게시글 작성
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    if ($user_id) {
        $stmt = $conn->prepare("INSERT INTO Posts (UserID, Title, Content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);
        $stmt->execute();
        $stmt->close();
    }
}

$sql = "SELECT Posts.*, Users.Username FROM Posts
        LEFT JOIN Users ON Posts.UserID = Users.UserID
        ORDER BY CreatedAt DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>지역 커뮤니티</title>
</head>
<body>
    <h1>지역 커뮤니티</h1>
    <p>여기에 지역 이벤트, 서비스, 토론 등을 공유하세요!</p>

    <?php if ($user_id): ?>
        <form method="post">
            <label for="title">제목:</label>
            <input type="text" name="title" required>
            <br>
            <label for="content">내용:</label>
            <textarea name="content" required></textarea>
            <br>
            <input type="submit" value="게시글 작성">
        </form>
    <?php endif; ?>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<h3>{$row['Title']}</h3>";
            echo "<p>{$row['Content']}</p>";
            echo "<small>작성자: {$row['Username']}, 작성일: {$row['CreatedAt']}</small>";

            // 게시글 수정 및 삭제
            if ($user_id && $user_id === $row['UserID']) {
                echo "<br>";
                echo "<a href='edit_post.php?post_id={$row['PostID']}'>수정</a>";
                echo " | ";
                echo "<a href='delete_post.php?post_id={$row['PostID']}'>삭제</a>";
            }

            echo "</div>";
        }
    } else {
        echo "게시글이 없습니다.";
    }

    $conn->close();
    ?>

</body>
</html>