<?php
session_start();

$conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 회원가입 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("INSERT INTO Users (UserID, Username, Password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_id, $username, $password);
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
        <label for="user_id">아이디:</label>
        <input type="text" name="user_id" required>
        <br>
        <label for="username">이름:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">비밀번호:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" value="가입 완료">
    </form>
</body>
</html>

