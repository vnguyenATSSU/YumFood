<?php
session_start();
require 'connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=Please log in first.");
    exit();
}

$user_id = $_SESSION['user_id'];

// DELETE all cart items after order is placed
$delete_cart = "DELETE FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($delete_cart);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$stmt->close();
$conn->close();

// Redirect to homepage after processing order
header("Location: main.php");
exit();
?>
