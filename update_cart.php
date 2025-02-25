<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "Error: Not logged in";
    exit();
}

if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $user_id = $_SESSION['user_id'];
    $item_id = intval($_POST['item_id']);
    $new_quantity = intval($_POST['quantity']);

    if ($new_quantity < 1) {
        echo "Error: Invalid quantity";
        exit();
    }

    // Update the quantity in the cart table
    $query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $new_quantity, $user_id, $item_id);
    
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error updating cart";
    }
    
    $stmt->close();
} else {
    echo "Error: Missing data";
}
?>
