<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['last_page'] = 'view_rented.php';
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
$stmt->execute([$userId]);
$customerId = $stmt->fetchColumn();

if (!$customerId) die("Customer not found.");

$sql = "
SELECT r.*, f.flat_id, f.ref_number, f.monthly_cost, f.location,
       o.owner_id, u.username AS owner_name, u.user_id AS owner_user_id
FROM rentals r
JOIN flats f ON r.flat_id = f.flat_id
JOIN owners o ON f.owner_id = o.owner_id
JOIN users u ON o.user_id = u.user_id
WHERE r.customer_id = ? AND r.status = 'approved'
ORDER BY r.rent_start DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$customerId]);
$rents = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$profilePic = $user['profile_pic'] ?? 'images/user.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Rented Flats</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="rented-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" height="50">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <div class="user-profile-nav">
        <img src="<?= $_SESSION['profile_pic'] ?? 'images/user.png' ?>" alt="User" class="user-photo">
      <span><?= htmlspecialchars($user['username']) ?></span>
      <a href="about.php">About Us</a>
      <a href="profile.php">Profile</a>
      <a href="basket.php">My Cart</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="search.php">Search Flats</a></li>
      <li><a href="view_rented.php" class="active">View Rented Flats</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="rented-main">
    <div class="rented-container">
      <h2>My Rented Flats</h2>
      <p class="rented-subtext">Below is a list of your current and past flat rentals.</p>

      <?php if (empty($rents)): ?>
        <p class="rented-empty">You haven't rented any flats yet.</p>
      <?php else: ?>
        <table class="rented-table">
          <thead>
            <tr>
              <th>Flat Ref</th>
              <th>Monthly Cost</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Location</th>
              <th>Owner</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $today = date('Y-m-d');
            foreach ($rents as $rent):
              $isCurrent = ($rent['rent_end'] >= $today);
              $rowClass = $isCurrent ? 'rented-current' : 'rented-past';
            ?>
              <tr class="<?= $rowClass ?>">
                <td>
                  <a href="flat_details.php?flat_id=<?= $rent['flat_id'] ?>" target="_blank" class="rented-flat-link">
                    <?= htmlspecialchars($rent['ref_number']) ?>
                  </a>
                </td>
                <td>₪<?= htmlspecialchars($rent['monthly_cost']) ?></td>
                <td><?= htmlspecialchars($rent['rent_start']) ?></td>
                <td><?= htmlspecialchars($rent['rent_end']) ?></td>
                <td><?= htmlspecialchars($rent['location']) ?></td>
                <td>
                  <a href="owner_card.php?owner_id=<?= $rent['owner_user_id'] ?>" class="rented-owner-link" target="_blank">
                    <?= htmlspecialchars($rent['owner_name']) ?>
                  </a>
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
