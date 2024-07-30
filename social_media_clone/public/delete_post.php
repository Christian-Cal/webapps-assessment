<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'];

// Delete likes associated with the post
$sql = 'DELETE FROM likes WHERE post_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);

// Delete comments associated with the post
$sql = 'DELETE FROM comments WHERE post_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);

// Delete the post
$sql = 'DELETE FROM posts WHERE id = ? AND user_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id, $user_id]);

header('Location: index.php');
exit;
?>
