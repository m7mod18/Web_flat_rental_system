<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['register_owner_step1'])) {
    header("Location: register_owner_step1.php");
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
    else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = "This email is already registered.";
        }
        elseif (!preg_match('/^\d.{4,13}[a-z]$/', $password)) {
            $error = "Password must be 6-15 characters, start with a digit and end with a lowercase letter.";
        }
        elseif ($password !== $confirmPassword) {
            $error = "Password confirmation does not match.";
        }
        else {
            $_SESSION['register_owner']['username'] = $username;
            $_SESSION['register_owner']['password'] = password_hash($password, PASSWORD_DEFAULT);
            header("Location: register_owner_step3.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Owner Registration - Step 2</title>
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
  <form method="post" class="register-form">
    <h2 class="register-title">Step 2: Create E-Account</h2>

    <?php if ($error): ?>
      <p class="register-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <fieldset class="register-fieldset">
      <legend class="register-legend">Account Details</legend>

      <label class="register-label">Email (Username):
        <input type="email" name="username" class="register-input" required
               placeholder="e.g. owner@example.com">
      </label>

      <label class="register-label">Password:
        <input type="password" name="password" class="register-input" required
               placeholder="6-15 characters, start with digit, end with lowercase letter">
      </label>

      <label class="register-label">Confirm Password:
        <input type="password" name="confirm_password" class="register-input" required>
      </label>

      <button type="submit" class="register-button">Continue</button>
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
