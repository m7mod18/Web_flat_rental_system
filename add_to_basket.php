<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$flat_id = $_POST['flat_id'] ?? null;

if ($flat_id) {
    if (!isset($_SESSION['basket'])) {
        $_SESSION['basket'] = [];
    }
    if (!in_array($flat_id, $_SESSION['basket'])) {
        $_SESSION['basket'][] = $flat_id;
    }
}

header("Location: basket.php");
exit();
