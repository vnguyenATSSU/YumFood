<?php
session_start();
require_once "connect.php"; // Ensure this file contains your database connection

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php?message=Please log in to view your cart.");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch grouped orders by order_id
$query = "
    SELECT o.order_id, o.order_datetime, 
           SUM(m.unit_price * od.quantity) AS total_price
    FROM user_order o
    JOIN order_detail od ON o.order_id = od.order_id
    JOIN menu_item m ON od.item_id = m.item_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_datetime DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yummi Food - Purchase History</title>
    <link rel="stylesheet" href="./css/food-style.css">
    <link rel="stylesheet" href="order.css">
    <style>
        .order-header {
            background-color: #f8f8f8;
            padding: 10px;
            border: 1px solid #ddd;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: background-color 0.3s;
        }
        .order-header:hover {
            background-color: #e0e0e0;
        }
        .order-details {
            display: none;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 8px;
            background-color: #fafafa;
        }
        .order-info {
            margin-left: 10px;
        }
        .toggle-button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .toggle-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<header>
    <nav class="nav-left">
        <ul>
            <li><a href="index.php">Home</a></li>
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
                <a href="signup.php" class="sign-in-button">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<section class="hero">
    <h1>Your Orders</h1>
</section>

<div class="purchase-history-container">
    <?php if ($orders->num_rows > 0): ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <div class="order-box">
                <div class="order-header" onclick="toggleOrderDetails(<?php echo $order['order_id']; ?>)">
                    <span>Order Date: <?php echo date("F j, Y", strtotime($order['order_datetime'])); ?></span>
                    <span>Total Price: $<?php echo number_format($order['total_price'], 2); ?></span>
                </div>
                <div class="order-details" id="order-<?php echo $order['order_id']; ?>">

                    <?php
                    // Fetch full order details for the current order_id
                    $details_query = "
                        SELECT m.item_name, m.item_photo, od.quantity,
                               (m.unit_price * od.quantity) AS total_item_price, od.item_id
                        FROM order_detail od
                        JOIN menu_item m ON od.item_id = m.item_id
                        WHERE od.order_id = ?";
                    
                    $detail_stmt = $conn->prepare($details_query);
                    $detail_stmt->bind_param("i", $order['order_id']);
                    $detail_stmt->execute();
                    $details = $detail_stmt->get_result();

                    while ($row = $details->fetch_assoc()): ?>
                        <div class="order-body">
                            <img src="<?php echo htmlspecialchars($row['item_photo']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>" class="order-image" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                            <div class="order-info">
                                <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                                <p>Price: $<?php echo number_format($row['total_item_price'], 2); ?> (x<?php echo $row['quantity']; ?>)</p>
                                <form action="order_detail.php" method="post" style="display:inline;">
                                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                    <button type="submit" class="buy-again-button">
                                        Buy it again <img src="./images/buy_it_again.png" alt="Cart Icon">
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>

                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; color: red;">No purchase history found.</p>
    <?php endif; ?>
</div>

<script>
function toggleOrderDetails(orderId) {
    var details = document.getElementById("order-" + orderId);
    details.style.display = (details.style.display === "block") ? "none" : "block";
}
</script>

</body>
</html>
