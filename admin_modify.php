<?php
session_start();
require 'connect.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: main.php");
    exit();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: main.php"); // Redirect non-admin users
    exit();
}

// Fetch all menu items to display
$sql = "SELECT item_id, item_name, item_description, item_category, unit_price, item_photo FROM menu_item";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit food - Yummi Food</title>
    <link rel="stylesheet" href="./css/food-style.css">
    <style>
        .menu-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .menu-item {
            width: 300px;
            margin: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
        .menu-item img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .menu-item .actions {
            margin-top: 10px;
        }
        .menu-item .actions a {
            display: inline-block;
            margin: 0 5px;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
        
    </style>
</head>
<body>

<header>
    <nav class="nav-left">
        <ul>
            <li><a href="admin_modify.php">Edit Food</a></li>
            <li><a href="add_food.php">Add Food</a></li>
            <li><a href="delete_food.php">Delete Food</a></li>
        </ul>
    </nav>

    <div class="logo">
        <a href="admin_dashboard.php">
            <img src="./images/logo1.png" alt="Yummi Food Logo">
        </a>
    </div>

    <nav class="nav-right">
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
    <h1>Edit Food</h1>
    <p>Manage Your Menu Items</p>
</section>

<main>
    <div class="menu-container">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="menu-item">';
                echo '<h3>'.htmlspecialchars($row["item_name"]).'</h3>';
                echo '<img src="'.htmlspecialchars($row["item_photo"]).'" alt="'.htmlspecialchars($row["item_name"]).'">';
                echo '<p>Category: '.htmlspecialchars($row["item_category"]).'</p>';
                echo '<p>Price: $'.number_format($row["unit_price"], 2).'</p>';
                echo '<p>'.htmlspecialchars(substr($row["item_description"], 0, 100)).'...</p>';
                echo '<div class="actions">';
                echo '<a href="edit_food.php?id='.htmlspecialchars($row["item_id"]).'">Edit</a>';
                
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<p>No food items found.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
