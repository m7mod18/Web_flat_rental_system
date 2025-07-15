<?php
session_start();
require_once 'database.inc.php';

if (!isset($_GET['owner_id'])) {
    die("Missing owner ID.");
}

$ownerId = $_GET['owner_id'];

$stmt = $pdo->prepare("
    SELECT o.name, o.address, o.email, o.mobile, o.telephone, o.city, 
           o.bank_name, o.bank_branch, o.account_number, o.house_no, o.street_name, o.postal_code
    FROM owners o
    WHERE o.user_id = ?
");
$stmt->execute([$ownerId]);
$owner = $stmt->fetch();

if (!$owner) {
    die("Owner not found.");
}

$userRole = $_SESSION['role'] ?? 'guest';
$userName = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Owner Card</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="owner-card-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" height="50">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <?php if ($userRole === 'guest'): ?>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    <?php else: ?>
      <div class="user-card">
        <img src="images/user_defult.png" alt="User Photo" height="30">
        <span><?= htmlspecialchars($userName) ?></span>
        <a href="logout.php">Logout</a>
      </div>
    <?php endif; ?>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <?php if ($userRole === 'customer'): ?>
        <li><a href="search.php">Search Flats</a></li>
        <li><a href="view_rented.php">View Rented Flats</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php elseif ($userRole === 'owner'): ?>
        <li><a href="add_flat.php">Add Flat</a></li>
        <li><a href="appointments.php">Appointments</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php elseif ($userRole === 'manager'): ?>
        <li><a href="pending_flats.php">Approve Flats</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <main class="owner-card-main">
    <h2><?= htmlspecialchars($owner['name']) ?>'s Owner Card</h2>

    <div class="owner-card-box">
      <p><strong>Address:</strong> <?= htmlspecialchars($owner['address']) ?>,
        <?= htmlspecialchars($owner['house_no']) ?>,
        <?= htmlspecialchars($owner['street_name']) ?>,
        <?= htmlspecialchars($owner['city']) ?> (<?= htmlspecialchars($owner['postal_code']) ?>)</p>

      <p><strong>Email:</strong> <?= htmlspecialchars($owner['email']) ?></p>
      <p><strong>Mobile:</strong> <?= htmlspecialchars($owner['mobile']) ?></p>
      <p><strong>Telephone:</strong> <?= htmlspecialchars($owner['telephone']) ?></p>

      <hr>

      <h3>Bank Information</h3>
      <p><strong>Bank Name:</strong> <?= htmlspecialchars($owner['bank_name']) ?></p>
      <p><strong>Bank Branch:</strong> <?= htmlspecialchars($owner['bank_branch']) ?></p>
      <p><strong>Account #:</strong> <?= htmlspecialchars($owner['account_number']) ?></p>
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
