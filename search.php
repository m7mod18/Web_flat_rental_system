<?php
session_start();
require_once 'database.inc.php';

$userRole = $_SESSION['role'] ?? 'guest';
$userName = $_SESSION['username'] ?? 'Guest';

$query = "
SELECT * FROM flats 
WHERE approved = 1 
AND is_rented = 0
";
$params = [];

if (!empty($_GET['location'])) {
    $query .= " AND location LIKE ?";
    $params[] = "%" . $_GET['location'] . "%";
}
if (!empty($_GET['max_price'])) {
    $query .= " AND monthly_cost <= ?";
    $params[] = $_GET['max_price'];
}
if (!empty($_GET['bedrooms'])) {
    $query .= " AND bedrooms = ?";
    $params[] = $_GET['bedrooms'];
}
if (!empty($_GET['bathrooms'])) {
    $query .= " AND bathrooms = ?";
    $params[] = $_GET['bathrooms'];
}
if (isset($_GET['furnished']) && $_GET['furnished'] !== '') {
    $query .= " AND is_furnished = ?";
    $params[] = $_GET['furnished'];
}

$sort_column = $_GET['sort'] ?? 'flat_id';
$sort_order = $_GET['order'] ?? 'desc';
$valid_columns = ['ref_number', 'monthly_cost', 'available_from', 'bedrooms', 'is_furnished'];
$sort_column = in_array($sort_column, $valid_columns) ? $sort_column : 'flat_id';
$sort_order = strtolower($sort_order) === 'asc' ? 'asc' : 'desc';

$query .= " ORDER BY $sort_column $sort_order";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$flats = $stmt->fetchAll();

function sort_link($column, $label, $currentSort, $currentOrder) {
    $icon = '';
    $newOrder = 'asc';
    if ($currentSort === $column) {
        if ($currentOrder === 'asc') {
            $icon = ' ▲';
            $newOrder = 'desc';
        } else {
            $icon = ' ▼';
            $newOrder = 'asc';
        }
    }
    return "<a href=\"?" . http_build_query(array_merge($_GET, ['sort' => $column, 'order' => $newOrder])) . "\">$label$icon</a>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Flats</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="logo">
    <img src="images/logo.png" alt="Logo">
    <h1><a href="home.php" class="site-title">Birzeit Flat Rent</a></h1>
  </div>
  <div class="top-links">
    <a href="about.php">About Us</a>
    <?php if ($userRole === 'guest'): ?>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    <?php else: ?>
      <div class="user-card">
        <img src="images/user_defult.png" alt="User" height="30">
        <span><?= htmlspecialchars($userName) ?></span>
        <a href="logout.php">Logout</a>
      </div>
    <?php endif; ?>
  </div>
</header>

<div class="layout-container">
  <nav class="sidebar">
    <ul>
      <li><a href="home.php" class="<?= basename($_SERVER['PHP_SELF']) === 'home.php' ? 'active' : '' ?>">Home</a></li>
      <?php if (in_array($userRole, ['guest', 'customer', 'owner'])): ?>
        <li><a href="search.php" class="active">Search Flats</a></li>
      <?php endif; ?>
      <?php if ($userRole === 'customer'): ?>
        <li><a href="view_rented.php">View Rented Flats</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php elseif ($userRole === 'owner'): ?>
        <li><a href="add_flat.php">Add Flat</a></li>
        <li><a href="appointments.php">Appointments</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php elseif ($userRole === 'manager'): ?>
        <li><a href="pending_flats.php">Approve Flats</a></li>
        <li><a href="manager_inquiry.php">Manager Search Flats</a></li>
        <li><a href="messages.php">Messages</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <main class="search-page-main">
    <section class="search-hero-section">
      <form method="get" class="search-form">
        <h2>Find Your Perfect Flat</h2>
        <input type="text" name="location" placeholder="Location" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
        <input type="number" name="max_price" placeholder="Max Price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
        <input type="number" name="bedrooms" placeholder="Bedrooms" value="<?= htmlspecialchars($_GET['bedrooms'] ?? '') ?>">
        <input type="number" name="bathrooms" placeholder="Bathrooms" value="<?= htmlspecialchars($_GET['bathrooms'] ?? '') ?>">
        <select name="furnished">
          <option value="">Furnished?</option>
          <option value="1" <?= (($_GET['furnished'] ?? '') === "1") ? "selected" : "" ?>>Yes</option>
          <option value="0" <?= (($_GET['furnished'] ?? '') === "0") ? "selected" : "" ?>>No</option>
        </select>
        <button type="submit">Search</button>
      </form>
    </section>

    <section class="search-main-content">
      <h2 class="search-title">Available Flats for Rent</h2>
      <?php if (count($flats) === 0): ?>
        <p class="search-no-results">No flats match your search.</p>
      <?php else: ?>
        <table class="search-table">
          <thead>
            <tr>
              <th><?= sort_link('ref_number', 'Flat Reference', $sort_column, $sort_order) ?></th>
              <th><?= sort_link('monthly_cost', 'Monthly Cost', $sort_column, $sort_order) ?></th>
              <th><?= sort_link('available_from', 'Availability', $sort_column, $sort_order) ?></th>
              <th>Location</th>
              <th><?= sort_link('bedrooms', 'Bedrooms', $sort_column, $sort_order) ?></th>
              <th><?= sort_link('is_furnished', 'Furnished', $sort_column, $sort_order) ?></th>
              <th>Photo</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($flats as $flat): ?>
              <tr>
                <td><?= htmlspecialchars($flat['ref_number']) ?></td>
                <td>₪<?= htmlspecialchars($flat['monthly_cost']) ?></td>
                <td><?= htmlspecialchars($flat['available_from']) ?></td>
                <td><?= htmlspecialchars($flat['location']) ?></td>
                <td><?= htmlspecialchars($flat['bedrooms']) ?></td>
                <td><?= $flat['is_furnished'] ? 'Yes' : 'No' ?></td>
                <td class="search-flat-photo-cell">
                  <div class="search-photo-hint">Click photo for details</div>
                  <?php
                    $stmt = $pdo->prepare("SELECT photo_url FROM flat_photos WHERE flat_id = ? LIMIT 1");
                    $stmt->execute([$flat['flat_id']]);
                    $photo = $stmt->fetchColumn();
                    $img = $photo ?: "images/no_image.png";
                  ?>
                  <a href="flat_details.php?flat_id=<?= $flat['flat_id'] ?>"><img src="<?= $img ?>" alt="Flat Photo"></a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
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
