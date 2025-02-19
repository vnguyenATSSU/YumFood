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
    <link rel="stylesheet" href="food-style.css">
</head>
<body>

    <header> <!-- Fixed the extra `<` issue -->
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
                <div class="user-dropdown">
                    <a href="#" class="login-button">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
                    <div class="dropdown-content">
                        <a href="?logout=true">Log Out</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="index.php" class="sign-in-button">Sign In</a>
            <?php endif; ?>
        </div>
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
                    <td><?php echo $row['quantity']; ?></td>
                    <td>$<?php echo number_format($item_total, 2); ?></td>
                    <td>
                        <form action="remove_from_cart.php" method="post">
                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                            <button type="button" class="remove-button" onclick="confirmRemove(<?php echo $row['item_id']; ?>)">Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h2>Total: $<?php echo number_format($total_price, 2); ?></h2>

        <form action="checkout.php" method="post">
            <button type="submit" class="checkout-button">Proceed to Checkout</button>
        </form>
    </main>

    <script>
    function confirmRemove(itemId) {
        // Show confirmation popup
        if (confirm("Do you want to cancel this item?")) {
            // If user clicks "Yes", send a request to remove the item
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'item_id=' + itemId
            })
            .then(response => response.text())
            .then(data => {
                // Reload the page to reflect the changes
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        }
    }
    </script>


</body>
</html>
