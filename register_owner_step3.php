<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['register_owner_step1']) || !isset($_SESSION['register_owner'])) {
    header("Location: register_owner_step1.php");
    exit;
}

$step1 = $_SESSION['register_owner_step1'];
$step2 = $_SESSION['register_owner'];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'owner')");
        $stmt->execute([$step2['username'], $step2['password']]);
        $userId = $pdo->lastInsertId();

        $ownerId = rand(100000000, 999999999);

        $stmt = $pdo->prepare("INSERT INTO owners (
            owner_id, user_id, national_id, name, house_no, street_name, city, postal_code, dob, email, mobile, telephone, 
            bank_name, bank_branch, account_number
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $ownerId, $userId,
            $step1['national_id'],
            $step1['full_name'],
            $step1['house_no'],
            $step1['street_name'],
            $step1['city'],
            $step1['postal_code'],
            $step1['dob'],
            $step1['email'],
            $step1['mobile'],
            $step1['telephone'],
            $step1['bank_name'],
            $step1['bank_branch'],
            $step1['account_number']
        ]);

        $pdo->commit();

        unset($_SESSION['register_owner_step1'], $_SESSION['register_owner']);

        $successMessage = "✅ Welcome {$step1['full_name']}! Your Owner ID is: <strong style='color:green;'>$ownerId</strong>";

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Registration failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Owner Registration - Step 3</title>
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
    <h2 class="register-title">Step 3: Review & Confirm</h2>

    <?php if ($successMessage): ?>
      <div class="success-box">
        <p><?= $successMessage ?></p>
        <a href="login.php" class="register-button" style="margin-top: 20px;">Login to your account</a>
      </div>
    <?php else: ?>
      <form method="post">
        <fieldset class="register-fieldset">
          <legend class="register-legend">Review Your Information</legend>

          <p><strong>Name:</strong> <?= htmlspecialchars($step1['full_name']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($step1['email']) ?></p>
          <p><strong>Address:</strong> <?= htmlspecialchars($step1['house_no'] . ', ' . $step1['street_name'] . ', ' . $step1['city']) ?></p>
          <p><strong>Postal Code:</strong> <?= htmlspecialchars($step1['postal_code']) ?></p>
          <p><strong>Date of Birth:</strong> <?= htmlspecialchars($step1['dob']) ?></p>
          <p><strong>Mobile:</strong> <?= htmlspecialchars($step1['mobile']) ?></p>
          <p><strong>Telephone:</strong> <?= htmlspecialchars($step1['telephone']) ?></p>
          <p><strong>Bank:</strong> <?= htmlspecialchars($step1['bank_name']) ?> (<?= htmlspecialchars($step1['bank_branch']) ?>)</p>
          <p><strong>Account No.:</strong> <?= htmlspecialchars($step1['account_number']) ?></p>

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
