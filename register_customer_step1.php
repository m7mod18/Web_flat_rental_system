<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['reg_customer'] = [
        'national_id' => $_POST['national_id'],
        'full_name' => $_POST['full_name'],
        'house_no' => $_POST['house_no'],
        'street_name' => $_POST['street_name'],
        'city' => $_POST['city'],
        'postal_code' => $_POST['postal_code'],
        'dob' => $_POST['dob'],
        'email' => $_POST['email'],
        'mobile' => $_POST['mobile'],
        'telephone' => $_POST['telephone'],
    ];

    header("Location: register_step2.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Registration - Step 1</title>
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
    <form method="post">
      <fieldset class="register-fieldset">
        <legend class="register-legend">Step 1: Personal Information</legend>

        <label class="register-label">National ID Number:
          <input type="text" name="national_id" class="register-input" placeholder="9-digit ID (e.g., 123456789)" pattern="\d{9}" title="Enter exactly 9 digits" required>
        </label>

        <label class="register-label">Full Name:
          <input type="text" name="full_name" class="register-input" placeholder="Only letters (e.g., Ahmad Yaseen)" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" required>
        </label>

        <label class="register-label">Flat / House Number:
          <input type="text" name="house_no" class="register-input" placeholder="e.g., 12B" required>
        </label>

        <label class="register-label">Street Name:
          <input type="text" name="street_name" class="register-input" placeholder="e.g., Main Street" required>
        </label>

        <label class="register-label">City:
          <input type="text" name="city" class="register-input" placeholder="e.g., Ramallah" required>
        </label>

        <label class="register-label">Postal Code:
          <input type="text" name="postal_code" class="register-input" placeholder="e.g., 00970" pattern="\d{5}" title="Enter 5-digit postal code" required>
        </label>

        <label class="register-label">Date of Birth:
          <input type="date" name="dob" class="register-input" required>
        </label>

        <label class="register-label">Email Address:
          <input type="email" name="email" class="register-input" placeholder="e.g., user@example.com" required>
        </label>

        <label class="register-label">Mobile Number:
          <input type="tel" name="mobile" class="register-input" placeholder="e.g., 0591234567" pattern="\d{10}" title="Enter a 10-digit phone number" required>
        </label>

        <label class="register-label">Telephone Number:
          <input type="tel" name="telephone" class="register-input" placeholder="e.g., 022345678" pattern="\d{9}" title="Enter a 9-digit phone number">
        </label>

        <button type="submit" class="register-button">Next Step</button>
      </fieldset>
    </form>
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
