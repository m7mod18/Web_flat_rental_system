<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION['username'];
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flat_id'], $_POST['action'])) {
    $flatId = (int)$_POST['flat_id'];

    if ($_POST['action'] === 'approve') {
        $pdo->prepare("UPDATE flats SET approved = 1 WHERE flat_id = ?")->execute([$flatId]);
        $successMessage = " Flat approved successfully.";
    } elseif ($_POST['action'] === 'reject') {
        $pdo->prepare("DELETE FROM flat_availability WHERE flat_id = ?")->execute([$flatId]);
        $pdo->prepare("DELETE FROM flat_marketing WHERE flat_id = ?")->execute([$flatId]);
        $pdo->prepare("DELETE FROM flat_photos WHERE flat_id = ?")->execute([$flatId]);

        $pdo->prepare("DELETE FROM flats WHERE flat_id = ?")->execute([$flatId]);
        $successMessage = " Flat rejected and deleted.";
    }

    header("Location: pending_flats.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM flats WHERE approved = 0 ORDER BY flat_id DESC");
$flats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pending Flats</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="manager-pending-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" >
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <div class="user-card">
      <img src="images/user_defult.png" alt="User Photo" class="user-photo">
      <span><?= htmlspecialchars($_SESSION['username']) ?></span>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="pending_flats.php" class="active">Pending Flats</a></li>
      <li><a href="manager_inquiry.php">Manager Search Flats</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="manager-pending-main">
    <h2 class="page-title">Pending Flats for Approval</h2>

    <?php if ($successMessage): ?>
      <p class="form-success"><?= $successMessage ?></p>
    <?php endif; ?>

    <?php if (empty($flats)): ?>
      <p class="info-text">No pending flats found.</p>
    <?php else: ?>
      <table class="pending-table">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Location</th>
            <th>Cost</th>
            <th>Available</th>
            <th>Size</th>
            <th>Features</th>
            <th>Photos</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($flats as $flat): ?>
          <tr>
            <td><?= htmlspecialchars($flat['ref_number']) ?></td>
            <td><?= htmlspecialchars($flat['location']) ?></td>
            <td>₪<?= $flat['monthly_cost'] ?></td>
            <td><?= $flat['available_from'] ?> → <?= $flat['available_to'] ?></td>
            <td><?= $flat['size_sqm'] ?> sqm</td>
            <td>
              <?= $flat['has_heating'] ? ' ' : '' ?>
              <?= $flat['has_air_conditioning'] ? '❄️ ' : '' ?>
              <?= $flat['access_control'] ? ' ' : '' ?>
              <?= $flat['car_parking'] ? '' : '' ?>
              <?= $flat['playground'] ? ' ' : '' ?>
              <?= $flat['storage'] ? ' ' : '' ?>
              <?= $flat['backyard'] !== 'none' ? ucfirst($flat['backyard']) : '' ?>
            </td>
            <td>
              <?php
              $stmtPhotos = $pdo->prepare("SELECT photo_url FROM flat_photos WHERE flat_id = ?");
              $stmtPhotos->execute([$flat['flat_id']]);
              $photos = $stmtPhotos->fetchAll();
              foreach ($photos as $p):
              ?>
                <img src="<?= htmlspecialchars($p['photo_url']) ?>" class="flat-photo-thumb">
              <?php endforeach; ?>
            </td>
            <td>
              <form method="post">
                <input type="hidden" name="flat_id" value="<?= $flat['flat_id'] ?>">
                <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
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
