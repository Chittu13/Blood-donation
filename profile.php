<?php
session_start();
include 'db.php'; // Ensure correct path to your database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name, phone_number, email, date_of_birth, blood_group, address FROM user WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/profile.css"> <!-- Ensure the path to your CSS is correct -->
</head>
<body>
<div class="profile-wrapper">
    <h1>User Profile</h1>
    <!-- Display mode -->
    <div id="profileDisplay">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" value="+91 <?php echo htmlspecialchars(substr($user['phone_number'], 3)); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Date of Birth:</label>
            <input type="text" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Blood Group:</label>
            <input type="text" value="<?php echo htmlspecialchars($user['blood_group']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Address:</label>
            <input type="text" value="<?php echo htmlspecialchars($user['address']); ?>" readonly>
        </div>
        <button onclick="toggleEdit()">Edit Profile</button>
    </div>

    <!-- Edit mode (hidden by default) -->
    <form id="profileEdit" action="updateProfileProcessor.php" method="post" style="display: none;">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone_number" value="<?php echo htmlspecialchars(substr($user['phone_number'], 3)); ?>" placeholder="1234567890" pattern="[0-9]{10}">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>">
        </div>
        <div class="form-group">
            <label for="blood_group">Blood Group:</label>
            <select id="blood_group" name="blood_group">
                <option value="A+" <?php echo $user['blood_group'] == 'A+' ? 'selected' : ''; ?>>A+</option>
                <option value="A-" <?php echo $user['blood_group'] == 'A-' ? 'selected' : ''; ?>>A-</option>
                <option value="B+" <?php echo $user['blood_group'] == 'B+' ? 'selected' : ''; ?>>B+</option>
                <option value="B-" <?php echo $user['blood_group'] == 'B-' ? 'selected' : ''; ?>>B-</option>
                <option value="AB+" <?php echo $user['blood_group'] == 'AB+' ? 'selected' : ''; ?>>AB+</option>
                <option value="AB-" <?php echo $user['blood_group'] == 'AB-' ? 'selected' : ''; ?>>AB-</option>
                <option value="O+" <?php echo $user['blood_group'] == 'O+' ? 'selected' : ''; ?>>O+</option>
                <option value="O-" <?php echo $user['blood_group'] == 'O-' ? 'selected' : ''; ?>>O-</option>
            </select>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
        </div>
        <button type="submit">Save Changes</button>
    </form>

</div>
<script>
    function toggleEdit() {
        var displayDiv = document.getElementById('profileDisplay');
        var editForm = document.getElementById('profileEdit');
        if (displayDiv.style.display === 'none') {
            displayDiv.style.display = 'block';
            editForm.style.display = 'none';
        } else {
            displayDiv.style.display = 'none';
            editForm.style.display = 'block';
        }
    }
</script>
</body>
</html>
