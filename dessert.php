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
    <link rel="stylesheet" href="./css/food-style.css">
</head>
<body>
    <header>
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

        <div class="logo">
            <a href="aboutus.php">
                <img src="./images/logo1.png" alt="Yummi Food Logo">
            </a>
        </div>

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

    <section class="hero">
        <h1>Dessert</h1>
        <p>Explore our delicious dessert dishes!</p>
    </section>

<main>
    <div class="menu-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="menu-item">
                    <img src="<?php echo htmlspecialchars($row['item_photo']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                    <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['item_description']); ?></p>
                    <p class="price">$<?php echo number_format($row['unit_price'], 2); ?></p>
                    
                    <form action="order_detail.php" method="post">
                        <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                        <button type="submit" class="order-button">Order Now</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No dessert items available.</p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
