<?php
session_start();

$conn = new mysqli("localhost", "root", "", "CommunityPlatform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    $stmt = $conn->prepare("DELETE FROM Posts WHERE PostID = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

header("Location: index.php");
?>
