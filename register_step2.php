<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['reg_customer'])) {
    header("Location: register_customer_step1.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }
    elseif (!preg_match('/^\d.{4,13}[a-z]$/', $password)) {
        $error = "Password must be 6-15 chars, start with a digit and end with a lowercase letter.";
    }
    elseif ($password !== $confirmPassword) {
        $error = "Password confirmation does not match.";
    }
    else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = "This email is already registered.";
        } else {
            $_SESSION['reg_customer']['username'] = $username;
            $_SESSION['reg_customer']['password'] = password_hash($password, PASSWORD_DEFAULT);
            header("Location: register_step3.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Step 2</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" height="50">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
</header>

<main class="register-step2-bg">
  <form method="post" class="register-step2-form">
    <h2 class="register-step2-title">Step 2: Create E-Account</h2>

    <?php if ($error): ?>
      <p class="register-step2-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <fieldset class="register-step2-fieldset">
      <legend class="register-step2-legend">Account Details</legend>

      <label class="register-step2-label">Email (Username):
        <input type="email" name="username" class="register-step2-input" required
               placeholder="e.g. user@example.com">
      </label>

      <label class="register-step2-label">Password:
        <input type="password" name="password" class="register-step2-input" required
               placeholder="6-15 chars, start with digit, end with lowercase letter">
      </label>

      <label class="register-step2-label">Confirm Password:
        <input type="password" name="confirm_password" class="register-step2-input" required>
      </label>

      <button type="submit" class="register-step2-button">Continue</button>
    </fieldset>
  </form>
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
