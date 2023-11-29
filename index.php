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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .post {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
        }

        .post h3 {
            color: #333;
        }

        .post p {
            color: #666;
        }

        .post small {
            color: #999;
        }

        .user-info {
            margin-bottom: 10px;
        }

        .user-info a {
            margin-right: 10px;
            color: #333;
            text-decoration: none;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        .edit-delete {
            margin-top: 10px;
        }

        .edit-delete a {
            margin-right: 10px;
            color: #3498db;
            text-decoration: none;
        }

        .edit-delete a:hover {
            text-decoration: underline;
        }
        </style>
</head>
<body>
    <div class="container">
        <h1>지역 커뮤니티</h1>
        <p>여기에 지역 이벤트, 서비스, 토론 등을 공유하세요!</p>

        <?php if ($user_id): ?>
            <!-- 사용자가 로그인한 경우 -->
            <div class="user-info">
                <p>안녕하세요, <?php echo $username; ?>님! <a class="logout-btn" href="logout.php">로그아웃</a></p>
            </div>
            <form method="post">
                <label for="title">제목:</label>
                <input type="text" name="title" required>
                <br>
                <label for="content">내용:</label>
                <textarea name="content" required></textarea>
                <br>
                <input type="submit" value="게시글 작성">
            </form>
        <?php else: ?>
            <!-- 사용자가 로그인하지 않은 경우 -->
            <p><a href="login.php">로그인</a> | <a href="register.php">회원가입</a></p>
        <?php endif; ?>

        <?php
        // $result가 null이 아닌 경우에만 처리
        if ($result !== null && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h3>{$row['Title']}</h3>";
                echo "<p>{$row['Content']}</p>";
                echo "<small>작성자: {$row['Username']}, 작성일: {$row['CreatedAt']}</small>";

                // 게시글 수정 및 삭제
                if ($user_id && $user_id === $row['UserID']) {
                    echo "<div class='edit-delete'>";
                    // 게시물 수정 기능 추가
                    echo "<a href='edit_post.php?post_id={$row['PostID']}'>수정</a>";
                    echo " | ";
                    // 게시물 삭제 기능 추가
                    echo "<a href='delete_post.php?post_id={$row['PostID']}'>삭제</a>";
                    echo "</div>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>게시글이 없습니다.</p>";
        }
        ?>
    </div>
</body>
</html>