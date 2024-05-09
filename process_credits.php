<?php
require 'db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit;
}

$user_id = $_SESSION['user_id'];
$code = $_POST['code'];

$stmt = $pdo->prepare("SELECT * FROM history WHERE user_id = ? AND code = ?");
$stmt->execute([$user_id, $code]);
$row = $stmt->fetch();

if ($row) {
    $phone = $row['phone'];
    $name = $row['name'];
    $city = $row['city'];
    $state = $row['state'];

    $checkSql = "SELECT * FROM ScoreTable WHERE phone = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$phone]);

    if ($checkStmt->rowCount() > 0) {
        $updateSql = "UPDATE ScoreTable SET score = score + 5 WHERE phone = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$phone]);
    } else {
        $insertSql = "INSERT INTO ScoreTable (name, phone, city, state, score) VALUES (?, ?, ?, ?, 5)";
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([$name, $phone, $city, $state]);
    }

    $deleteSql = "DELETE FROM history WHERE id = ?";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute([$row['id']]);

    echo "Credits processed successfully";
} else {
    echo "Invalid code";
}
?>
