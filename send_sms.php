<?php
require_once 'twilio-php-8.0.1/src/Twilio/autoload.php';

use Twilio\Rest\Client;

// Twilio API credentials
$accountSid = '';
$authToken = '';
$twilioNumber = ''; // Update this with your Twilio phone number

// Database connection
$host = 'localhost';
$dbname = 'user_registration';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Function to generate a random code
function generateRandomCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Function to insert data into history table
function insertIntoHistory($pdo, $userId, $name, $phone, $email, $bloodGroup, $state, $city, $address, $zipCode, $code) {
    try {
        $stmt = $pdo->prepare("INSERT INTO history (user_id, name, phone, email, blood_group, state, city, address, zip_code, code) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $name, $phone, $email, $bloodGroup, $state, $city, $address, $zipCode, $code]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Function to delete data from request table
function deleteFromRequest($pdo, $phone) {
    try {
        $stmt = $pdo->prepare("DELETE FROM request WHERE phone = ?");
        $stmt->execute([$phone]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if phone number is set
    if (isset($_POST['phone'])) {
        // Fetch donor's details from the database (replace this with your authentication mechanism)
        session_start();
        include 'db.php'; // Ensure correct path to your database connection file

        if (!isset($_SESSION['user_id'])) {
            echo "User not logged in";
            exit;
        }

        $userId = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT name, phone_number, email, blood_group, state, city, address, zipcode FROM user WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            echo "User not found";
            exit;
        }

        // Generate random code
        $randomCode = generateRandomCode();

        // Prepare the SMS message
        $message = "Dear " . $user['name'] . ",\n\n";
        $message .= "Please share the following code after donation: " . $randomCode . "\n\n";
        $message .= "Your Details:\n";
        $message .= "Name: " . $user['name'] . "\n";
        $message .= "Phone Number: " . $user['phone_number'] . "\n";
        $message .= "Blood Group: " . $user['blood_group'];

        // Insert data into history table
        $success = insertIntoHistory($pdo, $userId, $user['name'], $user['phone_number'], $user['email'], $user['blood_group'], $user['state'], $user['city'], $user['address'], $user['zipcode'], $randomCode);

        if (!$success) {
            echo "Failed to store data in history";
            exit;
        }

        try {
            // Initialize Twilio client
            $twilio = new Client($accountSid, $authToken);

            // Send SMS message
            $twilio->messages->create(
                // Recipient phone number (with country code)
                '+91' . $_POST['phone'], // Assuming you're sending to India (+91)
                [
                    // Twilio number (sender)
                    'from' => $twilioNumber,
                    // Message body
                    'body' => $message
                ]
            );

            // Delete data from request table
            $deleted = deleteFromRequest($pdo, $_POST['phone']);

            if (!$deleted) {
                echo "Failed to delete data from request table";
                exit;
            }

            // SMS sent successfully
            echo "SMS sent successfully";
        } catch (Exception $e) {
            // Failed to send SMS
            echo "Failed to send SMS: " . $e->getMessage();
        }
    } else {
        // Phone number not provided
        echo "Phone number not provided";
    }
} else {
    // Invalid request method
    echo "Invalid request method";
}
?>
