<?php
session_start();

// Retrieve stored form data
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login - YummiFood</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .error-message {
            background-color: #ffebee;
            color: #b71c1c;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ef9a9a;
            border-radius: 4px;
        }
        .success-message {
            background-color: #e8f5e9;
            color: #1b5e20;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #b9f6ca;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="container" id="signup" style="<?php echo isset($_SESSION['show_signup']) ? 'display:block;' : 'display:none;'; ?>">
        <h1 class="form-title">Register</h1>
        <form method="post" action="register.php">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="first_name" id="first_name" placeholder="First Name" required value="<?php echo isset($form_data['first_name']) ? htmlspecialchars($form_data['first_name']) : ''; ?>">
                <label for="first_name">First Name</label>
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="last_name" id="last_name" placeholder="Last Name" required value="<?php echo isset($form_data['last_name']) ? htmlspecialchars($form_data['last_name']) : ''; ?>">
                <label for="last_name">Last Name</label>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Email" required value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>">
                <label for="email">Email</label>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            <input type="submit" class="btn" value="Sign Up" name="signUp">
        </form>
        <div class="links">
            <p>Already Have Account?</p>
            <button id="signInButton">Sign In</button>
        </div>
    </div>

    <div class="container" id="signIn" style="<?php echo !isset($_SESSION['show_signup']) ? 'display:block;' : 'display:none;'; ?>">
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="register.php">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Email" required value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>">
                <label for="email">Email</label>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
        <div class="links">
            <p>Don't have an account yet?</p>
            <button id="signUpButton">Sign Up</button>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
