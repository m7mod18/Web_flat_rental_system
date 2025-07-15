<?php
session_start();
$username = $_SESSION['username'] ?? 'Guest';
$role = $_SESSION['role'] ?? 'guest';
$profilePic = $_SESSION['profile_pic'] ?? 'images/user_defult.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="contact-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" height="50">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <?php if ($role !== 'guest'): ?>
    <div class="user-profile-nav">
      <img src="<?= htmlspecialchars($profilePic) ?>" alt="User Photo" class="user-photo">
      <span><?= htmlspecialchars($username) ?></span>
      <a href="profile.php">Profile</a>
      <?php if ($role === 'customer'): ?>
        <a href="basket.php">My Cart</a>
      <?php endif; ?>
      <a href="logout.php">Logout</a>
    </div>
    <?php else: ?>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="search.php">Search Flats</a></li>
      <li><a href="contact.php" class="active">Contact Us</a></li>
      <?php if ($role === 'customer'): ?>
        <li><a href="view_rented.php">View Rented Flats</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php elseif ($role === 'owner'): ?>
        <li><a href="add_flat.php">Add Flat</a></li>
        <li><a href="owner_appointments.php">Appointments</a></li>
        <li><a href="approve_rentals.php">Approve Rentals</a></li>
      <?php elseif ($role === 'manager'): ?>
        <li><a href="pending_flats.php">Pending Flats</a></li>
        <li><a href="manager_inquiry.php">Manager Search Flats</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <main class="contact-main">
    <h2>Contact Us</h2>
    <p>Have a question or feedback? Send us a message and we’ll get back to you as soon as possible.</p>

    <form method="post" action="#" class="contact-form">
      <label for="name">Full Name: <span class="required">*</span></label>
      <input type="text" name="name" id="name" required>

      <label for="email">Email: <span class="required">*</span></label>
      <input type="email" name="email" id="email" required>

      <label for="message">Message: <span class="required">*</span></label>
      <textarea name="message" id="message" rows="5" required></textarea>

      <button type="submit" class="action-button">Send Message</button>
    </form>
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
