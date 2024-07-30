<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = 'SELECT * FROM users WHERE id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $profile_pic = $user['profile_pic'];

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $image_name = basename($_FILES['profile_pic']['name']);
        $image_path = '../uploads/' . $image_name;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $image_path);
        $profile_pic = 'uploads/' . $image_name;
    }

    $sql = 'UPDATE users SET username = ?, email = ?, profile_pic = ? WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email, $profile_pic, $user_id]);

    header('Location: profile.php');
}

$default_pic = 'uploads/default_user.png'; // Path to your default user image
?>

<?php include '../templates/header.php'; ?>

<h2>Your Profile</h2>
<div class="profile-container">
    <div class="profile-pic">
        <img src="../<?php echo htmlspecialchars($user['profile_pic'] ? $user['profile_pic'] : $default_pic); ?>" alt="Profile Picture">
    </div>
    <form action="profile.php" method="post" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <label for="profile_pic">Profile Picture:</label>
        <input type="file" id="profile_pic" name="profile_pic">
        <button type="submit">Update Profile</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>
