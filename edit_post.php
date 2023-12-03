<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postID = $_POST['post_id'];
    $password = $_POST['password'];

    // Validate password (replace 'your_password_hash' with the actual hashed password)
    $hashedPassword = password_hash('your_password', PASSWORD_DEFAULT);
    
    if (password_verify($password, $hashedPassword)) {
        // Password is correct, perform modification logic
        // ...

        header("Location: index.php"); // Redirect to the main page after modification
        exit();
    } else {
        $error = "Invalid password";
    }
}

// Display the modification form
?>

<!DOCTYPE html>
<html lang="en">

<!-- Head section remains unchanged -->

<body>
    <div class="container">
        <!-- Your existing HTML structure -->

        <!-- Modification form -->
        <form method="POST" action="">
            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <input type="hidden" name="post_id" value="<?php echo $_GET['id']; ?>">

            <input type="submit" value="수정">
        </form>

        <?php
        if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
        ?>
    </div>
</body>

</html>
