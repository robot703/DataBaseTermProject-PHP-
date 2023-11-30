<?php
session_start();

function connectDB()
{
    $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

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

    $conn->close();
} else {
    echo "Invalid post ID.";
    exit();
}
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
    </style>
</head>
<body>

    <!-- Your existing toggleMenu function -->
    <script>
        function toggleMenu() {
            var menuBar = document.querySelector('.menu-bar');
            menuBar.style.left = (menuBar.style.left === '-250px') ? '0' : '-250px';
        }

        // Your existing toggleLike function
        function toggleLike(likeButton) {
            var commentId = likeButton.getAttribute('data-comment-id');

            // Ajax 요청을 통해 서버에 좋아요 토글 요청
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // 서버 응답 처리
                    var response = xhr.responseText;
                    var likeCountSpan = document.getElementById('likeCount' + commentId);

                    if (response.includes('liked')) {
                        likeButton.innerText = '좋아요 취소';
                        likeCountSpan.innerText = parseInt(likeCountSpan.innerText) + 1 + ' Likes';
                    } else if (response.includes('unliked')) {
                        likeButton.innerText = 'Like';
                        likeCountSpan.innerText = parseInt(likeCountSpan.innerText) - 1 + ' Likes';
                    }
                }
            };
            xhr.open('POST', 'update_likes.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send('like_comment_id=' + commentId);
            
        }
    </script>

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

        <!-- Display post details -->
        <h1><?php echo $post['Title']; ?></h1>
        <p><?php echo $post['Content']; ?></p>

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
        <script>
            function validateComment() {
                var commentContent = document.getElementsByName('comment_content')[0].value.trim();
                if (commentContent === '') {
                    alert('Please enter a comment.');
                    return false;
                }
                return true;
            }
        </script>

        <?php if ($resultComments->num_rows > 0) : ?>
            <!-- Display comments -->
            <ul class="comments">
                <?php while ($comment = $resultComments->fetch_assoc()) : ?>
                    <li class="comment">
                        <div class="comment-content"><?php echo $comment['Content']; ?></div>
                        <div class="comment-meta">
                            <p>Author: <?php echo $comment['UserID']; ?></p>
                            <p>Created at: <?php echo $comment['CreatedAt']; ?></p>
                            <!-- Like button with data-comment-id attribute -->
                            <button class="like-button" data-comment-id="<?php echo $comment['CommentID']; ?>" onclick="toggleLike(this)">Like</button>
                            <!-- Display current likes count for the comment -->
                            <span id="likeCount<?php echo $comment['CommentID']; ?>"><?php echo $comment['Likes']; ?> Likes</span>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>

</body>
</html>
