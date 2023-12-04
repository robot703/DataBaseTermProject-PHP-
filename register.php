<?php
session_start();

$conn = new mysqli("127.0.0.1", "root", "cho7031105*", "CommunityPlatform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$registration_error = "";

// 회원가입 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the UserID is already in use
    $check_stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
    $check_stmt->bind_param("s", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // UserID is already in use
        $registration_error = "이미 사용 중인 아이디입니다.";
    } else {
        // UserID is not in use, proceed with registration
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO Users (UserID, Username, Password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user_id, $username, $hashed_password);
        $stmt->execute();
        $stmt->close();

        // Redirect to login page or another appropriate page after successful registration
        header("Location: login.php");
        exit();
    }

    $check_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
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

        .error-message {
            color: #e74c3c;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Sign Up</h2>
    
    <!-- Display registration error message if there is one -->
    <?php if (!empty($registration_error)): ?>
        <p class="error-message"><?php echo $registration_error; ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="user_id">ID:</label>
        <input type="text" name="user_id" required>
        <label for="username">Name:</label>
        <input type="text" name="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <input type="submit" value="가입 완료">
        <a class="login-link" href="login.php">Login</a>
    </form>
    <!-- Link to the login page -->
    
</body>
</html>
