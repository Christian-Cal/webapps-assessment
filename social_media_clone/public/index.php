<?php
session_start();
require_once '../config/database.php';

$default_pic = 'uploads/default_user.png';

$sql = 'SELECT posts.*, users.username, users.profile_pic,
               (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count';

if (isset($_SESSION['user_id'])) {
    $sql .= ', (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id AND likes.user_id = ?) AS user_liked';
}

$sql .= ' FROM posts 
          JOIN users ON posts.user_id = users.id 
          ORDER BY posts.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute(isset($_SESSION['user_id']) ? [$_SESSION['user_id']] : []);
$posts = $stmt->fetchAll();

include '../templates/header.php';
?>

<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="banner">Looks like you're not logged in. <a href="login.php">Log in now</a>.</div>
<?php endif; ?>

<h2>All Posts</h2>
<?php foreach ($posts as $post): ?>
    <div class="post">
        <div class="post-header">
            <img src="../<?php echo htmlspecialchars($post['profile_pic'] ? $post['profile_pic'] : $default_pic); ?>" alt="Profile Picture" class="profile-thumb">
            <span><?php echo htmlspecialchars($post['username']); ?></span>
        </div>
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="like.php" method="post">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" class="like-btn">
                        <?php echo $post['user_liked'] ? 'Unlike' : 'Like'; ?>
                    </button>
                    <span><?php echo $post['like_count']; ?> likes</span>
                </form>
                <button class="comment-btn" data-post-id="<?php echo $post['id']; ?>">Comments</button>
            <?php else: ?>
                <span><?php echo $post['like_count']; ?> likes</span>
                <span><a href="login.php" class="login-prompt">Log in to like or comment</a></span>
            <?php endif; ?>
        </div>
        <div class="comments-modal" id="comments-<?php echo $post['id']; ?>">
            <div class="comments-content">
                <span class="close" data-post-id="<?php echo $post['id']; ?>">&times;</span>
                <h3>Comments</h3>
                <div class="comments-list">
                    <?php
                    $comment_sql = 'SELECT comments.*, users.username, users.profile_pic 
                                    FROM comments 
                                    JOIN users ON comments.user_id = users.id 
                                    WHERE comments.post_id = ? 
                                    ORDER BY comments.created_at DESC';
                    $comment_stmt = $pdo->prepare($comment_sql);
                    $comment_stmt->execute([$post['id']]);
                    $comments = $comment_stmt->fetchAll();
                    ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <img src="../<?php echo htmlspecialchars($comment['profile_pic'] ? $comment['profile_pic'] : $default_pic); ?>" alt="Profile Picture" class="profile-thumb">
                            <div class="comment-details">
                                <span><?php echo htmlspecialchars($comment['username']); ?></span>
                                <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                                <?php if (isset($_SESSION['user_id']) && $comment['user_id'] == $_SESSION['user_id']): ?>
                                    <div class="comment-actions">
                                        <a href="edit_comment.php?id=<?php echo $comment['id']; ?>" class="edit-btn">Edit</a>
                                        <a href="delete_comment.php?id=<?php echo $comment['id']; ?>" class="delete-btn">Delete</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="comment.php" method="post">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <textarea name="comment" required placeholder="Add a comment..."></textarea>
                        <button type="submit" class="add-comment-btn">Comment</button>
                    </form>
                <?php else: ?>
                    <p><a href="login.php" class="login-prompt">Log in to add a comment</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include '../templates/footer.php'; ?>

<script>
document.querySelectorAll('.comment-btn').forEach(button => {
    button.addEventListener('click', function() {
        const postId = this.getAttribute('data-post-id');
        document.getElementById(`comments-${postId}`).style.display = 'block';
    });
});

document.querySelectorAll('.close').forEach(span => {
    span.addEventListener('click', function() {
        const postId = this.getAttribute('data-post-id');
        document.getElementById(`comments-${postId}`).style.display = 'none';
    });
});

window.onclick = function(event) {
    if (event.target.classList.contains('comments-modal')) {
        event.target.style.display = 'none';
    }
}
</script>
