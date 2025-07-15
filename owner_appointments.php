<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT o.owner_id FROM owners o JOIN users u ON o.user_id = u.user_id WHERE u.username = ?");
$stmt->execute([$userName]);
$owner_id = $stmt->fetchColumn();

$success = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['appointment_id'])) {
    $appointmentId = $_POST['appointment_id'];
    $newStatus = $_POST['action'] === 'approve' ? 'approved' : 'rejected';

    $update = $pdo->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
    $update->execute([$newStatus, $appointmentId]);

    $customer_stmt = $pdo->prepare("
        SELECT u.user_id 
        FROM appointments a
        JOIN customers c ON a.customer_id = c.customer_id
        JOIN users u ON c.user_id = u.user_id
        WHERE a.appointment_id = ?
    ");
    $customer_stmt->execute([$appointmentId]);
    $customer = $customer_stmt->fetch();

    if ($customer) {
        $title = "Appointment " . ucfirst($newStatus);
        $body = "Your flat preview appointment has been $newStatus by the owner.";
        $msg_stmt = $pdo->prepare("INSERT INTO messages (receiver_id, sender_role, title, message_body, sent_at, is_read) VALUES (?, 'owner', ?, ?, NOW(), 0)");
        $msg_stmt->execute([$customer['user_id'], $title, $body]);
    }

    $success = "✅ Appointment has been $newStatus.";
}

$stmt = $pdo->prepare("
    SELECT a.*, u.username AS full_name, u.phone, c.email, f.ref_number
    FROM appointments a
    JOIN customers c ON a.customer_id = c.customer_id
    JOIN users u ON c.user_id = u.user_id
    JOIN flats f ON a.flat_id = f.flat_id
    WHERE f.owner_id = ?
    ORDER BY a.appointment_date, a.appointment_time
");
$stmt->execute([$owner_id]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Owner Appointments</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="owner-appointments-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" >
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <div class="user-card">
      <img src="images/user_defult.png" alt="User Photo" class="user-photo">
      <span><?= htmlspecialchars($userName) ?></span>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
                    <li><a href="search.php">Search Flats</a></li>

      <li><a href="add_flat.php">Add Flat</a></li>
                  <li><a href="approve_rentals.php">Approve Rentals</a></li>

      <li><a href="appointments.php" class="active">Appointments</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="owner-appointments-main">
    <h2>Flat Viewing Appointments</h2>

    <?php if ($success): ?>
      <p class="form-success"><?= $success ?></p>
    <?php endif; ?>

    <?php if (empty($appointments)): ?>
      <p>No appointments found.</p>
    <?php else: ?>
      <table class="owner-appointments-table">
        <thead>
          <tr>
            <th>Ref #</th>
            <th>Date</th>
            <th>Time</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($appointments as $a): ?>
            <tr class="status-<?= $a['status'] ?>">
              <td><?= htmlspecialchars($a['ref_number']) ?></td>
              <td><?= htmlspecialchars($a['appointment_date']) ?></td>
              <td><?= htmlspecialchars($a['appointment_time']) ?></td>
              <td><?= htmlspecialchars($a['full_name']) ?></td>
              <td><?= htmlspecialchars($a['phone']) ?></td>
              <td><a href="mailto:<?= htmlspecialchars($a['email']) ?>"><?= htmlspecialchars($a['email']) ?></a></td>
              <td class="status-cell"><?= ucfirst($a['status']) ?></td>
              <td>
                <?php if ($a['status'] === 'pending'): ?>
                  <form method="post" class="action-form">
                    <input type="hidden" name="appointment_id" value="<?= $a['appointment_id'] ?>">
                    <button name="action" value="approve">Approve</button>
                    <button name="action" value="reject">Reject</button>
                  </form>
                <?php else: ?>
                  <em>No actions</em>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
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
