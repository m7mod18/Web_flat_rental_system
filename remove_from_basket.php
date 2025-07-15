<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$flat_id = $_POST['flat_id'] ?? null;

if ($flat_id && isset($_SESSION['basket'])) {
    $_SESSION['basket'] = array_filter($_SESSION['basket'], function ($id) use ($flat_id) {
        return $id != $flat_id;
    });
}

header("Location: basket.php");
exit();
