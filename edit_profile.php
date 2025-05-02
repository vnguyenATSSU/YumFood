<?php
session_start();
require 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Fetch the current user's password hash
    $stmt = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        // Current password is correct, proceed with update
        $update_query = "UPDATE user SET first_name = ?, last_name = ?";
        $params = [$first_name, $last_name];

        if (!empty($new_password)) {
            if ($new_password === $confirm_new_password) {
                $update_query .= ", password = ?";
                $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            } else {
                $error_message = "New passwords do not match.";
            }
        }

        if (empty($error_message)) {
            $update_query .= " WHERE user_id = ?";
            $params[] = $user_id;

            $stmt = $conn->prepare($update_query);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            
            if ($stmt->execute()) {
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Error updating profile: " . $conn->error;
            }
        }
    } else {
        $error_message = "Current password is incorrect.";
    }
}

// Fetch current user data
$stmt = $conn->prepare("SELECT first_name, last_name, email FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Yummi Food</title>
    <link rel="stylesheet" href="./css/food-style.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-update {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <!-- Include your header here -->
    </header>

    <main>
        <div class="form-container">
            <h2>Edit Profile</h2>
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <form action="edit_profile.php" method="post">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password (leave blank to keep current):</label>
                    <input type="password" id="new_password" name="new_password">
                </div>

                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password:</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password">
                </div>

                <button type="submit" class="btn-update">Update Profile</button>
            </form>
        </div>
    </main>

    <footer>
        <!-- Include your footer here -->
    </footer>
</body>
</html>
