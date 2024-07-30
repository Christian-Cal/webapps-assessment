<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'];

$sql = 'SELECT * FROM posts WHERE id = ? AND user_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id, $user_id]);
$post = $stmt->fetch();

if (!$post) {
    echo 'Post not found or you do not have permission to edit this post.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $image = $post['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $sql = 'UPDATE posts SET content = ?, image = ? WHERE id = ? AND user_id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$content, $image, $post_id, $user_id]);

    header('Location: post.php');
}
?>

<?php include '../templates/header.php'; ?>

<h2>Edit Post</h2>
<form action="edit_post.php?id=<?php echo $post_id; ?>" method="post" enctype="multipart/form-data">
    <label for="content">Content:</label>
    <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    <label for="image">Image:</label>
    <input type="file" id="image" name="image">
    <button type="submit">Update Post</button>
</form>

<?php include '../templates/footer.php'; ?>
