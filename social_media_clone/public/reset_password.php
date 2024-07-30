<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(16)); 
    $expires = time() + 1800; 

    $sql = 'DELETE FROM password_resets WHERE email = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    $sql = 'INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $token, $expires]);

    $reset_link = "http://localhost/public/reset.php?token=$token";
    mail($email, "Password Reset", "Click this link to reset your password: $reset_link");

    echo 'A password reset link has been sent to your email.';
}
?>

<form action="reset_password.php" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit">Reset Password</button>
</form>
