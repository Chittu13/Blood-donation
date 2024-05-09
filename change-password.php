<?php
session_start(); // Start the session if you're using session variables to store user info

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database configuration
    $host = 'localhost'; // or your host
    $db   = 'user_registration';
    $user = 'root'; // Your database username
    $pass = ''; // Your database password
    $charset = 'utf8mb4';

    // DSN (Data Source Name)
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        // Establish a connection to the database
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Retrieve form data
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        // Assuming the user's email is stored in a session variable after login
        if (isset($_SESSION['user_email'])) { // Adjust to match your actual session variable
            $userEmail = $_SESSION['user_email'];
        } else {
            echo "User is not logged in.";
            exit; // Stop script execution if the user email is not set
        }

        if ($newPassword === $confirmPassword) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update SQL query to use the correct identifier column
            $sql = "UPDATE user SET password = ? WHERE email = ?"; // Adjust to match your identifier column

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$hashedPassword, $userEmail]);

            echo "Password changed successfully!";
        } else {
            echo "Passwords do not match.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
