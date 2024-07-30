<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

// Check if the user has already liked the post
$sql = 'SELECT * FROM likes WHERE post_id = ? AND user_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id, $user_id]);
$like = $stmt->fetch();

if ($like) {
    // If liked, remove the like
    $sql = 'DELETE FROM likes WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$like['id']]);
} else {
    // If not liked, add a like
    $sql = 'INSERT INTO likes (post_id, user_id) VALUES (?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $user_id]);
}

header('Location: index.php');
exit;
