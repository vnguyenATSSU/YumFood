<?php
session_start();
require 'connect.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: main.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=Please log in to view your cart.");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$query = "SELECT cart.item_id, menu_item.item_name, menu_item.unit_price, menu_item.item_photo, cart.quantity 
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
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="./css/food-style.css">
    <link rel="stylesheet" href="./css/cart-style.css">
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
    <h1>Your Shopping Cart</h1>
</section>

<main class="cart-container">
    <?php if ($result->num_rows > 0): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                    $item_total = $row['unit_price'] * $row['quantity'];
                    $total_price += $item_total;
                    ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($row['item_photo']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>" class="cart-item-photo"></td>

                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>

                        <td>$<?php echo number_format($row['unit_price'], 2); ?></td>

                        <td>
                            <div class="quantity-controls">
                                <button class="quantity-button" onclick="updateQuantity(<?php echo $row['item_id']; ?>, -1)">➖</button>
                                <span id="quantity-<?php echo $row['item_id']; ?>"><?php echo $row['quantity']; ?></span>
                                <button class="quantity-button" onclick="updateQuantity(<?php echo $row['item_id']; ?>, 1)">➕</button>
                            </div>
                        </td>

                        <td id="total-<?php echo $row['item_id']; ?>" data-price="<?php echo $row['unit_price']; ?>">
                            $<?php echo number_format($item_total, 2); ?>
                        </td>

                        <td><button type="button" class="remove-button" onclick="confirmRemove(<?php echo $row['item_id']; ?>)">Remove</button></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Subtotal: $<span id="grand-total"><?php echo number_format($total_price, 2); ?></span></h3>
            <form action="checkout.php" method="post">
                <button type="submit" class="checkout-button">Proceed to Checkout</button>
            </form>
        </div>

    <?php else: ?>
        <div class="empty-cart-message">
            You don't have any items in your cart yet! Go back to the menu and add some delicious food.
        </div>

        <div class="back-to-menu">
            <a href="maincourse.php" class="back-button">Back to Menu</a>
        </div>

    <?php endif; ?>
</main>

<script>
// Update quantity and total price 
function updateQuantity(itemId, change) {
    let quantityElement = document.getElementById("quantity-" + itemId);
    let totalElement = document.getElementById("total-" + itemId);
    let grandTotalElement = document.getElementById("grand-total");

    let currentQuantity = parseInt(quantityElement.innerText);
    let newQuantity = currentQuantity + change;

    if (newQuantity <= 0) return;

    fetch("update_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "item_id=" + itemId + "&quantity=" + newQuantity
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "Success") {
            quantityElement.innerText = newQuantity;

            let unitPrice = parseFloat(totalElement.getAttribute("data-price"));
            let newTotal = (unitPrice * newQuantity).toFixed(2);
            totalElement.innerText = "$" + newTotal;

            let grandTotal = 0;
            document.querySelectorAll("[id^='total-']").forEach(el => {
                grandTotal += parseFloat(el.innerText.replace("$", ""));
            });
            grandTotalElement.innerText = grandTotal.toFixed(2);
        }
    });
}

// Confirm removal of an item
function confirmRemove(itemId) {
    if (confirm("Are you sure you want to remove this item from your cart?")) {
        fetch("remove_from_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "item_id=" + itemId
        })
        .then(() => location.reload())
        .catch(error => console.error('Error:', error));
    }
}
</script>

</body>
</html>
