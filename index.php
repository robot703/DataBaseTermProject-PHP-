<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$menuLabel = 'Logout';
$menuLink = 'logout.php';
if (!isset($_SESSION['user_id'])) {
    $menuLabel = 'Login';
    $menuLink = 'login.php';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stackoverflow</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333; /* Set text color to black */
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
            position: relative;
        }

        .menu-bar {
            position: fixed;
            top: 0;
            left: -250px;
            /* Initially hidden off-screen */
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
            /* Add cursor style to indicate clickable */
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

        .post {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
        }

        .post h3 {
            color: #000; /* Set post title color to black */
        }

        .post .meta {
            color: #666; /* Set meta information color to a dark gray */
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="menu-bar" id="menuBar">
        <br>
        <a href="create_post.php">게시물 작성</a>
        <a href="<?php echo $menuLink; ?>"><?php echo $menuLabel; ?></a>
    </div>

    <div class="container">
        <div class="menu-toggle" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <h1>Stack overflow</h1>
        <p>Empowering the world to develop technology through collective knowledge.</p>

        <?php
        $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
        if ($conn->connect_error) {
            die("연결 실패: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM Posts";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h3><a href='post_detail.php?id={$row['PostID']}'>{$row['Title']}</a></h3>";
                echo "<p class='meta'>By {$row['UserID']} on {$row['CreatedAt']}</p>";
                echo "</div>";
            }
        } else {
            echo "게시물이 없습니다.";
        }

        $conn->close();
        ?>

        <!-- Additional scripts for menu toggle and logout function -->
        <script>
            function toggleMenu() {
                var menuBar = document.getElementById("menuBar");
                menuBar.style.left = menuBar.style.left === "0px" ? "-250px" : "0px";
                document.querySelector('.menu-toggle').classList.toggle('open');
            }

            function logout() {
                // You can redirect to the logout page or perform other logout actions here
                alert('Logout clicked!'); // Placeholder, replace with actual logout logic
                window.location.href = 'login.php';
            }
        </script>
    </div>
</body>

</html>
