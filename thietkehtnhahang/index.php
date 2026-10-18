<?php
// index.php - Chuyển hướng thông minh vào hệ thống
session_start();
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    if ($role === 'admin') {
        header('Location: dashboard.php');
        exit;
    } elseif ($role === 'reception') {
        header('Location: reservations.php');
        exit;
    } elseif ($role === 'kitchen') {
        header('Location: kds.php');
        exit;
    } else {
        // waiter & cashier
        header('Location: tables.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
