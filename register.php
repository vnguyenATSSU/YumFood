<?php
include 'connect.php';
session_start();

// Registration (Sign Up)
if (isset($_POST['signUp'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
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
        die("Email Address Already Exists!");
    }

    // Insert new user (default is_admin = 0)
    $insert_query = "INSERT INTO user (first_name, last_name, email, password, is_admin) VALUES (?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Registration Successful! Redirecting to login page...";
        header("refresh:2; url=index.php");
        exit();
    } else {
        die("Error: " . $stmt->error);
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
            // Set session variables
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['is_admin'] = $row['is_admin']; // Store admin status

            // Redirect based on admin status
            if ($row['is_admin'] == 1) {
                header("Location: admin_dashboard.php"); // Redirect admin
            } else {
                header("Location: main.php"); // Redirect normal user
            }
            exit();
        } else {
            die("Incorrect Password");
        }
    } else {
        die("Email not found.");
    }
}

$conn->close();
?>
