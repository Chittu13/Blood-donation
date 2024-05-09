<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle authentication error
    header("Location: login.php");
    exit();
}

// Retrieve user information from session
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$phone = $_SESSION['phone'];
$city = $_SESSION['city'];
$state = $_SESSION['state'];

// Get input data from the form
$enteredCode = $_POST['code'];

// Check if the code exists in the history table and matches the phone
$sql = "SELECT * FROM history WHERE code = ? AND phone = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$enteredCode, $phone]);
$row = $stmt->fetch();

if ($row) {
    // Code exists and matches, so delete the data from the history table
    $deleteSql = "DELETE FROM history WHERE id = ?";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute([$row['id']]);

    // Check if the phone number already exists in the ScoreTable
    $checkSql = "SELECT * FROM ScoreTable WHERE phone = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$phone]);

    if ($checkStmt->rowCount() > 0) {
        // Phone number exists, so increment the score by 5
        $updateSql = "UPDATE ScoreTable SET score = score + 5 WHERE phone = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$phone]);
    } else {
        // Phone number doesn't exist, so insert a new row into ScoreTable with an initial score of 5
        $insertSql = "INSERT INTO ScoreTable (name, phone, city, state, score) VALUES (?, ?, ?, ?, 5)";
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([$name, $phone, $city, $state]);
    }

    // Display success message
    echo "Success: Your data has been processed.";
} else {
    // Display error message
    echo "Error: Code does not match.";
}
?>
