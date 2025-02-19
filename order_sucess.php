<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=Please log in before making an order.");
    exit();
}

require 'connect.php';

date_default_timezone_set("America/New_York"); 

// Ensure item_id is received
if (!isset($_POST['item_id'])) {
    header("Location: maincourse.php?message=Invalid order.");
    exit();
}

$item_id = $_POST['item_id'];

// Fetch item details
$query = "SELECT item_name, unit_price FROM menu_item WHERE item_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();



$item = $result->fetch_assoc();
$current_datetime = date("Y-m-d H:i:s"); // Get correct date and time
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail information</title>
    <link rel="stylesheet" href="food-style.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="./images/logo.png" alt="Yummi Food Logo">
    </div>
    <nav>
        <ul class="nav-center">
            <li><a href="main.php">Home</a></li>
            <li><a href="maincourse.php">Menu</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>
</header>

<section class="hero">
    <h1></h1>
    <p></p>
</section>

<main>
    <div class="order-container">
        <h2><?php echo htmlspecialchars($item['item_name']); ?></h2>
        <p>Price: $<?php echo number_format($item['unit_price'], 2); ?></p>
        <p>Order Date & Time: <?php echo $current_datetime; ?></p>

        <form action="process_order.php" method="post">
            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
            
            <label for="quantity">Quantity:</label>
            <select name="quantity" id="quantity">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit" class="order-button">Add</button>
        </form>
    </div>
</main>

</body>
</html>
