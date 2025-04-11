<?php
include 'connect.php';
session_start();

// Clear previous errors and form data
unset($_SESSION['error']);
unset($_SESSION['form_data']);

// Registration (Sign Up)
if (isset($_POST['signUp'])) {
    $_SESSION['form_data'] = $_POST; // Store form data

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: index.php"); // Redirect back to index.php
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: index.php");
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_email_query = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email Address Already Exists!";
        header("Location: index.php");
        exit();
    }

    // Insert new user (default is_admin = 0)
    $insert_query = "INSERT INTO user (first_name, last_name, email, password, is_admin) VALUES (?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration Successful! Redirecting to login page...";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration error: " . $stmt->error;
        header("Location: index.php");
        exit();
    }
}

// Login (Sign In)
if (isset($_POST['signIn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user by email
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['is_admin'] = $row['is_admin'];

            if ($row['is_admin'] == 1) {
                header("Location: admin_modify.php"); // Redirect admin
            } else {
                header("Location: main.php"); // Redirect normal user
            }
            exit();
        } else {
            $_SESSION['error'] = "Incorrect Password";
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Email not found.";
        header("Location: index.php");
        exit();
    }
}

$conn->close();
?>
