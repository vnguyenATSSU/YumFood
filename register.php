<?php
include 'connect.php';
session_start(); // Start session at the beginning

// Registration (Sign Up)
if (isset($_POST['signUp'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $check_email_query = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        // Insert new user into "user" table
        $insert_query = "INSERT INTO user (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "Registration Successful! Redirecting to login page...";
            header("refresh:2; url=index.php"); // Redirect after 2 seconds
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    $stmt->close();
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

        // Debugging: Print stored hashed password
        var_dump($row['password']); 

        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];

            
            echo "<h2>Welcome, " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "!</h2>";
            echo "<p>Your email: " . htmlspecialchars($row['email']) . "</p>";
            echo "<a href='logout.php'>Logout</a>"; // Provide a logout link
        } else {
            echo "Incorrect Password";
        }
    } else {
        echo "Not Found, Incorrect Email or Password";
    }
    $stmt->close();
}

$conn->close();
?>
