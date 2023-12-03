<?php
session_start();

// Check if the user is not logged in, redirect to login page
function connectDB()
{
    $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Function to delete a comment by comment ID
function deleteComment($commentID)
{
    $conn = connectDB();
    $commentID = $conn->real_escape_string($commentID);

    // Perform the deletion
    $deleteSQL = "DELETE FROM Comments WHERE CommentID = '$commentID'";
    $result = $conn->query($deleteSQL);

    // Debugging statements
    if ($result) {
        echo "Comment deleted successfully.";
    } else {
        echo "Error deleting comment: " . $conn->error;
    }

    $conn->close();

    return $result;
}
// Include any necessary database connection code here
if (isset($_GET['id'])) {
    $postID = $_GET['id'];
    $conn = connectDB();

    $sql = "SELECT * FROM Posts WHERE PostID = $postID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        echo "Post not found.";
        exit();
    }

    $sqlComments = "SELECT * FROM Comments WHERE PostID = $postID";
    $resultComments = $conn->query($sqlComments);

    // Check if the form is submitted for comment deletion
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_comment'])) {
        $commentIDToDelete = $_POST['delete_comment'];
        $success = deleteComment($commentIDToDelete);

        if ($success) {
            echo "Comment deleted successfully.";
            // Redirect to the current page to refresh the comments
            header("Location: {$_SERVER['PHP_SELF']}?id=$postID");
            exit();
        } else {
            echo "Error deleting comment.";
        }
    }

    $conn->close();
} else {
    echo "Invalid post ID.";
    exit();
}


// Retrieve the PostID from the URL
// Retrieve the PostID from the URL
$postID = isset($_GET['id']) ? $_GET['id'] : null;

// Check if the PostID is valid
if (!$postID) {
    echo "Invalid Post ID";
    exit();
}

// Include any necessary database connection code here
$conn = connectDB();

// Fetch the post details from the database
$sql = "SELECT * FROM Posts WHERE PostID = '$postID'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $title = $row['Title'];
    $content = $row['Content'];
    $codeLanguage = $row['CodeLanguage'];
    $userID = $row['UserID'];
    $createdAt = $row['CreatedAt'];

} else {
    echo "Post not found";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post['Title']; ?> - CommunityPlatform</title>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
         #post-details {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
        }

        #post-details h2 {
            color: #4285f4;
            margin-bottom: 10px;
        }

        #post-details p {
            color: #333;
            margin-bottom: 10px;
        }
       body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        h1 {
            color: #4285f4;
            margin-bottom: 10px;
        }

        p {
            color: #333;
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
            margin-top: 20px;
        }

        .comments {
            list-style: none;
            padding: 0;
        }

        .comment {
            border-bottom: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .comment-content {
            color: #333;
            margin: 0;
            flex-grow: 1;
        }

        .comment-meta {
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .comment-meta p {
            margin: 0;
            margin-right: 10px;
        }

        .comment-form {
            margin-top: 20px;
        }

        .comment-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .comment-form input[type="submit"] {
            background-color: #4285f4;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .comment-form input[type="submit"]:hover {
            background-color: #2c64b7;
        }

        .menu-bar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100%;
            background-color: #333;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            transition: left 0.3s ease;
        }

        .menu-bar a {
            display: block;
            padding: 15px;
            color: #fff;
            text-decoration: none;
            border-bottom: 1px solid #555;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .menu-bar a:hover {
            background-color: #555;
        }

        .menu-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            cursor: pointer;
            z-index: 2;
        }

        .menu-toggle span {
            display: block;
            height: 2px;
            width: 25px;
            background-color: #333;
            margin-bottom: 6px;
            transition: 0.3s;
        }

        .menu-toggle span:nth-child(2) {
            width: 18px;
        }

        .menu-toggle.open span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .menu-toggle.open span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.open span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }
        .like-button {
            background-color: rgb(0,0,0);
            color: white;
            border: none;
            padding: 7px 14px; /* 내부 여백 조절 */
            text-align: center;
            text-decoration: none;
            font-size: 14px; /* 글꼴 크기 조절 */
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
        }

        .like-button:hover {
            background-color: #2c64b7;
        }
        
        /* Add this style for the edit and delete buttons */
        .edit-delete-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
    <body>
