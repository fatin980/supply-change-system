<?php
session_start();
include 'config.php'; // Database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $_SESSION['username'] = $user['username']; // Store username in session
    $_SESSION['role'] = $user['role']; // Store role in session
}

$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'user';

// Fetch unread notifications (purchase order submissions)
$notif_query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE status = 'unread'";
$notif_result = $conn->query($notif_query);
$notif_row = $notif_result->fetch_assoc();
$unread_count = $notif_row['unread_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Change System</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Custom CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
            <div class="logo">Supply Change System</div>
        </div>
        <div class="nav-icons">
            <span class="notification-icon">
                <i class="fas fa-bell"></i> 
                <span class="notif-count"><?php echo $unread_count; ?></span>
            </span>
            <a href="logout.php" class="logout-icon">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="products.php">Product</a></li>
            <li><a href="suppliers.php">Suppliers / Vendors</a></li>
            <li><a href="deliver_to.php">Deliver To</a></li>
            <li><a href="shipping_terms.php">Shipping Terms</a></li>
            <li><a href="purchase_order.php">Purchase Order</a></li>
            <li><a href="report.php">Report</a></li>

            <?php if ($role === 'admin') { ?>
                <li class="menu-divider"></li> <!-- Divider for separation -->
                <li class="menu-title">Admin Settings</li> <!-- Title above settings -->
                <li><a href="settings.php">Settings</a></li>
            <?php } ?>
        </ul>
    </div>
