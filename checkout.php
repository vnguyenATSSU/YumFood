<?php
session_start();
require 'connect.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: maincourse.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=Please log in to proceed to checkout.");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$query = "SELECT cart.item_id, menu_item.item_name, menu_item.unit_price, cart.quantity 
          FROM cart 
          JOIN menu_item ON cart.item_id = menu_item.item_id 
          WHERE cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Yummi Food</title>
    <link rel="stylesheet" href="./css/food-style.css">
    <link rel="stylesheet" href="designcard.css"> <!-- Payment UI styling -->
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
    <h1>Checkout</h1>
</section>

<main class="checkout-container">
    <!-- Order Summary -->
    <div class="cart-summary">
        <h2>Your Order</h2>
        <div class="order-items">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                $item_total = $row['unit_price'] * $row['quantity'];
                $total_price += $item_total;
                ?>
                <div class="order-item">
                    <span class="item-name"><?php echo htmlspecialchars($row['item_name']); ?></span>
                    <span class="item-quantity">x<?php echo $row['quantity']; ?></span>
                    <span class="item-price">$<?php echo number_format($item_total, 2); ?></span>
                </div>
            <?php endwhile; ?>
        </div>
        <h3 class="order-total">Total: $<?php echo number_format($total_price, 2); ?></h3>
    </div>

    <!-- Payment Details -->
    <div class="payment-box">
        <h2>Payment Details</h2>
        
        <div class="card-icons">
            <img src="./images/visa.png" alt="Visa">
            <img src="./images/mastercard.png" alt="MasterCard">
            <img src="./images/paypal.png" alt="PayPal">
        </div>

        <form action="process_payment.php" method="post">
            <label>Card Number</label>
            <input type="text" name="card_number" placeholder="1234 5678 9101 1121" required>

            <div class="flex-row">
                <div>
                    <label>MM/YY</label>
                    <input type="text" name="expiry_date" placeholder="MM/YY" required>
                </div>
                <div>
                    <label>CVV</label>
                    <input type="text" name="cvv" placeholder="123" required>
                </div>
            </div>

            <div class="flex-row">
                <div>
                    <label>First Name</label>
                    <input type="text" name="first_name" placeholder="" required>
                </div>
                <div>
                    <label>Last Name</label>
                    <input type="text" name="last_name" placeholder="" required>
                </div>
            </div>
            <div class="checkout-buttons">
                <button type="button" class="cancel-button" onclick="window.location.href='cart.php'">Cancel</button>
                <button type="submit" class="pay-button">Payment</button>
            </div>

        </form>
    </div>
</main>

</body>
</html>
