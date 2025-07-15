<?php
session_start();
require_once 'database.inc.php';

if (!isset($_GET['user_id'])) {
    die("Missing user ID.");
}

$user_id = $_GET['user_id'];

$stmt = $pdo->prepare("SELECT u.username, u.email, u.phone, u.city, u.profile_pic FROM users u WHERE u.user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

$profilePic = $user['profile_pic'] ?? 'images/user_defult.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Card - <?= htmlspecialchars($user['username']) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="user-card-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" class="logo-img">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <?php if (isset($_SESSION['username'])): ?>
      <div class="user-card">
        <img src="<?= htmlspecialchars($_SESSION['profile_pic'] ?? 'images/user_defult.png') ?>" alt="User Photo">
        <span><?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
      </div>
    <?php else: ?>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>
</header>

<div class="layout-container">
  <main class="user-card-container">
    <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture" class="profile-view-image" style="width: 100px; height: 100px; border-radius: 50%;">
    <h2><?= htmlspecialchars($user['username']) ?></h2>
    <p><strong>City:</strong> <?= htmlspecialchars($user['city']) ?: 'N/A' ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?: 'N/A' ?></p>
<p><strong>Email:</strong>
  <?php if (!empty($user['email'])): ?>
    <a href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a>
  <?php else: ?>
    N/A
  <?php endif; ?>
</p>
  </main>
</div>

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
