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
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="./css/food-style.css">
</head>
<body>

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

    <section class="hero">
        <h1>Your Shopping Cart</h1>
    </section>

    <main>
        <table>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                $item_total = $row['unit_price'] * $row['quantity'];
                $total_price += $item_total;
                ?>
                <tr>
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
                    <td>
                        <form action="remove_from_cart.php" method="post">
                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                            <button type="button" class="remove-button" onclick="confirmRemove(<?php echo $row['item_id']; ?>)">Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h2>Sub total: $<span id="grand-total"><?php echo number_format($total_price, 2); ?></span></h2>

        <form action="checkout.php" method="post">
            <button type="submit" class="checkout-button">Proceed to Checkout</button>
        </form>
    </main>

    <script>
    function updateQuantity(itemId, change) {
        let quantityElement = document.getElementById("quantity-" + itemId);
        let totalElement = document.getElementById("total-" + itemId);
        let grandTotalElement = document.getElementById("grand-total");

        let currentQuantity = parseInt(quantityElement.innerText);
        let newQuantity = currentQuantity + change;

        if (newQuantity < 1) return;

        // Send the new quantity to update_cart.php
        fetch("update_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "item_id=" + itemId + "&quantity=" + newQuantity
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "Success") {
                // Update the displayed quantity
                quantityElement.innerText = newQuantity;

                // Update item total price
                let unitPrice = parseFloat(totalElement.getAttribute("data-price"));
                let newTotal = (unitPrice * newQuantity).toFixed(2);
                totalElement.innerText = "$" + newTotal;

                // Recalculate Grand Total
                let newGrandTotal = 0;
                document.querySelectorAll("[id^='total-']").forEach(el => {
                    newGrandTotal += parseFloat(el.innerText.replace("$", ""));
                });

                grandTotalElement.innerText = newGrandTotal.toFixed(2);
            } else {
                alert("Error updating quantity");
            }
        })
        .catch(error => console.error("Error:", error));
    }


    function confirmRemove(itemId) {
        if (confirm("Do you want to cancel this item?")) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'item_id=' + itemId
            })
            .then(response => response.text())
            .then(data => {
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        }
    }
    </script>

</body>
</html>
