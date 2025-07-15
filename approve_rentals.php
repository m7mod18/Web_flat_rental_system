<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['username'];
$profilePic = $_SESSION['profile_pic'] ?? 'images/user.png';

$stmt = $pdo->prepare("SELECT owner_id FROM owners WHERE user_id = ?");
$stmt->execute([$user_id]);
$owner_id = $stmt->fetchColumn();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rental_id'], $_POST['action'])) {
    $rentalId = $_POST['rental_id'];
    $action = $_POST['action'];

    $stmt = $pdo->prepare("
        SELECT r.*, f.owner_id, f.flat_id, f.ref_number, u.username AS customer_name, u.phone AS customer_phone, u.user_id AS customer_user_id
        FROM rentals r
        JOIN flats f ON r.flat_id = f.flat_id
        JOIN customers c ON r.customer_id = c.customer_id
        JOIN users u ON c.user_id = u.user_id
        WHERE r.rental_id = ? AND f.owner_id = ?
    ");
    $stmt->execute([$rentalId, $owner_id]);
    $rental = $stmt->fetch();

    if (!$rental) {
        $error = " Unauthorized action or rental not found.";
    } else {
        if ($action === 'approve') {
            $pdo->prepare("UPDATE rentals SET status = 'approved' WHERE rental_id = ?")->execute([$rentalId]);
            $pdo->prepare("UPDATE flats SET is_rented = 1 WHERE flat_id = ?")->execute([$rental['flat_id']]);

            $msg = "Your rental request for Flat Ref #{$rental['ref_number']} has been approved. Please contact the owner ({$_SESSION['username']}) at phone: {$rental['customer_phone']} to collect the key.";
            sendMessage($pdo, $rental['customer_user_id'], 'owner', "Rental Approved", $msg);

            $success = " Rental approved successfully.";
        } elseif ($action === 'reject') {
            $pdo->prepare("UPDATE rentals SET status = 'rejected' WHERE rental_id = ?")->execute([$rentalId]);

            $msg = "Your rental request for Flat Ref #{$rental['ref_number']} has been rejected by the owner.";
            sendMessage($pdo, $rental['customer_user_id'], 'owner', "Rental Rejected", $msg);

            $success = " Rental rejected.";
        }
    }
}

function sendMessage($pdo, $receiver_id, $sender_role, $title, $body) {
    $stmt = $pdo->prepare("INSERT INTO messages (receiver_id, sender_role, title, message_body, sent_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
    $stmt->execute([$receiver_id, $sender_role, $title, $body]);
}

$stmt = $pdo->prepare("
    SELECT r.*, f.ref_number, f.location, u.username AS customer_name, u.phone AS customer_phone
    FROM rentals r
    JOIN flats f ON r.flat_id = f.flat_id
    JOIN customers c ON r.customer_id = c.customer_id
    JOIN users u ON c.user_id = u.user_id
    WHERE f.owner_id = ? AND r.status = 'pending'
    ORDER BY r.rent_start
");
$stmt->execute([$owner_id]);
$rentals = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Approve Rentals</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="owner-approve-rentals-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" >
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <div class="user-card">
      <img src="<?= htmlspecialchars($profilePic) ?>" alt="User Photo"  class="user-photo">
      <span><?= htmlspecialchars($userName) ?></span>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="search.php">Search Flats</a></li>
      <li><a href="add_flat.php">Add Flat</a></li>
      <li><a href="approve_rentals.php" class="active">Approve Rentals</a></li>
      <li><a href="owner_appointments.php">Appointments</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="approve-main">
    <h2>Rental Requests</h2>

    <?php if ($success): ?><p class="form-success"><?= $success ?></p><?php endif; ?>
    <?php if ($error): ?><p class="form-error"><?= $error ?></p><?php endif; ?>

    <?php if (empty($rentals)): ?>
      <p>No pending rental requests.</p>
    <?php else: ?>
      <table class="approve-table">
        <thead>
          <tr>
            <th>Flat Ref</th>
            <th>Location</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>From</th>
            <th>To</th>
            <th>Cost</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rentals as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['ref_number']) ?></td>
              <td><?= htmlspecialchars($r['location']) ?></td>
              <td><?= htmlspecialchars($r['customer_name']) ?></td>
              <td><?= htmlspecialchars($r['customer_phone']) ?></td>
              <td><?= htmlspecialchars($r['rent_start']) ?></td>
              <td><?= htmlspecialchars($r['rent_end']) ?></td>
              <td><?= htmlspecialchars($r['total_cost']) ?> ₪</td>
              <td>
                <form method="post" class="approve-form-inline">
                  <input type="hidden" name="rental_id" value="<?= $r['rental_id'] ?>">
                  <button name="action" value="approve" class="btn-approve">Approve</button>
                  <button name="action" value="reject" class="btn-reject">Reject</button>
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
