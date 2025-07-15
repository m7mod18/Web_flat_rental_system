<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['username'];
$profilePic = 'images/user_defult.png';

$owners = $pdo->query("SELECT o.owner_id, u.username FROM owners o JOIN users u ON o.user_id = u.user_id")->fetchAll();
$customers = $pdo->query("SELECT c.customer_id, u.username FROM customers c JOIN users u ON c.user_id = u.user_id")->fetchAll();

$where = [];
$params = [];

if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $where[] = "r.rent_start >= ? AND r.rent_end <= ?";
    $params[] = $_GET['from_date'];
    $params[] = $_GET['to_date'];
}
if (!empty($_GET['specific_date'])) {
    $where[] = "r.rent_start <= ? AND r.rent_end >= ?";
    $params[] = $_GET['specific_date'];
    $params[] = $_GET['specific_date'];
}
if (!empty($_GET['location'])) {
    $where[] = "f.location LIKE ?";
    $params[] = "%" . $_GET['location'] . "%";
}
if (!empty($_GET['owner_id'])) {
    $where[] = "f.owner_id = ?";
    $params[] = $_GET['owner_id'];
}
if (!empty($_GET['customer_id'])) {
    $where[] = "r.customer_id = ?";
    $params[] = $_GET['customer_id'];
}

$sql = "
    SELECT r.*, f.flat_id, f.ref_number, f.monthly_cost, f.location,
           u1.username AS owner_name, o.user_id AS owner_user_id,
           u2.username AS customer_name, c.user_id AS customer_user_id
    FROM rentals r
    JOIN flats f ON r.flat_id = f.flat_id
    JOIN owners o ON f.owner_id = o.owner_id
    JOIN users u1 ON o.user_id = u1.user_id
    JOIN customers c ON r.customer_id = c.customer_id
    JOIN users u2 ON c.user_id = u2.user_id
";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$allowedSorts = [
    'ref_number' => 'f.ref_number',
    'monthly_cost' => 'f.monthly_cost',
    'rent_start' => 'r.rent_start',
    'rent_end' => 'r.rent_end',
    'location' => 'f.location',
    'owner_name' => 'u1.username',
    'customer_name' => 'u2.username'
];

$sort_by = $_GET['sort_by'] ?? 'r.rent_start';
$order = strtolower($_GET['order'] ?? 'desc');
$order = ($order === 'asc') ? 'ASC' : 'DESC';

$sort_column = $allowedSorts[$sort_by] ?? 'r.rent_start';
$sql .= " ORDER BY $sort_column $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manager Flat Inquiry</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="manager-inquiry-page">

<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo" height="50">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <div class="user-card">
      <img src="<?= htmlspecialchars($profilePic) ?>" alt="User Photo" height="30">
      <span><?= htmlspecialchars($userName) ?></span>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<div class="manager-inquiry-hero"></div>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php">Home</a></li>
        <li><a href="pending_flats.php">Pending Flats</a></li>
                <li><a href="manager_inquiry.php">Manager Search Flats</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
  </nav>

  <main class="manager-inquiry-main">
    <form method="get" class="manager-inquiry-form">
      <fieldset>
        <legend>Search Filters</legend>
        <label>Available Between:
          <input type="date" name="from_date"> to
          <input type="date" name="to_date">
        </label>
        <label>Available On:
          <input type="date" name="specific_date">
        </label>
        <label>Location:
          <input type="text" name="location" placeholder="e.g., Ramallah">
        </label>
        <label>Owner:
          <select name="owner_id">
            <option value="">-- All Owners --</option>
            <?php foreach ($owners as $o): ?>
              <option value="<?= $o['owner_id'] ?>"><?= htmlspecialchars($o['username']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Customer:
          <select name="customer_id">
            <option value="">-- All Customers --</option>
            <?php foreach ($customers as $c): ?>
              <option value="<?= $c['customer_id'] ?>"><?= htmlspecialchars($c['username']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <button type="submit">Apply Filters</button>
      </fieldset>
    </form>

    <h2>Results</h2>
    <?php if (empty($results)): ?>
      <p>No matching results found.</p>
    <?php else: ?>
      <table class="results-table">
        <thead>
          <tr>
            <th><a href="?sort_by=ref_number&order=<?= ($sort_by === 'ref_number' && $order === 'ASC') ? 'desc' : 'asc' ?>">Ref #<?= $sort_by === 'ref_number' ? ($order === 'ASC' ? ' ▲' : ' ▼') : '' ?></a></th>
            <th><a href="?sort_by=monthly_cost&order=<?= ($sort_by === 'monthly_cost' && $order === 'ASC') ? 'desc' : 'asc' ?>">Monthly Cost<?= $sort_by === 'monthly_cost' ? ($order === 'ASC' ? ' ▲' : ' ▼') : '' ?></a></th>
            <th><a href="?sort_by=rent_start&order=<?= ($sort_by === 'rent_start' && $order === 'ASC') ? 'desc' : 'asc' ?>">From<?= $sort_by === 'rent_start' ? ($order === 'ASC' ? ' ▲' : ' ▼') : '' ?></a></th>
            <th><a href="?sort_by=rent_end&order=<?= ($sort_by === 'rent_end' && $order === 'ASC') ? 'desc' : 'asc' ?>">To<?= $sort_by === 'rent_end' ? ($order === 'ASC' ? ' ▲' : ' ▼') : '' ?></a></th>
            <th><a href="?sort_by=location&order=<?= ($sort_by === 'location' && $order === 'ASC') ? 'desc' : 'asc' ?>">Location<?= $sort_by === 'location' ? ($order === 'ASC' ? ' ▲' : ' ▼') : '' ?></a></th>
            <th><a href="?sort_by=owner_name&order=<?= ($sort_by === 'owner_name' && $order === 'ASC') ? 'desc' : 'asc' ?>">Owner<?= $sort_by === 'owner_name' ? ($order === 'ASC' ? ' ▲' : ' ▼') : '' ?></a></th>
            <th><a href="?sort_by=customer_name&order=<?= ($sort_by === 'customer_name' && $order === 'ASC') ? 'desc' : 'asc' ?>">Customer<?= $sort_by === 'customer_name' ? ($order === 'ASC' ? ' ▲' : ' ▼') : '' ?></a></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($results as $row): ?>
            <tr>
              <td><a class="ref-link" href="flat_details.php?flat_id=<?= $row['flat_id'] ?>" target="_blank"><?= $row['ref_number'] ?></a></td>
              <td><?= $row['monthly_cost'] ?> ₪</td>
              <td><?= $row['rent_start'] ?></td>
              <td><?= $row['rent_end'] ?></td>
              <td><?= htmlspecialchars($row['location']) ?></td>
              <td><a class="user-link" href="user_card.php?user_id=<?= $row['owner_user_id'] ?>" target="_blank"><?= htmlspecialchars($row['owner_name']) ?></a></td>
              <td><a class="user-link" href="user_card.php?user_id=<?= $row['customer_user_id'] ?>" target="_blank"><?= htmlspecialchars($row['customer_name']) ?></a></td>
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
