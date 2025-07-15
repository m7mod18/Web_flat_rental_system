<?php
session_start();
require_once 'database.inc.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_pic'] = $user['profile_pic'] ?? 'images/user.png';

            $redirect = $_SESSION['redirect_after_login'] ?? 'home.php';
            unset($_SESSION['redirect_after_login']);

            header("Location: $redirect");
            exit;
        } else {
            $message = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" class="user-photo">
<h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
 <div class="top-links">
  <a href="about.php">About Us</a>

  <?php if (isset($_SESSION['username'])): ?>
    <div class="user-card">
      <img src="images/user.png" alt="User Photo" height="30">
      <span><?= htmlspecialchars($_SESSION['username']) ?></span>
      <a href="logout.php">Logout</a>
    </div>
  <?php else: ?>
    <a href="register.php">Register</a>
    <a href="login.php">Login</a>
  <?php endif; ?>
</div>

</header>
<main class="login-bg">
  <form method="post" class="login-form">
    <h2 class="login-title">Login</h2>
    <p class="login-error"><?= $message ?></p>

    <fieldset class="login-fieldset">
      <legend class="login-legend">Login to your account</legend>

      <label class="login-label">Username:
        <input type="text" name="username" required class="login-input">
      </label>

      <label class="login-label">Password:
        <input type="password" name="password" required class="login-input">
      </label>

      <button type="submit" name="login" class="login-button">Login</button>
    </fieldset>
  </form>
</main>

<footer>
  <img src="images/logo.png" alt="Mini Logo">
  <p>
    &copy; 2025 Birzeit Flat Rent | Developed by Mahmoud Kafafi | All rights reserved.<br>
    Email: info@birzeitflat.ps | Phone: 02-123456 |
    <a href="contact.php">Contact Us</a>
  </p>
</footer>
</body>
</html>
