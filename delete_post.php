<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $postID = $_GET['id'];

        // Check if the user is the owner of the post
        $userID = $_SESSION['user_id'];
        $conn = new mysqli("localhost", "root", "cho7031105*", "CommunityPlatform");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $getPostOwnerID = "SELECT UserID FROM Posts WHERE PostID = '$postID'";
        $result = $conn->query($getPostOwnerID);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $postOwnerID = $row['UserID'];

            if ($userID === $postOwnerID) {
                // User is the owner; delete related comments first
                $deleteCommentsQuery = "DELETE FROM comments WHERE PostID = '$postID'";
                if ($conn->query($deleteCommentsQuery) === TRUE) {
                    // Comments deleted; now delete the post
                    $deletePostQuery = "DELETE FROM Posts WHERE PostID = '$postID'";
                    if ($conn->query($deletePostQuery) === TRUE) {
                        header("Location: index.php"); // Redirect to the main page after deletion
                        exit();
                    } else {
                        $error = "Error deleting post: " . $conn->error;
                    }
                } else {
                    $error = "Error deleting comments: " . $conn->error;
                }
            } else {
                $error = "Permission denied. You don't have the right to delete this post.";
            }
        } else {
            $error = "Error retrieving post information: " . $conn->error;
        }

        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<!-- Head section remains unchanged -->

<body>
    <div class="container">
        <!-- Your existing HTML structure -->

        <?php
        if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
        ?>
    </div>
</body>

</html>
