<?php
session_start();
require_once "connect.php"; // Ensure this file contains your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your purchase history.");
}

$user_id = $_SESSION['user_id'];

$query = "SELECT o.order_datetime, u.first_name, u.last_name, 
                 m.item_name, m.item_photo, od.quantity, od.item_id, 
                 (m.unit_price * od.quantity) AS total_item_price
          FROM user_order o
          JOIN order_detail od ON o.order_id = od.order_id
          JOIN menu_item m ON od.item_id = m.item_id
          JOIN user u ON o.user_id = u.user_id
          WHERE o.user_id = ? 
          ORDER BY o.order_datetime DESC";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yummi Food - Purchase History</title>
    <link rel="stylesheet" href="./css/food-style.css">
    <link rel="stylesheet" href="order.css">
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

    <div class="purchase-history-container">

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="order-box">
                    <div class="order-header">
                        <span>Order Date: <?php echo date("F j, Y", strtotime($row['order_datetime'])); ?></span>
                    </div>
                    <div class="order-body">
                        <img src="<?php echo htmlspecialchars($row['item_photo']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>" class="order-image" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                        <div class="order-info">
                            <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                            <p>Price: $<?php echo number_format($row['total_item_price'], 2); ?> (x<?php echo $row['quantity']; ?>)</p>
                            <p>Ship To: <?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></p>
                            <form action="order_detail.php" method="post" style="display:inline;">
                                <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                <button type="submit" class="buy-again-button">
                                    Buy it again <img src="./images/buy_it_again.png" alt="Cart Icon">
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; color: red;">No purchase history found.</p>
        <?php endif; ?>
    </div>

</body>
</html>
