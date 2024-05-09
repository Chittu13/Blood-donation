<?php
session_start();
include 'db.php'; // Make sure you're correctly including your database connection script

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$phone_number = '+91' . filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);

// Proceed with updating other user information if email and phone number are valid
// Assuming you've validated and sanitized other inputs as necessary
$name = $_POST['name'];
$email = $_POST['email']; // Ensure you validate and sanitize
$date_of_birth = $_POST['date_of_birth'];
$blood_group = $_POST['blood_group'];
$address = $_POST['address'];

$stmt = $pdo->prepare("UPDATE user SET name = ?, phone_number = ?, email = ?, date_of_birth = ?, blood_group = ?, address = ? WHERE id = ?");
$success = $stmt->execute([$name, $phone_number, $email, $date_of_birth, $blood_group, $address, $userId]);

if ($success) {
    $_SESSION['success'] = "Profile updated successfully.";
    header('Location: profile.php');
} else {
    $_SESSION['error'] = "Error updating profile.";
    header('Location: profile.php');
}
exit;
?>
