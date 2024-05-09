<?php
session_start();

require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found";
    exit;
}

$phone = $user['phone_number'];

// Check if the phone number exists in ScoreTable
$checkStmt = $pdo->prepare("SELECT * FROM ScoreTable WHERE phone = ?");
$checkStmt->execute([$phone]);

if ($checkStmt->rowCount() > 0) {
    $row = $checkStmt->fetch();
    echo $row['score'];
} else {
    // If phone number doesn't exist, display 0
    echo "0";
}
?>
