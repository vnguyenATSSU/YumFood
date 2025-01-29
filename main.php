<?php
// main.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YummiFood - Welcome</title>
    <link rel="stylesheet" href="style.css">
    <style>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f4;
            color: #333;
        }

        
        header {
            background: #ff7b72; /* Warm color for the header */
            padding: 20px 0;
            text-align: center;
            color: white;
            position: relative;
        }

        header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        /* Positioning the Login button at top-right corner */
        .login-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            background-color: #f14d58;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover {
            background-color: #d73741;
        }

        /* Main Content */
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 50px 20px;
            text-align: center;
        }

        /* Footer */
        .footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>YummiFood</h1>
        <a href="index.php">
            <button class="login-btn">Login</button>
        </a>
    </header>
    <div class="main-content">
    </div>
    <div class="footer">
        <p>&copy; 2025 YummiFood. All rights reserved.</p>
    </div>

</body>
</html>
