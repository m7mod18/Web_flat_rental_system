<?php
session_start();

require_once 'database.inc.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

$flat_id = $_GET['flat_id'] ?? $_POST['flat_id'] ?? null;
$error_message = "";

if (!$flat_id) {
    echo "No flat selected.";
    exit;
}

$stmt = $pdo->prepare("SELECT f.*, o.owner_id, o.national_id AS owner_nid, o.address AS owner_address, u.username AS owner_name, u.phone AS owner_phone
                       FROM flats f 
                       JOIN owners o ON f.owner_id = o.owner_id 
                       JOIN users u ON o.user_id = u.user_id
                       WHERE f.flat_id = ?");

$stmt->execute([$flat_id]);
$flat = $stmt->fetch();

if (!$flat) {
    echo "Flat not found.";
    exit;
}
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    if (empty($start_date) || empty($end_date)) {
        $error_message = "Please provide both start and end dates.";
    } elseif ($start_date < $flat['available_from'] || $end_date > $flat['available_to'] || $start_date > $end_date) {
        $error_message = "Please select dates within the available range: {$flat['available_from']} to {$flat['available_to']}.";
    } else {
        $_SESSION['rental_info'] = [
            'flat_id' => $flat_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
        header("Location: rent_flat_confirm.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rent Flat</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

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
  <form method="post" action="rent_flat.php" class="rent-flat-form">
    <h2>Rent Flat: Step 1</h2>

    <?php if ($error_message): ?>
      <div class="form-error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <fieldset>
      <legend>Flat Information</legend>
      <label>Reference Number:
        <input type="text" name="ref_number" value="<?= htmlspecialchars($flat['ref_number']) ?>" readonly>
      </label>
      <label>Location:
        <input type="text" value="<?= htmlspecialchars($flat['location']) ?>" readonly>
      </label>
      <label>Address:
        <input type="text" value="<?= htmlspecialchars($flat['address']) ?>" readonly>
      </label>
      <label>Details:
        <textarea readonly><?= htmlspecialchars($flat['rental_conditions'] ?? 'No rental conditions available.') ?></textarea>
      </label>
    </fieldset>

    <fieldset>
      <legend>Owner Information</legend>
      <label>Owner Name:
        <input type="text" value="<?= htmlspecialchars($flat['owner_name']) ?>" readonly>
      </label>
      <label>Owner National ID:
        <input type="text" value="<?= htmlspecialchars($flat['owner_nid']) ?>" readonly>
      </label>
      
    </fieldset>

    <fieldset>
      <legend>Rental Period</legend>
      <label>Start Date:
        <input type="date" name="start_date" value="<?= $_POST['start_date'] ?? '' ?>" required>
      </label>
      <label>End Date:
        <input type="date" name="end_date" value="<?= $_POST['end_date'] ?? '' ?>" required>
      </label>
      <p class="availability-note">
        Available from: <strong><?= $flat['available_from'] ?></strong> to <strong><?= $flat['available_to'] ?></strong>
      </p>
    </fieldset>

    <input type="hidden" name="flat_id" value="<?= $flat_id ?>">
    <button type="submit">Next</button>
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
