<?php
session_start();

// 사용자가 로그인한 상태인지 확인
if (!isset($_SESSION['user_id'])) {
    // 로그인되어 있지 않다면 로그인 페이지로 리다이렉트
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 폼 데이터 처리
    $title = $_POST["title"];
    $content = $_POST["content"];
    $codeLanguage = $_POST["code_language"]; // Added code_language

    $userID = $_SESSION["user_id"];

    $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
    if ($conn->connect_error) {
        die("연결 실패: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO Posts (UserID, Title, Content, CodeLanguage) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $userID, $title, $content, $codeLanguage);

    if ($stmt->execute()) {
        // 게시물이 성공적으로 작성되었을 때 JavaScript로 팝업 창 띄우고 index.php로 이동
        echo "<script>alert('게시물이 성공적으로 작성되었습니다!'); window.location.href = 'index.php';</script>";
    } else {
        // 게시물 작성 중 오류가 발생했을 때 JavaScript로 팝업 창 띄우기
        echo "<script>alert('게시물 작성 중 오류가 발생했습니다: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }

        h2 {
            color: #333;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        textarea {
            height: 100px;
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
    </style>
</head>
<body>
    <!-- 게시물 생성을 위한 양식 추가 -->
    <div class="container">
        <!-- 기존의 HTML 코드 -->

        <form action="create_post.php" method="POST">
            <label for="title">제목:</label>
            <input type="text" name="title" required>

            <label for="code_language">코드 언어:</label>
            <select name="code_language" required>
            <option value="php">PHP</option>
            <option value="html">HTML</option>
            <option value="css">CSS</option>
            <option value="javascript">JavaScript</option>
            <option value="python">Python</option>
            <option value="java">Java</option>
            <option value="csharp">C#</option>
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
                <!-- Add more options for other code languages as needed -->
            </select>

            <label for="content">내용:</label>
            <textarea name="content" rows="4" required></textarea>

            <input type="submit" value="게시물 작성">
        </form>
    </div>
</body>
</html>
