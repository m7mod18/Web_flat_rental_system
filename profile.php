<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

$profilePic = $user['profile_pic'] ?? 'images/user.png';

$extra = [];
if ($role === 'customer') {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $extra = $stmt->fetch() ?: [];
} elseif ($role === 'owner') {
    $stmt = $pdo->prepare("SELECT * FROM owners WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $extra = $stmt->fetch() ?: [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="profile-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" >
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <div class="user-profile-nav">
      <a href="profile.php"><img src="<?= htmlspecialchars($profilePic) ?>" alt="User Photo" class="user-photo" ></a>
      <span><?= htmlspecialchars($username) ?></span>
      <a href="about.php">About Us</a>
      <a href="profile.php">Profile</a>
      <?php if ($role === 'customer'): ?>
        <a href="basket.php">My Cart</a>
      <?php endif; ?>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <?php if ($role === 'customer'): ?>
        <li><a href="search.php">Search Flats</a></li>
        <li><a href="view_rented.php">View Rented Flats</a></li>
        <li><a href="basket.php">My Cart</a></li>
      <?php elseif ($role === 'owner'): ?>
        <li><a href="add_flat.php">Add Flat</a></li>
        <li><a href="owner_appointments.php">Appointments</a></li>
        <li><a href="approve_rentals.php">Approve Rentals</a></li>
      <?php elseif ($role === 'manager'): ?>
        <li><a href="pending_flats.php">Pending Flats</a></li>
        <li><a href="manager_inquiry.php">Flat Inquiry</a></li>
      <?php endif; ?>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="profile-edit-main" style="background-image: url('images/backg.jpg');">
    <div class="profile-view-card">
      <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture" class="profile-view-image">
      <h2><?= htmlspecialchars($user['email'] ?? 'No Email Available') ?></h2>
      <p><strong>Role:</strong> <?= ucfirst($role) ?></p>

      <?php if (!empty($extra)): ?>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($extra['name'] ?? 'N/A') ?></p>
        <p><strong>National ID:</strong> <?= htmlspecialchars($extra['national_id'] ?? 'N/A') ?></p>
      <?php endif; ?>

      <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'N/A') ?></p>
      <p><strong>City:</strong> <?= htmlspecialchars($user['city'] ?? 'N/A') ?></p>

      <a href="edit_profile.php" class="profile-edit-link">Edit Profile</a>
    </div>
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
