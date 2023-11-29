<?php
session_start();

$conn = new mysqli("localhost", "root", "", "CommunityPlatform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE Posts SET Title = ?, Content = ? WHERE PostID = ?");
    $stmt->bind_param("ssi", $title, $content, $post_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    $stmt = $conn->prepare("SELECT * FROM Posts WHERE PostID = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        // 존재하지 않는 게시글
        header("Location: index.php");
        exit();
    }
} else {
    // post_id가 전달되지 않은 경우
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 수정</title>
</head>
<body>
    <h2>게시글 수정</h2>
    <form method="post">
        <input type="hidden" name="post_id" value="<?php echo $post['PostID']; ?>">
        <label for="title">제목:</label>
        <input type="text" name="title" value="<?php echo $post['Title']; ?>" required>
        <br>
        <label for="content">내용:</label>
        <textarea name="content" required><?php echo $post['Content']; ?></textarea>
        <br>
        <input type="submit" value="수정 완료">
    </form>
</body>
</html>