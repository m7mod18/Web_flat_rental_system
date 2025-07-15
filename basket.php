<?php
session_start();
require_once 'database.inc.php';
$username = $_SESSION['username'] ?? '';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$flatIds = $_SESSION['basket'] ?? [];
$flats = [];

if (!empty($flatIds)) {
    $placeholders = implode(',', array_fill(0, count($flatIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM flats WHERE flat_id IN ($placeholders)");
    $stmt->execute($flatIds);
    $flats = $stmt->fetchAll();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$profilePic = $user['profile_pic'] ?? 'images/user.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Basket</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="cart-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" >
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    
    <div class="user-profile-nav">
      <a href="profile.php"><img src="<?= htmlspecialchars($profilePic) ?>" alt="User Photo" class="user-photo"></a>
      <span><?= htmlspecialchars($username) ?></span>
      <a href="about.php">About Us</a>
      <a href="profile.php">Profile</a>
   <a href="basket.php"> My Cart</a>
    <a href="logout.php">Logout</a>

    </div>

  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="search.php">Search Flats</a></li>
        <li><a href="view_rented.php">View Rented Flats</a></li>
      <li><a href="messages.php">Messages</a></li>
 
</ul>
  </nav>

  <main class="cart-main" >
    <div class="cart-container">
      <h2>My Basket</h2>

      <?php if (isset($_GET['added'])): ?>
        <p class="form-success">Flat successfully added to your basket.</p>
      <?php endif; ?>

      <?php if (empty($flats)): ?>
        <p class="empty-basket">Your basket is empty.</p>
      <?php else: ?>
        <table class="basket-table">
          <thead>
            <tr>
              <th>Photo</th>
              <th>Ref #</th>
              <th>Location</th>
              <th>Monthly Cost</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($flats as $flat): ?>
              <?php
                $flatId = $flat['flat_id'];
                $stmtPhoto = $pdo->prepare("SELECT photo_url FROM flat_photos WHERE flat_id = ? LIMIT 1");
                $stmtPhoto->execute([$flatId]);
                $photo = $stmtPhoto->fetchColumn() ?: 'images/no_image.png';
              ?>
              <tr>
                <td><img src="<?= htmlspecialchars($photo) ?>" class="basket-image" alt="Flat"></td>
                <td><?= htmlspecialchars($flat['ref_number']) ?></td>
                <td><?= htmlspecialchars($flat['location']) ?></td>
                <td>₪<?= htmlspecialchars($flat['monthly_cost']) ?></td>
                <td class="flat-buttons">
                  <a class="btn-proceed" href="rent_flat.php?flat_id=<?= $flatId ?>">Proceed to Rent</a>
                  <form method="post" action="remove_from_basket.php"class="inline-form">
                    <input type="hidden" name="flat_id" value="<?= $flatId ?>">
                    <button type="submit" class="btn-remove">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
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
