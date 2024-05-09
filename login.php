<?php
session_start(); // Start session at the beginning

// Database connection
$host = 'localhost';
$dbname = 'user_registration';
$username = 'root'; // Use appropriate credentials
$password = ''; // Use appropriate credentials
$conn = new mysqli($host, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $conn->real_escape_string($_POST['identifier']);
    $password = $conn->real_escape_string($_POST['password']); // The entered password

    // Check if the identifier is a phone number and prepend +91 if not present
    if (is_numeric($identifier) && strlen($identifier) === 10) {
        $identifier = '+91' . $identifier; // Prepend +91 to phone numbers
    }

    // Prepare SQL to avoid SQL Injection
    $sql = "SELECT * FROM user WHERE email = ? OR phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Verify password (assuming you're using password hashing)
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            // Redirect to the Donors Dashboard page
            header("Location: donors-dashboard.html"); // Consider changing to .php if dynamic content or session checks are needed
            exit();
        } else {
            // Redirect with an error message for incorrect password
            header("Location: login.html?error=incorrect_password");
            exit();
        }
    } else {
        // Redirect with an error message for no user found
        header("Location: login.html?error=no_user_found");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
