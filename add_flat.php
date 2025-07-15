<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['username'];
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$profilePic = $stmt->fetchColumn() ?? 'images/user.png';

$stmt = $pdo->prepare("SELECT o.owner_id FROM owners o JOIN users u ON o.user_id = u.user_id WHERE u.username = ?");
$stmt->execute([$userName]);
$owner_id = $stmt->fetchColumn();

if (!$owner_id) {
    die("Owner ID not found. Please make sure your account is properly linked.");
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $available_from = $_POST['available_from'];
    $available_to = $_POST['available_to'];
    $today = date('Y-m-d');

    if ($available_from < $today) {
        $errorMessage = " The available from date cannot be in the past.";
    } elseif ($available_to < $available_from) {
        $errorMessage = " The available to date cannot be before the available from date.";
    } else {
        $used_slots = [];
        for ($i = 1; $i <= 3; $i++) {
            $day = $_POST["availability_day_$i"] ?? '';
            $time = $_POST["availability_time_$i"] ?? '';
            if (!empty($day) && !empty($time)) {
                $key = "$day|$time";
                if (in_array($key, $used_slots)) {
                    $errorMessage = " Duplicate time slot detected: $day at $time.";
                    break;
                }
                $used_slots[] = $key;
            }
        }
    }

    if (empty($errorMessage)) {
        $location = $_POST['location'];
        $address = $_POST['address'];
        $monthly_cost = $_POST['monthly_cost'];
        $bedrooms = $_POST['bedrooms'];
        $bathrooms = $_POST['bathrooms'];
        $size_sqm = $_POST['size_sqm'];
        $has_heating = isset($_POST['has_heating']) ? 1 : 0;
        $has_air_conditioning = isset($_POST['has_air_conditioning']) ? 1 : 0;
        $access_control = isset($_POST['access_control']) ? 1 : 0;
        $car_parking = isset($_POST['car_parking']) ? 1 : 0;
        $playground = isset($_POST['playground']) ? 1 : 0;
        $storage = isset($_POST['storage']) ? 1 : 0;
        $backyard = $_POST['backyard'] ?? 'shared';
        $furnished = $_POST['furnished'] ?? 'no';

        $stmt = $pdo->prepare("INSERT INTO flats (owner_id, location, address, monthly_cost, available_from, available_to, bedrooms, bathrooms, size_sqm, has_heating, has_air_conditioning, access_control, car_parking, playground, storage, backyard, furnished)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$owner_id, $location, $address, $monthly_cost, $available_from, $available_to, $bedrooms, $bathrooms, $size_sqm, $has_heating, $has_air_conditioning, $access_control, $car_parking, $playground, $storage, $backyard, $furnished]);

        $flat_id = $pdo->lastInsertId();
        $ref_number = 'F' . str_pad($flat_id, 6, '0', STR_PAD_LEFT);
        $pdo->prepare("UPDATE flats SET ref_number = ? WHERE flat_id = ?")->execute([$ref_number, $flat_id]);

        for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
            $tmpName = $_FILES['photos']['tmp_name'][$i];
            $originalName = $_FILES['photos']['name'][$i];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
            if ($tmpName && in_array($ext, $allowed) && getimagesize($tmpName)) {
                $photoName = "uploads/flat_" . $flat_id . "_$i.$ext";
                if (move_uploaded_file($tmpName, $photoName)) {
                    $stmt = $pdo->prepare("INSERT INTO flat_photos (flat_id, photo_url) VALUES (?, ?)");
                    $stmt->execute([$flat_id, $photoName]);
                }
            }
        }

        foreach ($used_slots as $slot) {
            [$day, $time] = explode('|', $slot);
            $stmt = $pdo->prepare("INSERT INTO flat_availability (flat_id, day_of_week, time_slot) VALUES (?, ?, ?)");
            $stmt->execute([$flat_id, $day, $time]);
        }

        for ($i = 1; $i <= 3; $i++) {
            $title = $_POST["marketing_title_$i"] ?? '';
            $desc = $_POST["marketing_description_$i"] ?? '';
            $url = $_POST["marketing_url_$i"] ?? '';
            if (!empty($title) && !empty($desc)) {
                $stmt = $pdo->prepare("INSERT INTO flat_marketing (flat_id, title, description, url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$flat_id, $title, $desc, $url]);
            }
        }

        $successMessage = " Flat added successfully. Waiting for manager approval.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Flat</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="add-flat-body">

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

<div class="add-flat-layout">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="search.php">Search Flats</a></li>
      <li><a href="add_flat.php" class="active">Add Flat</a></li>
      <li><a href="approve_rentals.php">Approve Rentals</a></li>
      <li><a href="owner_appointments.php">Appointments</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="add-flat-main">
    <h2 class="add-flat-title">Add a New Flat</h2>
    <?php if ($successMessage): ?>
      <p class="form-success"><?= $successMessage ?></p>
    <?php endif; ?>
<?php if ($errorMessage): ?>
  <p class="form-error"><?= htmlspecialchars($errorMessage) ?></p>
<?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="add-flat-form">
      <fieldset>
        <legend>Flat Details</legend>
        <label>Location: <input type="text" name="location" required></label>
        <label>Address: <input type="text" name="address" required></label>
        <label>Monthly Rent (₪): <input type="number" name="monthly_cost" required></label>
        <label>Available From: <input type="date" name="available_from" required></label>
        <label>Available To: <input type="date" name="available_to" required></label>
        <label>Bedrooms: <input type="number" name="bedrooms" required></label>
        <label>Bathrooms: <input type="number" name="bathrooms" required></label>
        <label>Size (sqm): <input type="number" name="size_sqm" required></label>

        <div class="add-flat-checkbox-row">
          <label><input type="checkbox" name="has_heating"> Heating</label>
          <label><input type="checkbox" name="has_air_conditioning"> Air Conditioning</label>
          <label><input type="checkbox" name="access_control"> Access Control</label>
          <label><input type="checkbox" name="car_parking"> Parking</label>
          <label><input type="checkbox" name="playground"> Playground</label>
          <label><input type="checkbox" name="storage"> Storage</label>
        </div>

        <label>Backyard Type:
          <select name="backyard">
            <option value="shared">Shared</option>
            <option value="individual">Individual</option>
          </select>
        </label>

        <label>Furnished:
          <select name="furnished">
            <option value="yes">Yes</option>
            <option value="no" selected>No</option>
          </select>
        </label>

        <label>Upload at least 3 photos:
          <input type="file" name="photos[]" accept="image/*" multiple required>
        </label>
      </fieldset>

      <fieldset>
        <legend>Viewing Availability</legend>
        <?php for ($i = 1; $i <= 3; $i++): ?>
          <label>Day <?= $i ?>:
            <select name="availability_day_<?= $i ?>">
              <option value="">-- Select Day --</option>
              <option>Sunday</option><option>Monday</option><option>Tuesday</option>
              <option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option>
            </select>
            <input type="time" name="availability_time_<?= $i ?>">
          </label>
        <?php endfor; ?>
      </fieldset>

      <fieldset>
        <legend>Nearby Marketing Information (Optional)</legend>
        <?php for ($i = 1; $i <= 3; $i++): ?>
          <label>Marketing Point <?= $i ?>:
            <input type="text" name="marketing_title_<?= $i ?>" placeholder="e.g., Nearby School">
            <textarea name="marketing_description_<?= $i ?>" placeholder="Short description"></textarea>
            <input type="url" name="marketing_url_<?= $i ?>" placeholder="URL (optional)">
          </label>
        <?php endfor; ?>
      </fieldset>

      <button type="submit">Submit Flat</button>
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
