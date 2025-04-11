<?php
session_start();
require 'connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: main.php"); 
    exit();
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id'])) {
    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);

    if ($item_id !== false) {
        
        $sql = "DELETE FROM menu_item WHERE item_id = ?";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $item_id);
            if ($stmt->execute()) {
                echo "<script>
                        alert('Food item deleted successfully.');
                        window.location.href='delete_food.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Error deleting food item: " . $conn->error . "');
                        window.location.href='delete_food.php';
                      </script>";
            }
            $stmt->close();
        } else {
            echo "<script>
                    alert('Error preparing statement: " . $conn->error . "');
                    window.location.href='delete_food.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Invalid item ID.');
                window.location.href='delete_food.php';
              </script>";
    }
}

// Fetch all menu items to display
$sql = "SELECT item_id, item_name, item_photo FROM menu_item";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Food Item</title>
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

        .delete-form {
            margin-top: 10px;
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
            <a href="admin_modify.php">
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
        <h1>Delete Food Item</h1>
    </section>

    <div class="menu-container">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="menu-item">';
                echo '<h3>'.htmlspecialchars($row["item_name"]).'</h3>';
                echo '<img src="'.htmlspecialchars($row["item_photo"]).'" alt="'.htmlspecialchars($row["item_name"]).'">';
                echo '<form class="delete-form" method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                echo '<input type="hidden" name="item_id" value="'.htmlspecialchars($row["item_id"]).'">';
                echo '<button type="submit" class="order-button">Delete</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo "No food items found.";
        }
        ?>
    </div>

</body>
</html>
