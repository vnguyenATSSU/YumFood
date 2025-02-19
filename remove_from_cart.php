<?php
session_start();
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id'])) {
    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];

    // Delete item from cart
    $query = "DELETE FROM cart WHERE user_id = ? AND item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $item_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
