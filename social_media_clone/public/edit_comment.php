<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$comment_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = 'SELECT * FROM comments WHERE id = ? AND user_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$comment_id, $user_id]);
$comment = $stmt->fetch();

if (!$comment) {
    echo 'Comment not found or you do not have permission to edit this comment.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updated_comment = $_POST['comment'];
    $sql = 'UPDATE comments SET comment = ? WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$updated_comment, $comment_id]);

    header('Location: index.php');
    exit;
}
?>

<?php include '../templates/header.php'; ?>

<h2>Edit Comment</h2>
<form action="edit_comment.php?id=<?php echo $comment_id; ?>" method="post">
    <textarea name="comment" required><?php echo htmlspecialchars($comment['comment']); ?></textarea>
    <button type="submit">Update Comment</button>
</form>

<?php include '../templates/footer.php'; ?>
