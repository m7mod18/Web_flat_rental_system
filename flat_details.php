<?php
session_start();
require_once 'database.inc.php';

$flatId = $_GET['flat_id'] ?? null;
if (!$flatId) {
    echo "<p>Invalid flat ID.</p>";
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM flats WHERE flat_id = ?");
$stmt->execute([$flatId]);
$flat = $stmt->fetch();
if (!$flat) {
    echo "<p>Flat not found.</p>";
    exit;
}

$stmtPhotos = $pdo->prepare("SELECT photo_url FROM flat_photos WHERE flat_id = ?");
$stmtPhotos->execute([$flatId]);
$photos = $stmtPhotos->fetchAll(PDO::FETCH_COLUMN);
$stmtMarketing = $pdo->prepare("SELECT title, description, url FROM flat_marketing WHERE flat_id = ?");
$stmtMarketing->execute([$flatId]);
$marketingItems = $stmtMarketing->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Flat Details</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" >
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <?php if (isset($_SESSION['username'])): ?>
      <div class="user-card">
        <img src="<?= $_SESSION['profile_pic'] ?? 'images/user.png' ?>" alt="User" >
        <span><?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php">Logout</a>
      </div>
    <?php else: ?>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>
</header>

<div class="layout-container">

  <nav>
    <ul>
      <li><a href="appointments.php?flat_id=<?= $flatId ?>">Request Flat Viewing Appointment</a></li>
      <li><a href="rent_flat.php?flat_id=<?= $flatId ?>">Rent the Flat</a></li>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer'): ?>
        <li>
          <form method="post" action="add_to_basket.php">
            <input type="hidden" name="flat_id" value="<?= $flatId ?>">
            <button type="submit" > Add to Basket</button>
          </form>
        </li>
      <?php endif; ?>
    </ul>
  </nav>

  <main class="flatdetails-main">

    <div class="flatcard">
      <div class="flatcard-photos">
        <?php foreach ($photos as $photo): ?>
          <figure>
            <img src="<?= htmlspecialchars($photo) ?>" alt="Flat Photo">
          </figure>
        <?php endforeach; ?>
      </div>

      <div class="flatcard-details">
        <h2 class="flatcard-title">Details for Flat #<?= htmlspecialchars($flat['ref_number']) ?></h2>
        <p><strong>Address:</strong> <?= htmlspecialchars($flat['address']) ?></p>
        <p><strong>Price:</strong> ₪<?= htmlspecialchars($flat['monthly_cost']) ?> / month</p>
        <p><strong>Rental Conditions:</strong> <?= htmlspecialchars($flat['rental_conditions']) ?></p>
        <p><strong>Bedrooms:</strong> <?= $flat['bedrooms'] ?> | <strong>Bathrooms:</strong> <?= $flat['bathrooms'] ?></p>
        <p><strong>Size:</strong> <?= $flat['size_sqm'] ?> m²</p>
        <p><strong>Heating:</strong> <?= $flat['has_heating'] ? 'Yes' : 'No' ?></p>
        <p><strong>Air Conditioning:</strong> <?= $flat['has_air_conditioning'] ? 'Yes' : 'No' ?></p>
        <p><strong>Access Control:</strong> <?= $flat['access_control'] ? 'Yes' : 'No' ?></p>
        <p><strong>Extra Features:</strong>
          <?= $flat['car_parking'] ? 'Parking, ' : '' ?>
          <?= $flat['backyard'] ? $flat['backyard'] . ' Backyard, ' : '' ?>
          <?= $flat['playground'] ? 'Playground, ' : '' ?>
          <?= $flat['storage'] ? 'Storage' : '' ?>
        </p>
      </div>
    </div>

    <aside class="flatcard-aside">
      <h3>Nearby Landmarks</h3>
      <?php if (count($marketingItems) === 0): ?>
        <p>No marketing info available.</p>
      <?php else: ?>
        <ul>
          <?php foreach ($marketingItems as $item): ?>
            <li>
              <strong><?= htmlspecialchars($item['title']) ?>:</strong>
              <?= htmlspecialchars($item['description']) ?>
              <?php if (!empty($item['url'])): ?>
                (<a href="<?= $item['url'] ?>" target="_blank">View</a>)
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </aside>

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
