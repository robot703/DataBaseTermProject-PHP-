<?php
session_start();

$conn = new mysqli("127.0.0.1", "root", "cho7031105*", "CommunityPlatform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$login_error = ""; // Variable to store login error message

// 로그인 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashed_password = $user['Password'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user['UserID'];
            $log = "INSERT INTO LoginLogs (UserID) value ('$user_id');";
            $result1 = $conn->query($log);
            $row = $result->fetch_assoc();
            header("Location: index.php");
            exit();
        } else {
            $login_error = "로그인에 실패했습니다. 다시 확인해주세요.";
        }
    } else {
        $login_error = "로그인에 실패했습니다. 다시 확인해주세요.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
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

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: #e74c3c;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Login</h2>
    
    <!-- Display login error message if there is one -->
    <?php if (!empty($login_error)): ?>
        <p class="error-message"><?php echo $login_error; ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="user_id">ID:</label> <!-- Update input name -->
        <input type="text" name="user_id" required> <!-- Update input name -->
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <input type="submit" value="로그인">
        <a class="login-link" href="register.php">SignUp</a>
    </form>
</body>
</html>
