<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

$flat_id = $_GET['flat_id'] ?? null;
if (!$flat_id) {
    die("Missing flat ID.");
}

$success = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM flat_availability WHERE flat_id = ?");
$stmt->execute([$flat_id]);
$slots = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT appointment_date, appointment_time FROM appointments WHERE flat_id = ? AND status != 'rejected'");
$stmt->execute([$flat_id]);
$booked = $stmt->fetchAll(PDO::FETCH_ASSOC);
$booked_map = [];
foreach ($booked as $b) {
    $booked_map[$b['appointment_date'] . ' ' . $b['appointment_time']] = true;
}

function getUpcomingDatesForDay($day_name) {
    $dates = [];
    $today = new DateTime();
    for ($i = 0; $i < 14; $i++) {
        $date = (clone $today)->modify("+$i days");
        if ($date->format('l') === $day_name) {
            $dates[] = $date;
        }
    }
    return $dates;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'], $_POST['time'])) {
    $date = $_POST['date'];
    $time = $_POST['time'];

    $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $customer_id = $stmt->fetchColumn();

    if (!$customer_id) {
        die("Customer record not found.");
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE flat_id = ? AND customer_id = ? AND appointment_date = ? AND appointment_time = ?");
    $stmt->execute([$flat_id, $customer_id, $date, $time]);

    if ($stmt->fetchColumn() > 0) {
        $error = "❌ You have already requested this appointment.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO appointments (flat_id, customer_id, appointment_date, appointment_time, status)
                               VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$flat_id, $customer_id, $date, $time]);

        $owner_stmt = $pdo->prepare("SELECT u.user_id, u.username FROM flats f
                                     JOIN owners o ON f.owner_id = o.owner_id
                                     JOIN users u ON o.user_id = u.user_id
                                     WHERE f.flat_id = ?");
        $owner_stmt->execute([$flat_id]);
        $owner = $owner_stmt->fetch();

        $customer_name = $_SESSION['username'];
        $message = "Customer $customer_name requested a flat viewing on $date at $time.";
        $msg_stmt = $pdo->prepare("INSERT INTO messages (receiver_id, sender_role, title, message_body, sent_at, is_read)
                                   VALUES (?, 'customer', ?, ?, NOW(), 0)");
        $msg_stmt->execute([$owner['user_id'], "Appointment Request", $message]);

        $success = "✅ Appointment request sent successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Request Flat Appointment</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="customer-appointments-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" height="50">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
</header>

<main class="customer-appointments-main">
  <h2>Available Viewing Slots</h2>

  <?php if (!empty($success)): ?>
    <p class="form-success"><?= $success ?></p>
  <?php elseif (!empty($error)): ?>
    <p class="form-error"><?= $error ?></p>
  <?php endif; ?>

  <table class="customer-appointments-table">
    <thead>
      <tr>
        <th>Day</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($slots as $slot): ?>
        <?php foreach (getUpcomingDatesForDay($slot['day_of_week']) as $dateObj): 
          $dateStr = $dateObj->format('Y-m-d');
          $timeStr = $slot['time_slot'];
          $isBooked = isset($booked_map["$dateStr $timeStr"]);
        ?>
          <tr class="<?= $isBooked ? 'appointment-booked-row' : '' ?>">
            <td><?= htmlspecialchars($slot['day_of_week']) ?></td>
            <td><?= $dateStr ?></td>
            <td><?= $timeStr ?></td>
            <td><?= $isBooked ? 'Booked' : 'Available' ?></td>
            <td>
              <?php if (!$isBooked): ?>
                <form method="post" style="margin:0;">
                  <input type="hidden" name="date" value="<?= $dateStr ?>">
                  <input type="hidden" name="time" value="<?= $timeStr ?>">
                  <button type="submit" class="appointment-book-btn">Book</button>
                </form>
              <?php else: ?>
                <button class="appointment-disabled-btn" disabled>Unavailable</button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
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
