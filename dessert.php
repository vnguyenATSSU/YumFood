<?php
session_start();
require 'connect.php';
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: dessert.php");
    exit();
}

// Fetch all main course items
$query = "SELECT * FROM menu_item WHERE item_category = 'dessert'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Course - Yummi Food</title>
    <link rel="stylesheet" href="food-style.css">
    <style>
        .menu-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 items per row */
            gap: 20px; /* Space between items */
            justify-items: center; /* Center items */
            padding: 20px;
        }
        .menu-item {
            text-align: center;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            width: 100%;
            max-width: 250px; /* Prevent items from being too large */
        }
        .menu-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }
        .price {
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>

<header>
        <div class="logo">
            <img src="./images/logo.png" alt="Yummi Food Logo">
        </div>
        <nav>
            <ul class="nav-center">
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
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="cart.php">🛒 Cart</a></li>
            </ul>
        </nav>
        <div class="user-welcome">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Display Welcome, [Name] dropdown if logged in -->
                <a href="#" class="login-button">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
                <div class="dropdown-content">
                    <a href="?logout=true">Log Out</a>
                </div>
            <?php else: ?>
                <!-- Display Sign In button if not logged in -->
                <a href="index.php" class="sign-in-button">Sign In</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero">
        <h1>Dessert</h1>
        <p>Explore our delicious dessert dishes!</p>
    </section>

    <main>
        <div class="menu-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="menu-item">
                    <img src="<?php echo htmlspecialchars($row['item_photo']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                    <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['item_description']); ?></p>
                    <p class="price">$<?php echo number_format($row['unit_price'], 2); ?></p>
                    <form action="order_sucess.php" method="post">
                        <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                        <input type="hidden" name="category" value="dessert.php"> <!-- Add category -->
                        <button type="submit" class="order-button">Order Now</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

</body>
</html>
