<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['reg_customer'])) {
    header("Location: register_customer_step1.php");
    exit;
}

$customer = $_SESSION['reg_customer'];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmtUser = $pdo->prepare("INSERT INTO users (username, password, role, phone, city) VALUES (?, ?, 'customer', ?, ?)");
    $stmtUser->execute([
        $customer['username'],
        $customer['password'],
        $customer['mobile'],
        $customer['city']
    ]);
    $userId = $pdo->lastInsertId();

    $stmtCust = $pdo->prepare("INSERT INTO customers (user_id, national_id, name, address, dob, email, mobile, telephone) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $fullAddress = "{$customer['house_no']}, {$customer['street_name']}, {$customer['city']}, {$customer['postal_code']}";

    $stmtCust->execute([
        $userId,
        $customer['national_id'],
        $customer['full_name'],
        $fullAddress,
        $customer['dob'],
        $customer['email'],
        $customer['mobile'],
        $customer['telephone']
    ]);

    $customerId = $pdo->lastInsertId();
    unset($_SESSION['reg_customer']);
    $successMessage = "✅ Registration complete! Welcome, {$customer['full_name']} — Your Customer ID is " . str_pad($customerId, 9, "0", STR_PAD_LEFT) . ".";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Step 3</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" height="50">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
</header>

<main class="register-background">
  <div class="register-form">
    <?php if ($successMessage): ?>
      <h2>Registration Successful</h2>
      <p><?= htmlspecialchars($successMessage) ?></p>
      <a href="login.php" class="register-button">Login Now</a>
    <?php else: ?>
      <form method="post">
        <fieldset class="register-fieldset">
          <legend class="register-legend">Step 3: Review Your Details</legend>

          <p><strong>Full Name:</strong> <?= htmlspecialchars($customer['full_name']) ?></p>
          <p><strong>National ID:</strong> <?= htmlspecialchars($customer['national_id']) ?></p>
          <p><strong>Address:</strong> <?= htmlspecialchars("{$customer['house_no']}, {$customer['street_name']}, {$customer['city']}, {$customer['postal_code']}") ?></p>
          <p><strong>Date of Birth:</strong> <?= htmlspecialchars($customer['dob']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
          <p><strong>Mobile:</strong> <?= htmlspecialchars($customer['mobile']) ?></p>
          <p><strong>Telephone:</strong> <?= htmlspecialchars($customer['telephone']) ?></p>
          <p><strong>Username:</strong> <?= htmlspecialchars($customer['username']) ?></p>

          <button type="submit" class="register-button">Confirm Registration</button>
        </fieldset>
      </form>
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
