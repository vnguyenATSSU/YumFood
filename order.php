<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=" . urlencode("Please log in before making an order."));
    exit();
}

header("Location: order_sucess.php");
exit();
?>
