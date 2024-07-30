<?php
require_once '../config/database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = 'SELECT * FROM password_resets WHERE token = ? AND expires > ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token, time()]);
    $reset = $stmt->fetch();

    if ($reset) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

            $sql = 'UPDATE users SET password = ? WHERE email = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$new_password, $reset['email']]);

            $sql = 'DELETE FROM password_resets WHERE email = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$reset['email']]);

            echo 'Your password has been reset. <a href="login.php">Login</a>';
        }
    } else {
        echo 'Invalid or expired token.';
    }
}
?>

<form action="reset.php?token=<?php echo htmlspecialchars($_GET['token']); ?>" method="post">
    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required>
    <button type="submit">Reset Password</button>
</form>
