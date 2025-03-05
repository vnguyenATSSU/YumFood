<?php
session_start();
require 'connect.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: main.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yummi Food</title>
    <link rel="stylesheet" href="./css/food-style.css">
</head>
<body>

    <!-- Header -->
<header>
    <!-- Left Side Navigation -->
    <nav class="nav-left">
        <ul>
            <li><a href="main.php">Home</a></li>
            <li class="dropdown">
                <a href="#">Menu ▼</a>
                <ul class="dropdown-content">
                    <li><a href="maincourse.php">Main Course</a></li>
                    <li><a href="appetizer.php">Appetizer</a></li>
                    <li><a href="dessert.php">Dessert</a></li>
                    <li><a href="drink.php">Drink</a></li>
                </ul>
            </li>
            <li><a href="aboutus.php">About us</a></li>
        </ul>
    </nav>

    <!-- Center Logo -->
    <div class="logo">
        <a href="aboutus.php">
            <img src="./images/logo1.png" alt="Yummi Food Logo">
        </a>
    </div>

    <!-- Right Side Navigation -->
    <nav class="nav-right">
        <ul>
            <li><a href="purchase_history.php">Orders</a></li>
            <li><a href="cart.php">🛒 Cart</a></li>
        </ul>
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


    <!-- Cool Design Below Navigation -->
    <section class="hero">
        <h1>Welcome to Yummi Food</h1>
        <p>Delicious meals, great flavors, and fresh ingredients!</p>
    </section>

    <!-- Main Content -->
    <main>
        <h2>Our Menu</h2>
        <div class="menu">
            <?php
            $query = "SELECT * FROM menu_item";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='menu-item'>";
                    echo "<img src='" . $row['item_photo'] . "' alt='" . $row['item_name'] . "'>";
                    echo "<h3>" . $row['item_name'] . "</h3>";
                    echo "<p>" . $row['item_description'] . "</p>";
                    echo "<p class='price'>$" . number_format($row['unit_price'], 2) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No menu items available.</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>