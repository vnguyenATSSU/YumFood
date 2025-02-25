<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=Please log in before making an order.");
    exit();
}

require 'connect.php';

date_default_timezone_set("America/New_York");

if (!isset($_POST['item_id'])) {
    header("Location: maincourse.php?message=Invalid order.");
    exit();
}

$item_id = $_POST['item_id']; // Use POST instead of GET


// Fetch item details
$query = "SELECT item_name, unit_price FROM menu_item WHERE item_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

// If item not found, redirect
if (!$item) {
    header("Location: main.php?message=Item not found.");
    exit();
}

$current_datetime = date("Y-m-d H:i:s");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="./css/food-style.css">
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
            <li><a href="purchase_history.php">Orders</a></li>
            <li><a href="cart.php">🛒 Cart</a></li>
        </ul>
    </nav>
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
</header>

<section class="hero">
    <h1>Order Details</h1>
    <p>Review your order before proceeding.</p>
</section>

<main>
    <div class="order-container">
        <h2><?php echo htmlspecialchars($item['item_name']); ?></h2>
        <p>Price: $<?php echo number_format($item['unit_price'], 2); ?></p>
        <p>Order Date & Time: <?php echo $current_datetime; ?></p>

        <form action="process_order.php" method="post">
            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
            
            <label for="quantity">Quantity:</label>
            <select name="quantity" id="quantity">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit" class="order-button">Add to Cart</button>
        </form>
    </div>
</main>

</body>
</html>
