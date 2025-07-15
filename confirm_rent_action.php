<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    // Get customer_id
    $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $customer_id = $stmt->fetchColumn();

    if (!$customer_id) {
        die(" Error: Customer not found.");
    }

    // Get POST data
    $flat_id = $_POST['flat_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $total_cost = $_POST['total_cost'];
    $card_number = $_POST['card_number'];
    $card_expiry = $_POST['card_expiry'];
    $card_name = $_POST['card_name'];

    if (!preg_match('/^\d{9}$/', $card_number)) {
        die("Error: Credit card number must be exactly 9 digits.");
    }

    // Check for overlapping rentals
    $overlapCheck = $pdo->prepare("
        SELECT COUNT(*) FROM rentals
        WHERE flat_id = ? AND customer_id = ?
        AND (
            (rent_start <= ? AND rent_end >= ?) OR
            (rent_start <= ? AND rent_end >= ?) OR
            (rent_start >= ? AND rent_end <= ?)
        )
    ");
    $overlapCheck->execute([
        $flat_id, $customer_id,
        $start_date, $start_date,
        $end_date, $end_date,
        $start_date, $end_date
    ]);
    $conflictCount = $overlapCheck->fetchColumn();

    if ($conflictCount > 0) {
        die(" Error: You already have a rental request for this flat in the selected period.");
    }

    $insert = $pdo->prepare("
        INSERT INTO rentals (customer_id, flat_id, rent_start, rent_end, credit_card_number, total_cost, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $insert->execute([$customer_id, $flat_id, $start_date, $end_date, $card_number, $total_cost]);

    $flatStmt = $pdo->prepare("
        SELECT f.ref_number, o.user_id AS owner_user_id, u.username AS owner_name, u.phone AS owner_phone
        FROM flats f
        JOIN owners o ON f.owner_id = o.owner_id
        JOIN users u ON o.user_id = u.user_id
        WHERE f.flat_id = ?
    ");
    $flatStmt->execute([$flat_id]);
    $flatData = $flatStmt->fetch();

    $userStmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
    $userStmt->execute([$user_id]);
    $customerName = $userStmt->fetchColumn();

    $msgTitle = "New Rental Request";
    $msgBody = "Customer {$customerName} has requested to rent Flat Ref #{$flatData['ref_number']} from {$start_date} to {$end_date}.";
    $msgStmt = $pdo->prepare("INSERT INTO messages (receiver_id, sender_role, title, message_body, sent_at, is_read)
                              VALUES (?, 'customer', ?, ?, NOW(), 0)");
    $msgStmt->execute([$flatData['owner_user_id'], $msgTitle, $msgBody]);

    $success = true;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Rent Confirmation</title>
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

<main class="rent-flat-bg">
  <div class="rent-flat-form">
    <?php if (isset($success) && $success): ?>
      <h2>Rental Request Sent!</h2>
      <p>Thank you <strong><?= htmlspecialchars($customerName) ?></strong>, your request has been submitted successfully.</p>
      <p><strong>Owner Name:</strong> <?= htmlspecialchars($flatData['owner_name']) ?></p>
      <p><strong>Owner Phone:</strong> <?= htmlspecialchars($flatData['owner_phone']) ?></p>
      <p class="success-note">The owner has been notified. You will receive a message once the rental is approved.</p>
    <?php else: ?>
      <p class="form-error">Something went wrong. Please try again later.</p>
    <?php endif; ?>
  </div>
</main>

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
