<?php
session_start();

// Check if the user is not logged in, redirect to login page


$menuLabel = 'Logout';
$menuLink = 'logout.php';

// Check if the user is logged in to toggle between login and logout
if (!isset($_SESSION['user_id'])) {
    $menuLabel = 'Login';
    $menuLink = 'login.php';
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postID = $_POST['post_id'];

    // Check if the current user has the permission to delete the post
    $userID = $_SESSION['user_id'];
    $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the user ID of the post owner
    $getPostOwnerID = "SELECT UserID FROM Posts WHERE PostID = '$postID'";
    $result = $conn->query($getPostOwnerID);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $postOwnerID = $row['UserID'];

        // Check if the current user is the owner of the post
        if ($userID === $postOwnerID) {
            // Delete the post
            $deletePostQuery = "DELETE FROM Posts WHERE PostID = '$postID'";
            if ($conn->query($deletePostQuery) === TRUE) {
                header("Location: index.php"); // Redirect to the main page after deletion
                exit();
            } else {
                echo "Error deleting post: " . $conn->error;
            }
        } else {
            $error = "Permission denied. You don't have the right to delete this post.";
        }
    } else {
        echo "Error retrieving post information: " . $conn->error;
    }

    $conn->close();
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
            z-index: 1;
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
            z-index: 100;
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
        form {
            margin-top: 20px;
            display: flex;
            align-items: center;
        }

        label {
            margin-right: 10px;
        }

        input[type="text"],
        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #555;
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

        <form method="GET" action="">
            <label for="searchLanguage">검색:</label>
            <input type="text" name="searchLanguage" id="searchLanguage" placeholder="코드 언어를 입력하세요">

            <label for="selectLanguage">언어 선택:</label>
            <select name="selectLanguage" id="selectLanguage">
                <option value="">전체</option>
                <option value="php">PHP</option>
                <option value="html">HTML</option>
                <option value="css">CSS</option>
                <option value="javascript">JavaScript</option>
                <option value="python">Python</option>
                <option value="java">Java</option>
                <option value="csharp">C#</option>
                <option value="c">C</option>
                <option value="cpp">C++</option>
                <option value="ruby">Ruby</option>
                <option value="swift">Swift</option>
                <option value="go">Go</option>
                <option value="typescript">TypeScript</option>
                <option value="rust">Rust</option>
                <option value="kotlin">Kotlin</option>
                <option value="dart">Dart</option>
                <option value="scala">Scala</option>
                <option value="r">R</option>
                <option value="shell">Shell</option>
                <option value="sql">SQL</option>
                <option value="perl">Perl</option>
                <option value="objective-c">Objective-C</option>
                <option value="matlab">MATLAB</option>
                <option value="groovy">Groovy</option>
                <option value="lua">Lua</option>
                <option value="haskell">Haskell</option>
                <option value="elixir">Elixir</option>
                <option value="dart">Dart</option>
                <option value="powershell">PowerShell</option>
                <option value="coffeescript">CoffeeScript</option>
                <option value="vbnet">VB.NET</option>
                <option value="jsx">JSX</option>
                <option value="tsx">TSX</option>
                <option value="graphql">GraphQL</option>
                <option value="bash">Bash</option>
                <option value="perl">Perl</option>
                <option value="vue">Vue.js</option>
                <option value="angular">Angular</option>
                <option value="react">React.js</option>
                <option value="d3">D3.js</option>
                <option value="svelte">Svelte</option>
                <option value="flutter">Flutter</option>
                <option value="assembly">Assembly</option>
                <option value="cobol">COBOL</option>
                <option value="forth">Forth</option>
                <!-- 다른 코드 언어도 추가하세요 -->
            </select>
            
            <input type="submit" value="검색">
        </form>

        <?php
        // 검색 입력에 기반하여 SQL 쿼리 수정
        $searchLanguage = isset($_GET['searchLanguage']) ? $_GET['searchLanguage'] : '';
        $selectLanguage = isset($_GET['selectLanguage']) ? $_GET['selectLanguage'] : '';

        $sql = "SELECT * FROM Posts";
        $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
        if ($conn->connect_error) {
            die("연결 실패: " . $conn->connect_error);
        }
        
        $sql = "SELECT * FROM Posts";
        // 언어 선택이 있으면 WHERE 절에 추가
        if (!empty($selectLanguage)) {
            $sql .= " WHERE CodeLanguage = '$selectLanguage'";
        }

        // 검색어가 있으면 WHERE 절에 추가
        if (!empty($searchLanguage)) {
            $sql .= empty($selectLanguage) ? " WHERE" : " AND";
            $sql .= " CodeLanguage LIKE '%$searchLanguage%'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Inside the while loop where you display posts
            while ($row = $result->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h3><a href='post_detail.php?id={$row['PostID']}'>{$row['Title']}</a></h3>";
                echo "<p class='meta'>#{$row['CodeLanguage']} By {$row['UserID']} on {$row['CreatedAt']}</p>";
            
                // Add links for modification and deletion
                echo "<p><a href='modify_post.php?id={$row['PostID']}'>수정</a> | <a href='#' onclick='confirmDelete({$row['PostID']})'>삭제</a></p>";
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

            function confirmDelete(postID) {
                var confirmation = confirm("Are you sure you want to delete this post?");
                if (confirmation) {
                    // If user confirms, proceed with deletion
                    deletePost(postID);
                }
            }

            function deletePost(postID) {
                // You can also use AJAX to send an asynchronous request to delete the post on the server
                // For simplicity, I'll just redirect to delete_post.php with the postID as a parameter
                window.location.href = 'delete_post.php?id=' + postID;
            }
        </script>
    </div>
</body>

</html>
