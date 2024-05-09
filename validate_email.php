<?php
session_start();

$host = 'localhost';
$dbname = 'user_registration';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $conn->real_escape_string($_POST['email']);

$sql = "SELECT id FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userId = $user['id'];

    // Generate a unique token for password reset
    $token = bin2hex(random_bytes(32));

    // Store the token in your database, assuming you have a password_resets table
    $sql = "INSERT INTO password_resets (email, token) VALUES (?, ?) ON DUPLICATE KEY UPDATE token=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $token, $token);
    $stmt->execute();

    // Construct the password reset link
    $resetLink = "http://yourwebsite.com/reset_password.php?token=$token"; // Adjust the URL

    // Send the email
    $to = $email;
    $subject = "Password Reset";
    $message = "To reset your password, click on this link: " . $resetLink;
    $headers = "From: no-reply@yourwebsite.com"; // Adjust the From address
    mail($to, $subject, $message, $headers);

    $_SESSION['success'] = "If your email is in our database, you will receive a password reset link shortly.";
} else {
    $_SESSION['error'] = "If your email is in our database, you will receive a password reset link shortly.";
}

header("Location: forgot_password.php");
exit();
?>
