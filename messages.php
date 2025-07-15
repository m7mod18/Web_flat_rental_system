<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 'customer';
$allowed_roles = ['customer', 'owner', 'manager'];

if (!in_array($role, $allowed_roles)) {
    $role = 'customer';
}

$section = $_GET['section'] ?? 'inbox';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
$user_id = $user['user_id'];
$profilePic = $user['profile_pic'] ?? 'images/user.png';

$messages = [];
if ($section === 'inbox') {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE receiver_id = ? ORDER BY sent_at DESC");
    $stmt->execute([$user_id]);
    $messages = $stmt->fetchAll();

    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ?");
    $stmt->execute([$user_id]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Messages</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="messages-page">

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
      <?php if ($role === 'customer'): ?>
        <a href="basket.php">My Cart</a>
      <?php endif; ?>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<main class="messages-layout">
  <aside class="messages-sidebar">
    <a href="?section=inbox" class="<?= $section === 'inbox' ? 'active-tab' : '' ?>">Inbox</a>
    <a href="?section=send" class="<?= $section === 'send' ? 'active-tab' : '' ?>">New Message</a>
  </aside>

  <section class="messages-main">
    <?php if ($section === 'inbox'): ?>
      <h2 class="page-title">Inbox</h2>
      <?php if (empty($messages)): ?>
        <p class="info-text">No messages received.</p>
      <?php else: ?>
        <table class="search-table">
          <thead>
            <tr>
              <th>New</th>
              <th>Title</th>
              <th>From</th>
              <th>Date</th>
              <th>Message</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($messages as $msg): ?>
              <tr class="<?= $msg['is_read'] == 0 ? 'status-current' : '' ?>">
                <td><?= $msg['is_read'] == 0 ? '' : '' ?></td>
                <td><?= htmlspecialchars($msg['title']) ?></td>
                <td><?= ucfirst($msg['sender_role']) ?></td>
                <td><?= $msg['sent_at'] ?></td>
                <td class="message-body"><?= nl2br(htmlspecialchars($msg['message_body'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    <?php elseif ($section === 'send'): ?>
      <h2 class="page-title">Send Message</h2>
      <p class="info-text">❗ Sending messages manually is not allowed. Messages are generated automatically based on system actions like booking, approval, and cancellation.</p>
    <?php endif; ?>
  </section>
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
