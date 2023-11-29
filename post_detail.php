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
    // 로그인되어 있지 않은 경우, 로그인 페이지로 이동
    header("Location: login.php");
    exit();
}

// 게시물 조회
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // 조회수 증가
    $conn->query("UPDATE Posts SET Views = Views + 1 WHERE PostID = $post_id");

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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment_content'])) {
        $comment_content = $_POST['comment_content'];

        if (isset($_POST['parent_comment_id'])) {
            // 대댓글 작성
            $parent_comment_id = $_POST['parent_comment_id'];

            $comment_insert_stmt = $conn->prepare("INSERT INTO Comments (PostID, UserID, ParentCommentID, Content) VALUES (?, ?, ?, ?)");
            $comment_insert_stmt->bind_param("iiis", $post_id, $user_id, $parent_comment_id, $comment_content);
            $comment_insert_stmt->execute();
            $comment_insert_stmt->close();

            // 대댓글 작성 후, 현재 페이지 리로드 (새로운 대댓글이 표시되도록)
            header("Location: $_SERVER[PHP_SELF]?post_id=$post_id");
            exit();
        } else {
            // 일반 댓글 작성
            $comment_insert_stmt = $conn->prepare("INSERT INTO Comments (PostID, UserID, Content) VALUES (?, ?, ?)");
            $comment_insert_stmt->bind_param("iis", $post_id, $user_id, $comment_content);
            $comment_insert_stmt->execute();
            $comment_insert_stmt->close();

            // 댓글 작성 후, 현재 페이지 리로드 (새로운 댓글이 표시되도록)
            header("Location: $_SERVER[PHP_SELF]?post_id=$post_id");
            exit();
        }
    } elseif (isset($_POST['like_comment_id'])) {
        // 댓글 추천
        $liked_comment_id = $_POST['like_comment_id'];

        $like_comment_stmt = $conn->prepare("UPDATE Comments SET Likes = Likes + 1 WHERE CommentID = ?");
        $like_comment_stmt->bind_param("i", $liked_comment_id);
        $like_comment_stmt->execute();
        $like_comment_stmt->close();

        // 리로드 없이 추천 수 갱신 (JavaScript를 사용하면 더 나은 방법이 있습니다)
        header("Location: $_SERVER[PHP_SELF]?post_id=$post_id#comment-$liked_comment_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($post_row['Title']) ? $post_row['Title'] : '게시물 상세 페이지'; ?></title>
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

        .comment {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
        }

        .comment p {
            color: #666;
        }

        .comment small {
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
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($post_row)): ?>
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
                <p><a href="login.php">로그인</a> 후 댓글을 남기려면 <a href="login.php">여기를 클릭하세요</a></p>
            <?php endif; ?>

            <?php while ($comment_row = $comment_result->fetch_assoc()): ?>
                <div class='comment' id="comment-<?php echo $comment_row['CommentID']; ?>">
                    <p><?php echo $comment_row['Content']; ?></p>
                    <small>작성자: <?php echo $comment_row['Username']; ?>, 작성일: <?php echo $comment_row['CreatedAt']; ?>
                ,추천 수: <?php echo $comment_row['Likes']; ?></small>
                    
                    
                    <!-- 대댓글 작성 폼 -->
                    <form method="post" style="margin-top: 10px;">
                        <label for="comment_content">댓글 작성:</label>
                        <textarea name="comment_content" required></textarea>
                        <input type="hidden" name="parent_comment_id" value="<?php echo $comment_row['CommentID']; ?>">
                        <br>
                        <input type="submit" value="댓글 작성">
                    </form>
                    
                    <!-- 추가: 추천 버튼 -->
                    <button class="like-comment-btn" data-comment-id="<?php echo $comment_row['CommentID']; ?>">
                    <?php
                    // 사용자가 이미 댓글에 추천을 눌렀는지 확인
                    $check_like_stmt = $conn->prepare("SELECT * FROM Likes WHERE CommentID = ? AND UserID = ?");
                    $check_like_stmt->bind_param("ii", $comment_row['CommentID'], $user_id);
                    $check_like_stmt->execute();
                    $check_like_result = $check_like_stmt->get_result();

                    if ($check_like_result->num_rows > 0) {
                        echo '추천 취소';
                    } else {
                        echo '추천';
                    }
                    ?>
                </button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>댓글이 없습니다.</p>
        <?php endif; ?>
    </div>
    <script>
           document.addEventListener('DOMContentLoaded', function () {
            var likeButtons = document.querySelectorAll('.like-comment-btn');

            likeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var commentId = this.getAttribute('data-comment-id');
                    likeComment(commentId, this);
                });
            });

            function likeComment(commentId, button) {
                // AJAX to update likes
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_likes.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Handle the response
                        if (xhr.responseText === 'liked') {
                            // User liked the comment
                            alert('추천되었습니다.');
                            // Change button text to "추천 취소"
                            button.textContent = '추천 취소';
                        } else if (xhr.responseText === 'unliked') {
                            // User unliked the comment
                            alert('추천이 취소되었습니다.');
                            // Change button text to "추천"
                            button.textContent = '추천';
                        }

                        // 페이지 리로드
                        location.reload();
                    }
                };
                xhr.send('like_comment_id=' + commentId);
            }
        });


    </script>
</body>
</html>
