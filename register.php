<?php
session_start();

$conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 회원가입 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("INSERT INTO Users (Username, Password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
</head>
<body>
    <h2>회원가입</h2>
    <form method="post">
        <label for="username">사용자명:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">비밀번호:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" value="가입 완료">
    </form>
</body>
</html>