<div class="container">
        <div class="menu-toggle" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <!-- Your menu bar -->
        <div class="menu-bar">
            <p></p>
            <a href="index.php">Home</a>
        </div>
        <div id="post-details">
            <h2><?php echo $title; ?></h2>
            <p>Language: <?php echo $codeLanguage; ?></p>
            <p>Posted by <?php echo $userID; ?> on <?php echo $createdAt; ?></p>
            <p><?php echo $content; ?></p>
        </div>
     

        <!-- Display comments -->
        <h2>Comments</h2>

        <?php if (isset($_SESSION['user_id'])) : ?>
            <!-- Comment form for logged-in users -->
            <form class="comment-form" method="post" action="add_comment.php">
                <input type="hidden" name="post_id" value="<?php echo $postID; ?>">
                <textarea name="comment_content" placeholder="Add a comment"></textarea>
                <input type="submit" value="Add Comment" onclick="return validateComment()">
            </form>
        <?php else : ?>
            <p>Login to add comments and like the post.</p>
        <?php endif; ?>

        <!-- JavaScript function for comment validation -->
        

        <?php if ($resultComments->num_rows > 0) : ?>
            <!-- Display comments -->
            <ul class="comments">
        <?php while ($comment = $resultComments->fetch_assoc()) : ?>
            <li class="comment">
                <div class="comment-content"><?php echo $comment['Content']; ?></div>
                <div class="comment-meta">
                    <p>작성자: <?php echo $comment['UserID']; ?></p>
                    <p>작성일: <?php echo $comment['CreatedAt']; ?></p>
                    <!-- 댓글 좋아요 버튼(data-comment-id 속성 사용) -->
                    <button class="like-button" data-comment-id="<?php echo $comment['CommentID']; ?>" onclick="toggleLike(this)">좋아요</button>
                    <!-- 편집 및 삭제 버튼 -->
                    <div id="edit-buttons">
                        <form action="edit_comment.php" method="post">
                            <button type="submit" value=<?php echo $comment['CommentID']; ?>>편집</button>
                        </form>
                    </div>
                    <div id="delete-buttons">
                        <!-- JavaScript를 사용하여 deleteComment 함수 호출 -->
                        <button onclick="deleteComment(<?php echo $comment['CommentID']; ?>)">삭제</button>
                    </div>
                    <!-- 댓글에 대한 현재 좋아요 횟수 표시 -->
                    <span id="likeCount<?php echo $comment['CommentID']; ?>"><?php echo $comment['Likes']; ?> 좋아요</span>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
        <?php endif; ?>
    </div>
    <script>
        function toggleMenu() {
            var menuBar = document.querySelector('.menu-bar');
            menuBar.style.left = (menuBar.style.left === '-250px') ? '0' : '-250px';
        }
        // 댓글 삭제를 위한 JavaScript 함수
        function deleteComment(commentID) {
            var confirmation = confirm("이 댓글을 삭제하시겠습니까?");
            
            if (confirmation) {
                // AJAX를 사용하여 비동기식 요청으로 댓글 삭제 처리
                $.ajax({
                    type: 'POST',
                    url: 'post_detail.php?id=<?php echo $postID; ?>', // 현재 게시물 ID 지정
                    data: { delete_comment: commentID },
                    success: function(response) {
                        // 댓글 삭제 후 페이지를 새로고침하여 댓글을 업데이트
                        location.reload();
                    },
                    error: function(error) {
                        console.log('댓글 삭제 중 오류 발생: ' + error);
                    }
                });
            }
        }
    </script>
</body>
</html>