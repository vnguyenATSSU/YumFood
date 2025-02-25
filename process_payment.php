<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=Please log in.");
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve cart items
$query = "SELECT item_id, quantity FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: checkout.php?message=Your cart is empty.");
    exit();
}

// Calculate total price
$total_price = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $item_query = "SELECT unit_price FROM menu_item WHERE item_id = ?";
    $item_stmt = $conn->prepare($item_query);
    $item_stmt->bind_param("i", $row['item_id']);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result()->fetch_assoc();
    
    $total_price += $item_result['unit_price'] * $row['quantity'];
}

// Insert into user_order
$order_query = "INSERT INTO user_order (user_id, order_datetime, total_price) VALUES (?, NOW(), ?)";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param("id", $user_id, $total_price);
$order_stmt->execute();
$order_id = $order_stmt->insert_id; // Get the generated order ID

// Insert each cart item into order_detail
$order_detail_query = "INSERT INTO order_detail (order_id, item_id, quantity) VALUES (?, ?, ?)";
$order_detail_stmt = $conn->prepare($order_detail_query);

foreach ($cart_items as $item) {
    $order_detail_stmt->bind_param("iii", $order_id, $item['item_id'], $item['quantity']);
    $order_detail_stmt->execute();
}

// Clear the cart after placing the order
$clear_cart_query = "DELETE FROM cart WHERE user_id = ?";
$clear_cart_stmt = $conn->prepare($clear_cart_query);
$clear_cart_stmt->bind_param("i", $user_id);
$clear_cart_stmt->execute();

// Redirect to purchase history page
header("Location: purchase_history.php?message=Order placed successfully!");
exit();
?>
