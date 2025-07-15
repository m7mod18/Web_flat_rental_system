<?php
session_start();
require_once 'database.inc.php';

$userRole = $_SESSION['role'] ?? 'guest';
$userName = $_SESSION['username'] ?? 'Guest';
$profilePic = "images/user_defult.png";

if ($userRole !== 'guest') {
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE username = ?");
    $stmt->execute([$userName]);
    $row = $stmt->fetch();
    if ($row && !empty($row['profile_pic'])) {
        $profilePic = $row['profile_pic'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" class="logo-img">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <?php if ($userRole !== 'guest'): ?>
      <div class="user-card">
        <img src="<?= htmlspecialchars($profilePic) ?>" alt="User Photo">
        <span><?= htmlspecialchars($userName) ?></span>
        <a href="profile.php">Profile</a>
      </div>
      <a href="logout.php">Logout</a>
      <?php if ($userRole === 'customer'): ?>
        <a href="basket.php" class="basket-link">🛒 Basket</a>
      <?php endif; ?>
    <?php else: ?>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php" class="<?= basename($_SERVER['PHP_SELF']) === 'home.php' ? 'active' : '' ?>">Home</a></li>
      <?php if ($userRole === 'guest'): ?>
        <li><a href="search.php">Search Flats</a></li>
      <?php elseif ($userRole === 'customer'): ?>
        <li><a href="search.php">Search Flats</a></li>
        <li><a href="view_rented.php">View Rented Flats</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php elseif ($userRole === 'owner'): ?>
        <li><a href="search.php">Search Flats</a></li>
        <li><a href="add_flat.php">Add Flat</a></li>
        <li><a href="approve_rentals.php">Approve Rentals</a></li>
        <li><a href="owner_appointments.php">Appointments</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php elseif ($userRole === 'manager'): ?>
        <li><a href="pending_flats.php">Pending Flats</a></li>
        <li><a href="manager_inquiry.php">Manager Search Flats</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <main>
    <div class="home-video-wrapper">
      <video autoplay muted loop playsinline class="home-background-video">
        <source src="images/vid2.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>

      <div class="home-video-overlay">
        <div class="hero-overlay">
          <h2>Welcome to Birzeit Flat Rent</h2>
          <p>
            <?= $userRole === 'guest'
              ? "Your gateway to finding the perfect flat in Birzeit."
              : "Welcome back, " . htmlspecialchars($userName) . "!" ?>
          </p>
          <?php if ($userRole === 'guest'): ?>
            <a href="register.php" class="action-button">Register</a>
          <?php endif; ?>
        </div>

        <?php if ($userRole === 'guest'): ?>
          <section class="about-site">
            <h2>About Birzeit Flat Rent</h2>
            <p>
              Birzeit Flat Rent is a platform designed to simplify and digitize the apartment rental process in Birzeit. 
              Whether you are a student, a worker, or a resident looking for a place to stay, our platform helps connect 
              you directly with flat owners.
            </p>
            <ul>
              <li><strong>Customers</strong> can search for available flats, book appointments, and request rentals online.</li>
              <li><strong>Owners</strong> can list their flats, manage incoming requests, and approve rental applications.</li>
              <li><strong>Managers</strong> monitor the listings, handle user messages, and maintain platform quality.</li>
            </ul>
            <p>Our mission is to make housing in Birzeit easier, transparent, and accessible for everyone.</p>
            <div><a href="register.php" class="action-button">Join Us</a></div>
          </section>
        <?php endif; ?>

        <?php if ($userRole === 'manager'): ?>
          <section class="dashboard-box">
            <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM flats WHERE approved = 0");
            $pendingCount = $stmt->fetchColumn();
            ?>
            <h3>Dashboard</h3>
            <p>You have <strong><?= $pendingCount ?></strong> flats waiting for approval.</p>
            <a href="pending_flats.php" class="action-button">Review Flats</a>
          </section>

        <?php elseif ($userRole === 'customer'): ?>
          <section class="dashboard-box">
            <?php
            $userId = $_SESSION['user_id'];
            $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
            $stmt->execute([$userId]);
            $customerId = $stmt->fetchColumn();
            $rentedCount = 0;
            if ($customerId) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM rentals WHERE customer_id = ? AND rent_end >= CURDATE() AND status = 'approved'");
                $stmt->execute([$customerId]);
                $rentedCount = $stmt->fetchColumn();
            }
            ?>
            <h3>Customer Dashboard</h3>
            <p>You are currently renting <strong><?= $rentedCount ?></strong> flat<?= $rentedCount == 1 ? '' : 's' ?>.</p>
            <a href="view_rented.php" class="action-button">View My Rentals</a>
          </section>

        <?php elseif ($userRole === 'owner'): ?>
          <?php
          $stmt = $pdo->prepare("SELECT o.owner_id FROM owners o JOIN users u ON o.user_id = u.user_id WHERE u.username = ?");
          $stmt->execute([$userName]);
          $ownerId = $stmt->fetchColumn();

          $stmtFlats = $pdo->prepare("SELECT COUNT(*) FROM flats WHERE owner_id = ?");
          $stmtFlats->execute([$ownerId]);
          $flatCount = $stmtFlats->fetchColumn();

          $stmtPendingAppointments = $pdo->prepare("
              SELECT COUNT(*) FROM appointments a
              JOIN flats f ON a.flat_id = f.flat_id
              WHERE f.owner_id = ? AND a.status = 'pending' AND a.appointment_date >= CURDATE()
          ");
          $stmtPendingAppointments->execute([$ownerId]);
          $pendingAppointments = $stmtPendingAppointments->fetchColumn();

          $stmtRented = $pdo->prepare("
              SELECT COUNT(DISTINCT f.flat_id) FROM appointments a
              JOIN flats f ON a.flat_id = f.flat_id
              WHERE f.owner_id = ? AND a.status = 'approved'
          ");
          $stmtRented->execute([$ownerId]);
          $rentedCount = $stmtRented->fetchColumn();
          ?>
          <section class="owner-dashboard">
            <h3>Owner Dashboard</h3>
            <ul>
              <li><strong><?= $flatCount ?></strong> flat(s) you’ve added.</li>
              <li><strong><?= $pendingAppointments ?></strong> pending appointment(s).</li>
              <li><strong><?= $rentedCount ?></strong> flat(s) currently rented.</li>
            </ul>
            <a href="owner_appointments.php" class="action-button">View Appointments</a>
          </section>
        <?php endif; ?>
      </div>
    </div>
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
