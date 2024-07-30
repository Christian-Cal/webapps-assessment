<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $tag_name = $_POST['tag'];

    $sql = 'SELECT id FROM tags WHERE name = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tag_name]);
    $tag = $stmt->fetch();

    if (!$tag) {
        $sql = 'INSERT INTO tags (name) VALUES (?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tag_name]);
        $tag_id = $pdo->lastInsertId();
    } else {
        $tag_id = $tag['id'];
    }

    $sql = 'INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $tag_id]);

    header('Location: post.php');
}
?>

<form action="add_tag.php" method="post">
    <input type="hidden" name="post_id" value="<?php echo $_GET['post_id']; ?>">
    <label for="tag">Tag:</label>
    <input type="text" id="tag" name="tag" required>
    <button type="submit">Add Tag</button>
</form>
