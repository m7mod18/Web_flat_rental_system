<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['rental_info'])) {
    die("Missing rental information.");
}

$flat_id = $_SESSION['rental_info']['flat_id'];
$start_date = $_SESSION['rental_info']['start_date'];
$end_date = $_SESSION['rental_info']['end_date'];

$stmt = $pdo->prepare("SELECT f.*, o.owner_id, u.user_id AS owner_user_id, u.username AS owner_name, u.phone AS owner_phone
                       FROM flats f
                       JOIN owners o ON f.owner_id = o.owner_id
                       JOIN users u ON o.user_id = u.user_id
                       WHERE f.flat_id = ?");
$stmt->execute([$flat_id]);
$flat = $stmt->fetch();

if (!$flat) {
    die("Flat not found.");
}

$total_days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
$total_cost = round(($flat['monthly_cost'] / 30) * $total_days, 2);
$error = "";
$successMessage = "";

function sendMessage($pdo, $receiver_id, $sender_role, $title, $body) {
    $stmt = $pdo->prepare("INSERT INTO messages (receiver_id, sender_role, title, message_body, sent_at, is_read)
                           VALUES (?, ?, ?, ?, NOW(), 0)");
    $stmt->execute([$receiver_id, $sender_role, $title, $body]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cc_number = $_POST['cc_number'] ?? '';
    $cc_expiry = $_POST['cc_expiry'] ?? '';
    $cc_name = $_POST['cc_name'] ?? '';

    if (!preg_match('/^\d{9}$/', $cc_number)) {
        $error = "Credit card number must be exactly 9 digits.";
    } elseif (empty($cc_expiry) || empty($cc_name)) {
        $error = "Please complete all payment fields.";
    } else {
        $customer_stmt = $pdo->prepare("SELECT customer_id, user_id FROM customers WHERE user_id = ?");
        $customer_stmt->execute([$_SESSION['user_id']]);
        $customer = $customer_stmt->fetch();

        if (!$customer) {
            $error = "Customer not found.";
        } else {
            $customer_id = $customer['customer_id'];

            $checkPending = $pdo->prepare("
                SELECT COUNT(*) FROM rentals
                WHERE flat_id = ? AND customer_id = ? AND status = 'pending'
                AND (
                    (rent_start <= ? AND rent_end >= ?) OR
                    (rent_start <= ? AND rent_end >= ?) OR
                    (rent_start >= ? AND rent_end <= ?)
                )
            ");
            $checkPending->execute([
                $flat_id, $customer_id,
                $start_date, $start_date,
                $end_date, $end_date,
                $start_date, $end_date
            ]);
            $pendingCount = $checkPending->fetchColumn();

            if ($pendingCount > 0) {
                $error = "You already have a pending rental request for this flat during the selected period.";
            } else {
                $insert = $pdo->prepare("INSERT INTO rentals (flat_id, customer_id, rent_start, rent_end, total_cost, status)
                                         VALUES (?, ?, ?, ?, ?, 'pending')");
                $insert->execute([$flat_id, $customer_id, $start_date, $end_date, $total_cost]);

                $customer_user_stmt = $pdo->prepare("SELECT username, phone FROM users WHERE user_id = ?");
                $customer_user_stmt->execute([$_SESSION['user_id']]);
                $customer_user = $customer_user_stmt->fetch();

                $stmtManager = $pdo->prepare("SELECT user_id FROM users WHERE role = 'manager' LIMIT 1");
                $stmtManager->execute();
                $manager_id = $stmtManager->fetchColumn();

                $msg1 = "Customer {$customer_user['username']} has requested to rent Flat Ref #{$flat['ref_number']} from $start_date to $end_date.";
                sendMessage($pdo, $flat['owner_user_id'], 'customer', "Rental Request", $msg1);

                $msg2 = "Your rental request for Flat Ref #{$flat['ref_number']} has been sent to the owner.";
                sendMessage($pdo, $_SESSION['user_id'], 'customer', "Rental Request Sent", $msg2);

                $msg3 = "Rental request for Flat Ref #{$flat['ref_number']} from {$customer_user['username']} ($start_date to $end_date).";
                sendMessage($pdo, $manager_id, 'customer', "Rental Request Submitted", $msg3);

                unset($_SESSION['rental_info']);
                $successMessage = " Your rental request has been submitted! Awaiting owner's approval.";
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirm Flat Rent</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="confirm-page">
<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" >
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <?php if (isset($_SESSION['username'])): ?>
      <div class="user-card">
        <img src="<?= $_SESSION['profile_pic'] ?? 'images/user.png' ?>" alt="User" class="user-photo">
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
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="search.php">Search Flats</a></li>
      <li><a href="view_rented.php">View Rented Flats</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="confirm-bg-wrap">
    <div class="confirm-container">
      <h2>Confirm Rent Summary</h2>

      <?php if ($error): ?>
        <p class="form-error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <?php if ($successMessage): ?>
        <p class="form-success"><?= $successMessage ?></p>
      <?php else: ?>
        <div class="summary-box">
          <p><strong>Flat Ref #:</strong> <?= htmlspecialchars($flat['ref_number']) ?></p>
          <p><strong>Location:</strong> <?= htmlspecialchars($flat['location']) ?> — <?= htmlspecialchars($flat['address']) ?></p>
          <p><strong>Rent Duration:</strong> <?= $start_date ?> to <?= $end_date ?> (<?= $total_days ?> days)</p>
          <p><strong>Total Cost:</strong> <?= $total_cost ?> ₪</p>
          <p><strong>Owner:</strong> <?= htmlspecialchars($flat['owner_name'] ?? 'N/A') ?> (<?= htmlspecialchars($flat['owner_phone'] ?? 'N/A') ?>)</p>
        </div>

        <form method="post" class="payment-form">
          <fieldset>
            <legend>Payment Details</legend>
            <label>Credit Card Number:
              <input type="text" name="cc_number" pattern="\d{9}" placeholder="9-digit number" required>
            </label>
            <label>Expiry Date:
              <input type="month" name="cc_expiry" required>
            </label>
            <label>Name on Card:
              <input type="text" name="cc_name" required>
            </label>
          </fieldset>
          <button type="submit">Confirm & Rent</button>
        </form>
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
