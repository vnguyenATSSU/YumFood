<?php
session_start();
require 'connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: main.php");
    exit();
}

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission
    $item_name = $_POST['item_name'];
    $item_description = $_POST['item_description'];
    $item_category = $_POST['item_category'];
    $unit_price = $_POST['unit_price'];

    $sql = "UPDATE menu_item SET item_name = ?, item_description = ?, item_category = ?, unit_price = ? WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdi", $item_name, $item_description, $item_category, $unit_price, $item_id);
    
    if ($stmt->execute()) {
        $success_message = "Food item updated successfully!";
    } else {
        $error_message = "Error updating food item: " . $conn->error;
    }
}

// Fetch the current food item details
$sql = "SELECT * FROM menu_item WHERE item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$food_item = $result->fetch_assoc();

if (!$food_item) {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Food Item</title>
    <link rel="stylesheet" href="./css/food-style.css">
    <style>
        form {
            max-width: 500px;
            margin: 0 auto;
        }
        label, input, textarea, select {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
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
        <h1>Edit Food Item</h1>
    </section>

    <main>
        <?php
        if (isset($success_message)) {
            echo "<p style='color: green;'>$success_message</p>";
        }
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        ?>

        <form action="edit_food.php?id=<?php echo $item_id; ?>" method="post">
            <label for="item_name">Food Name:</label>
            <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($food_item['item_name']); ?>" required>

            <label for="item_description">Description:</label>
            <textarea id="item_description" name="item_description" required><?php echo htmlspecialchars($food_item['item_description']); ?></textarea>

            <label for="item_category">Category:</label>
            <select id="item_category" name="item_category" required>
                <option value="appetizer" <?php if ($food_item['item_category'] == 'appetizer') echo 'selected'; ?>>Appetizer</option>
                <option value="main course" <?php if ($food_item['item_category'] == 'main course') echo 'selected'; ?>>Main Course</option>
                <option value="dessert" <?php if ($food_item['item_category'] == 'dessert') echo 'selected'; ?>>Dessert</option>
                <option value="drink" <?php if ($food_item['item_category'] == 'drink') echo 'selected'; ?>>Drink</option>
            </select>

            <label for="unit_price">Price:</label>
            <input type="number" id="unit_price" name="unit_price" step="0.01" value="<?php echo htmlspecialchars($food_item['unit_price']); ?>" required>

            <input type="submit" value="Update Food Item">
        </form>
    </main>

    
</body>
</html>
