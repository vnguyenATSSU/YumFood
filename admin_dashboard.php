<?php
session_start();
require 'connect.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: main.php");
    exit();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: main.php"); // Redirect non-admin users
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./css/food-style.css"> <!-- Link to an admin-specific CSS file -->
</head>
<body>

<header>
    <!-- Left Side Navigation -->
    <nav class="nav-left">
        <ul>
            <li><a href="add_food.php">Add food</a></li>
        </ul>
    </nav>

    <!-- Center Logo -->
    <div class="logo">
        <a href="admin_dashboard.php">
            <img src="./images/logo1.png" alt="Yummi Food Logo">
        </a>
    </div>

    <!-- Right Side Navigation -->
    <nav class="nav-right">
        <div class="user-welcome">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" class="login-button">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
                <div class="dropdown-content">
                    <a href="?logout=true">Log Out</a>
                </div>
            <?php else: ?>
                <a href="index.php" class="sign-in-button">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<section class="hero">
    <h1>Admin dashboard</h1>
</section>

</body>
</html>
