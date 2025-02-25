<?php
session_start();
require 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=Please log in before making an order.");
    exit();
}

// Validate input data
if (!isset($_POST['item_id']) || !isset($_POST['quantity']) || empty($_POST['item_id']) || empty($_POST['quantity'])) {
    header("Location: order_detail.php?message=Invalid order.");
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = intval($_POST['item_id']);
$quantity = intval($_POST['quantity']);

if ($quantity <= 0) {
    header("Location: order_detail.php?message=Invalid quantity.");
    exit();
}

// Check if the item is already in the cart
$query = "SELECT quantity FROM cart WHERE user_id = ? AND item_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("ii", $user_id, $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing cart quantity
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    
    $stmt->close(); // Close previous statement before reusing

    $update_query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND item_id = ?";
    $stmt = $conn->prepare($update_query);
    if (!$stmt) {
        die("Error preparing update statement: " . $conn->error);
    }
    $stmt->bind_param("iii", $new_quantity, $user_id, $item_id);
} else {
    // Insert new item into the cart
    $stmt->close(); // Close previous statement before reusing
    
    $insert_query = "INSERT INTO cart (user_id, item_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    if (!$stmt) {
        die("Error preparing insert statement: " . $conn->error);
    }
    $stmt->bind_param("iii", $user_id, $item_id, $quantity);
}

// Execute the query
if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}
$stmt->close();

// Redirect to cart.php with success message
header("Location: cart.php?message=Item added to cart successfully");
exit();
?>
