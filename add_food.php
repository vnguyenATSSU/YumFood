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
    header("Location: main.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST['item_name']);
    $item_description = trim($_POST['item_description']);
    $item_category = trim($_POST['item_category']);
    $unit_price = trim($_POST['unit_price']);
    
    // Image upload handling
    $target_dir = "./images/"; 
    $target_file = $target_dir . basename($_FILES["item_photo"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allowed file types
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($imageFileType, $allowed_types)) {
        die("Only JPG, JPEG, PNG & GIF files are allowed.");
    }

    if (move_uploaded_file($_FILES["item_photo"]["tmp_name"], $target_file)) {
        $image_path = $target_file;

        // Insert into database
        $sql = "INSERT INTO menu_item (item_name, item_description, item_category, unit_price, item_photo) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssds", $item_name, $item_description, $item_category, $unit_price, $image_path);

        if ($stmt->execute()) {
            echo "<script>alert('Food item added successfully!'); window.location='admin_modify.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Food Item</title>
    <link rel="stylesheet" href="./css/food-style.css">
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
    <h1>Add a New Food Item</h1>
</section>

<main>
    <form action="add_food.php" method="post" enctype="multipart/form-data">
        <label for="item_name">Food Name:</label>
        <input type="text" name="item_name" required>

        <label for="item_description">Description:</label>
        <textarea name="item_description" required></textarea>

        <label for="item_category">Category:</label>
        <select name="item_category" required>
            <option value="Appetizer">Appetizer</option>
            <option value="Main Course">Main Course</option>
            <option value="Dessert">Dessert</option>
            <option value="Drink">Drink</option>
        </select>

        <label for="unit_price">Price:</label>
        <input type="number" name="unit_price" step="0.01" required>

        <label for="item_photo">Upload Image:</label>
        <input type="file" name="item_photo" accept="image/*" required>

        <button type="submit">Add Food Item</button>
    </form>
</main>

</body>
</html>
