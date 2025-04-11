<?php
session_start();
require 'connect.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: main.php");
    exit();
}

// Handle thumbs up/down
if (isset($_POST['thumbs_action']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];
    $action = $_POST['thumbs_action'];

    // Check if the user has already voted on this item
    $check_vote = $conn->prepare("SELECT vote_type FROM user_votes WHERE user_id = ? AND item_id = ?");
    $check_vote->bind_param("ii", $user_id, $item_id);
    $check_vote->execute();
    $result = $check_vote->get_result();

    if ($result->num_rows > 0) {
        $previous_vote = $result->fetch_assoc()['vote_type'];
        if ($previous_vote === $action) {
            // User is removing their vote
            $conn->query("DELETE FROM user_votes WHERE user_id = $user_id AND item_id = $item_id");
            $conn->query("UPDATE menu_item SET thumbs_$action = thumbs_$action - 1 WHERE item_id = $item_id");
        } else {
            // User is changing their vote
            $conn->query("UPDATE user_votes SET vote_type = '$action' WHERE user_id = $user_id AND item_id = $item_id");
            $conn->query("UPDATE menu_item SET thumbs_$previous_vote = thumbs_$previous_vote - 1, thumbs_$action = thumbs_$action + 1 WHERE item_id = $item_id");
        }
    } else {
        // User is voting for the first time
        $conn->query("INSERT INTO user_votes (user_id, item_id, vote_type) VALUES ($user_id, $item_id, '$action')");
        $conn->query("UPDATE menu_item SET thumbs_$action = thumbs_$action + 1 WHERE item_id = $item_id");
    }

    // Redirect to prevent form resubmission
    header("Location: main.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yummi Food</title>
    <link rel="stylesheet" href="./css/food-style.css">
    <style>
        .thumbs-container {
            display: flex;
            justify-content: space-between;
            width: 120px;
            margin-top: 10px;
        }
        .thumbs-button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2em;
        }
        .thumbs-button.voted {
            color: #007bff;
            font-weight: bold;
        }
        .menu-item img {
            max-width: 100%;
            height: auto;
        }
        .menu-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<header>
    <nav class="nav-left">
        <ul>
            <li><a href="main.php">Home</a></li>
            <li class="dropdown">
                <a href="#">Menu ▼</a>
                <ul class="dropdown-content">
                    <li><a href="maincourse.php">Main Course</a></li>
                    <li><a href="appetizer.php">Appetizer</a></li>
                    <li><a href="dessert.php">Dessert</a></li>
                    <li><a href="drink.php">Drink</a></li>
                </ul>
            </li>
            <li><a href="aboutus.php">About us</a></li>
        </ul>
    </nav>

    <div class="logo">
        <a href="aboutus.php">
            <img src="./images/logo1.png" alt="Yummi Food Logo">
        </a>
    </div>

    <nav class="nav-right">
        <ul>
            <li><a href="purchase_history.php">Orders</a></li>
            <li><a href="cart.php">🛒 Cart</a></li>
        </ul>
        <div class="user-welcome">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" class="login-button">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
                <div class="dropdown-content">
                    <a href="edit_profile.php">Edit Profile</a>
                    <a href="?logout=true">Log Out</a>
                </div>
            <?php else: ?>
                <a href="index.php" class="sign-in-button">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>

</header>

<section class="hero">
    <h1>Welcome to Yummi Food</h1>
    <p>Delicious meals, great flavors, and fresh ingredients!</p>
</section>

<main>
    
    <div class="menu">
        <?php
        $query = "SELECT * FROM menu_item";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='menu-item'>";
                echo "<img src='" . htmlspecialchars($row['item_photo']) . "' alt='" . htmlspecialchars($row['item_name']) . "'>";
                echo "<h3>" . htmlspecialchars($row['item_name']) . "</h3>";
                echo "<p>" . htmlspecialchars($row['item_description']) . "</p>";
                
                // Thumbs up and down buttons
                echo "<div class='thumbs-container'>";
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $item_id = $row['item_id'];
                    $user_vote_query = $conn->query("SELECT vote_type FROM user_votes WHERE user_id = $user_id AND item_id = $item_id");
                    $user_vote = $user_vote_query->num_rows > 0 ? $user_vote_query->fetch_assoc()['vote_type'] : null;

                    $up_class = ($user_vote === 'up') ? 'voted' : '';
                    $down_class = ($user_vote === 'down') ? 'voted' : '';

                    echo "<form method='post'>";
                    echo "<input type='hidden' name='item_id' value='" . $row['item_id'] . "'>";
                    echo "<button type='submit' name='thumbs_action' value='up' class='thumbs-button $up_class'>👍 " . $row['thumbs_up'] . "</button>";
                    echo "</form>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='item_id' value='" . $row['item_id'] . "'>";
                    echo "<button type='submit' name='thumbs_action' value='down' class='thumbs-button $down_class'>👎 " . $row['thumbs_down'] . "</button>";
                    echo "</form>";
                } else {
                    echo "<p>👍 " . $row['thumbs_up'] . " 👎 " . $row['thumbs_down'] . "</p>";
                }
                echo "</div>";
                
                echo "</div>";
            }
        } else {
            echo "<p>No menu items available.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
