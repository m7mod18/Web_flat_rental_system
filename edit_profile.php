<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

$profilePic = $user['profile_pic'] ?? 'images/user.png';

if ($role === 'customer') {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $extra = $stmt->fetch();
    $table = 'customers';
} elseif ($role === 'owner') {
    $stmt = $pdo->prepare("SELECT * FROM owners WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $extra = $stmt->fetch();
    $table = 'owners';
} else {
    $extra = [];
    $table = null;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $nid = $_POST['national_id'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];

    $targetDir = "uploads/";
    $newPic = $profilePic;

    if (!empty($_FILES['profile_pic']['name'])) {
        $filename = time() . "_" . basename($_FILES['profile_pic']['name']);
        $targetFile = $targetDir . $filename;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
                $newPic = $targetFile;
            }
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET phone = ?, city = ?, profile_pic = ? WHERE user_id = ?");
        $stmt->execute([$phone, $city, $newPic, $user['user_id']]);

        if ($table) {
            $stmt = $pdo->prepare("UPDATE $table SET name = ?, national_id = ? WHERE user_id = ?");
            $stmt->execute([$name, $nid, $user['user_id']]);
        }

        header("Location: profile.php");
        exit;
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="profile-edit-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" >
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <div class="user-profile-nav">
      <a href="profile.php"><img src="<?= htmlspecialchars($profilePic) ?>" alt="User Photo" class="user-photo"></a>
      <span><?= htmlspecialchars($username) ?></span>
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
      <li><a href="view_rented.php">View Rented Flats</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="profile-edit-main" style="background-image: url('images/backg.jpg');">
    <form method="post" enctype="multipart/form-data" class="profile-edit-form">
      <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture" class="profile-edit-image">

      <?php if (!empty($message)): ?>
        <p class="form-error"><?= $message ?></p>
      <?php endif; ?>

      <?php if ($role === 'customer' && isset($extra['customer_id'])): ?>
        <label>Customer ID:</label>
        <input type="text" value="<?= htmlspecialchars($extra['customer_id']) ?>" readonly>
      <?php elseif ($role === 'owner' && isset($extra['owner_id'])): ?>
        <label>Owner ID:</label>
        <input type="text" value="<?= htmlspecialchars($extra['owner_id']) ?>" readonly>
      <?php endif; ?>

      <label>Full Name:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($extra['name'] ?? '') ?>" required>

      <label>National ID:</label>
      <input type="text" name="national_id" value="<?= htmlspecialchars($extra['national_id'] ?? '') ?>" required>

      <label>Phone:</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>

      <label>City:</label>
      <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" required>

      <label>Change Profile Picture:</label>
      <input type="file" name="profile_pic" accept="image/*">

      <button type="submit" class="save-profile-btn">Update Profile</button>
    </form>
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
