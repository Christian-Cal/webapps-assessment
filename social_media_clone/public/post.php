<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES['image']['name']);
        $image_path = '../uploads/' . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        $image = 'uploads/' . $image_name;
    }

    $sql = 'INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $title, $content, $image]);

    header('Location: post.php');
}

$sql = 'SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll();
?>

<?php include '../templates/header.php'; ?>

<h2>Create a Post</h2>
<form action="post.php" method="post" enctype="multipart/form-data">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required>
    <label for="content">Content:</label>
    <textarea id="content" name="content" required></textarea>
    <label for="image">Image:</label>
    <input type="file" id="image" name="image">
    <button type="submit">Post</button>
</form>

<h2>Your Posts</h2>
<?php foreach ($posts as $post): ?>
    <div class="post">
        <?php if ($post['image']): ?>
            <div class="image-container">
                <img src="../<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
            </div>
        <?php endif; ?>
        <div class="post-content">
            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        </div>
        <div class="post-actions">
            <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="edit-btn">Edit</a>
            <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="delete-btn">Delete</a>
        </div>
    </div>
<?php endforeach; ?>

<?php include '../templates/footer.php'; ?>
