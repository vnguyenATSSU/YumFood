<?php
session_start();
require_once "connect.php"; // Ensure this file contains your database connection

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: main.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=Please log in to view your cart.");
    exit();
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
    <link rel="stylesheet" href="./css/order.css">
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
    <h1>Your orders</h1>
</section>

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
