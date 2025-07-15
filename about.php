<?php
session_start();
$userRole = $_SESSION['role'] ?? 'guest';
$userName = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="about-us-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" height="50">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php" class="active">About Us</a>
    <?php if ($userRole !== 'guest'): ?>
      <div class="user-card">
        <img src="images/user.png" alt="User Photo" height="30">
        <span><?= htmlspecialchars($userName) ?></span>
        <a href="profile.php">Profile</a>
        <?php if ($userRole === 'customer'): ?>
          <a href="basket.php">My Cart</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
      </div>
    <?php else: ?>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="search.php">Search Flats</a></li>
      <?php if ($userRole === 'customer'): ?>
        <li><a href="view_rented.php">View Rented Flats</a></li>
      <?php elseif ($userRole === 'owner'): ?>
        <li><a href="add_flat.php">Add Flat</a></li>
        <li><a href="owner_appointments.php">Appointments</a></li>
      <?php elseif ($userRole === 'manager'): ?>
        <li><a href="pending_flats.php">Pending Flats</a></li>
        <li><a href="manager_inquiry.php">Manager Inquiry</a></li>
      <?php endif; ?>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="about-main">
    <h2>About Us</h2>

    <section class="about-section">
      <h3>The Agency</h3>
      <p>Birzeit Flat Rent was established in 2020 to streamline the apartment rental process in the city of Birzeit. Our company is led by an experienced management team and has received several awards for innovation in digital real estate solutions.</p>
    </section>

    <section class="about-section">
      <h3>The City</h3>
      <p>Birzeit is a historic town located near Ramallah in Palestine. It is known for Birzeit University, a pleasant climate, and a vibrant cultural life. Popular attractions include the Old City, local artisan markets, and annual cultural festivals.</p>
      <p>For more about the city, visit <a href="https://en.wikipedia.org/wiki/Birzeit" target="_blank">Wikipedia - Birzeit</a>.</p>
    </section>

    <section class="about-section">
      <h3>Main Business Activities</h3>
      <ul>
        <li>Connecting customers with verified rental listings</li>
        <li>Allowing owners to manage their flats online</li>
        <li>Enabling preview appointment bookings</li>
        <li>Manager oversight for rental approvals and inquiries</li>
        <li>Internal messaging between all platform users</li>
      </ul>
    </section>
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
