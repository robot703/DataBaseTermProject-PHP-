<?php
session_start();

if (isset($_POST['comment_id']) && isset($_SESSION['user_id'])) {
    $commentID = $_POST['comment_id'];
    $userID = $_SESSION['user_id'];

    $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $insertQuery = "INSERT INTO likes (CommentID, UserID) VALUES ('$commentID', '$userID')";
    $result = $conn->query($insertQuery);

    if ($result) {
        echo "success";
    } else {
        echo "error";
    }

    $conn->close();
} else {
    echo "error";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Detail - Stackoverflow</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
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
        }

        .comment-content {
            color: #333;
            margin: 0;
        }

        .comment-meta {
            color: #666;
            font-size: 14px;
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
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .comment-form input[type="submit"]:hover {
            background-color: #2c64b7;
        }

        .like-button {
            background-color: #4285f4;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
        }

        .like-button:hover {
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
    </style>
</head>

<body>
    <?php
    session_start();

    if (isset($_GET['id'])) {
        $postID = $_GET['id'];

        $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM Posts WHERE PostID = $postID";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $post = $result->fetch_assoc();

            echo "<div class='container'>";
            echo "<div class='menu-toggle' onclick='toggleMenu()'>";
            echo "<span></span>";
            echo "<span></span>";
            echo "<span></span>";
            echo "</div>";
            echo "<div class='menu-bar'>";
            echo "<p></p>";
            echo "<a href='index.php'>Home</a>";
            echo "</div>";

            echo "<h1>{$post['Title']}</h1>";
            echo "<p>{$post['Content']}</p>";

            echo "<h2>Comments</h2>";

            $sqlComments = "SELECT * FROM Comments WHERE PostID = $postID";
            $resultComments = $conn->query($sqlComments);

            if ($resultComments->num_rows > 0) {
                echo "<ul class='comments'>";
                while ($comment = $resultComments->fetch_assoc()) {
                    echo "<li class='comment'>";
                    echo "<div class='comment-content'>{$comment['Content']}</div>";
                    echo "<div class='comment-meta'>";
                    echo "<p>댓글 작성자: {$comment['UserID']}</p>";
                    echo "<p>작성일: {$comment['CreatedAt']}</p>";
                    echo "<button class='like-button' onclick='toggleLike(this)'>좋아요</button>";
                    echo "</div>";
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>댓글이 아직 없습니다.</p>";
            }
            
            
            

            if (isset($_SESSION['user_id'])) {
                echo "<form class='comment-form' method='post' action='add_comment.php'>";
                echo "<input type='hidden' name='post_id' value='{$postID}'>";
                echo "<textarea name='comment_content' placeholder='Add a comment'></textarea>";
                echo "<input type='submit' value='Add Comment'>";
                echo "&nbsp;&nbsp;";
                echo "<button class='like-button' onclick='toggleLike()'>Like</button>";
                echo "</form>";

               
            } else {
                echo "<p>Login to add comments and like the post.</p>";
            }

            echo "</div>";
        } else {
            echo "Post not found.";
        }

        $conn->close();
    } else {
        echo "Invalid post ID.";
    }
    ?>

    <script>
        function toggleLike() {
            alert('Like button clicked!'); // Replace with actual like logic
        }

        function toggleMenu() {
            var menuBar = document.querySelector('.menu-bar');
            menuBar.style.left = menuBar.style.left === "0px" ? "-250px" : "0px";
            document.querySelector('.menu-toggle').classList.toggle('open');
        }
        function toggleLike(button) {
        var currentState = button.dataset.state;
        var newState = currentState === 'on' ? 'off' : 'on';

        // 여기에서 좋아요 상태를 서버에 전송하는 로직을 추가할 수 있습니다.

        button.dataset.state = newState;
        button.textContent = newState === 'on' ? '좋아요 취소' : '좋아요';
    }
    </script>
</body>

</html>
