<?php
session_start();
require 'connect.php';


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: maincourse.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?message=Please log in to proceed to checkout.");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$query = "SELECT cart.item_id, menu_item.item_name, menu_item.unit_price, cart.quantity 
          FROM cart 
          JOIN menu_item ON cart.item_id = menu_item.item_id 
          WHERE cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;

// Initialize error messages array and form variables
$errors = [];
$card_number = $expiry_date = $cvv = $first_name = $last_name = '';

// Validation functions
function validateCardNumber($cardNumber) {
    // Remove all spaces from the card number
    $cardNumber = str_replace(' ', '', $cardNumber);
    
    // Check if it's exactly 16 digits
    if (!preg_match('/^\d{16}$/', $cardNumber)) {
        return false;
    }
    
    return true;
}

function validateExpiryDate($expiryDate) {
    // Check if the format is MM/YY
    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiryDate)) {
        return false;
    }

    // Get current month and year
    $currentMonth = (int)date('m');
    $currentYear = (int)date('y');

    // Split expiry date into month and year
    [$expMonth, $expYear] = explode('/', $expiryDate);
    $expMonth = (int)$expMonth;
    $expYear = (int)$expYear;

    // Check if the expiry date is in the past
    if ($expYear < $currentYear || ($expYear === $currentYear && $expMonth < $currentMonth)) {
        return false;
    }

    return true;
}

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_number = $_POST['card_number'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';

    // Validate card number (16 digits, with or without spaces)
    if (!validateCardNumber($card_number)) {
        $errors['card_number'] = "Please enter a valid 16-digit card number.";
    }

    // Validate expiry date (MM/YY format and not in the past)
    if (!validateExpiryDate($expiry_date)) {
        $errors['expiry_date'] = "Please enter a valid expiry date in MM/YY.";
    }

    // Validate CVV (3 or 4 digits)
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        $errors['cvv'] = "Please enter a valid 3 or 4 digit CVV.";
    }

    // Validate first name and last name (letters only)
    if (!preg_match('/^[A-Za-z]+$/', $first_name)) {
        $errors['first_name'] = "Please enter a valid first name (letters only).";
    }
    if (!preg_match('/^[A-Za-z]+$/', $last_name)) {
        $errors['last_name'] = "Please enter a valid last name (letters only).";
    }

    // If no errors, proceed with payment processing
    if (empty($errors)) {
        // Redirect to payment processing (you'll implement this later)
        header("Location: process_payment.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Yummi Food</title>
    <link rel="stylesheet" href="./css/food-style.css">
    <link rel="stylesheet" href="designcard.css"> 
    <style>
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
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
                        <a href="?logout=true">Log Out</a>
                    </div>
                <?php else: ?>
                    <a href="index.php" class="sign-in-button">Sign In</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <section class="hero">
        <h1>Checkout</h1>
    </section>

    <main class="checkout-container">
        <div class="cart-summary">
            <h2>Your Order</h2>
            <div class="order-items">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                    $item_total = $row['unit_price'] * $row['quantity'];
                    $total_price += $item_total;
                    ?>
                    <div class="order-item">
                        <span class="item-name"><?php echo htmlspecialchars($row['item_name']); ?></span>
                        <span class="item-quantity">x<?php echo $row['quantity']; ?></span>
                        <span class="item-price">$<?php echo number_format($item_total, 2); ?></span>
                    </div>
                <?php endwhile; ?>
            </div>
            <h3 class="order-total">Total: $<?php echo number_format($total_price, 2); ?></h3>
        </div>

        <div class="payment-box">
            <h2>Payment Details</h2>
            
            <div class="card-icons">
                <img src="./images/visa.png" alt="Visa">
                <img src="./images/mastercard.png" alt="MasterCard">
                <img src="./images/paypal.png" alt="PayPal">
            </div>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label>Card Number</label>
                <input type="text" name="card_number" placeholder="1234 5678 9101 1121" value="<?php echo htmlspecialchars($card_number); ?>" required>
                <?php if (isset($errors['card_number'])): ?>
                    <span class="error"><?php echo $errors['card_number']; ?></span>
                <?php endif; ?>

                <div class="flex-row">
                    <div>
                        <label>MM/YY</label>
                        <input type="text" name="expiry_date" placeholder="MM/YY" value="<?php echo htmlspecialchars($expiry_date); ?>" required>
                        <?php if (isset($errors['expiry_date'])): ?>
                            <span class="error"><?php echo $errors['expiry_date']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label>CVV</label>
                        <input type="text" name="cvv" placeholder="123" value="<?php echo htmlspecialchars($cvv); ?>" required>
                        <?php if (isset($errors['cvv'])): ?>
                            <span class="error"><?php echo $errors['cvv']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex-row">
                    <div>
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                        <?php if (isset($errors['first_name'])): ?>
                            <span class="error"><?php echo $errors['first_name']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                        <?php if (isset($errors['last_name'])): ?>
                            <span class="error"><?php echo $errors['last_name']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="checkout-buttons">
                    <button type="button" class="cancel-button" onclick="window.location.href='cart.php'">Cancel</button>
                    <button type="submit" class="pay-button">Payment</button>
                </div>
            </form>
        </div>
    </main>

    <script>
    document.querySelector('input[name="card_number"]').addEventListener('input', function (e) {
        // Remove all non-digit characters
        let value = this.value.replace(/\D/g, '');
        
        // Add a space after every 4 digits
        value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
        
        // Value to max 19 characters (16 digits + 3 spaces)
        this.value = value.substring(0, 19);
    });
    </script>

</body>
</html>
