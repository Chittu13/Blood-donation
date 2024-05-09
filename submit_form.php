<?php
// Establish a connection to the database
$conn = new mysqli("localhost", "root", "", "user_registration");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$bloodGroup = $_POST['bloodGroup'];
$state = $_POST['state'];
$city = $_POST['city'];
$address = $_POST['address'];

// Insert user request into the request table
$sql = "INSERT INTO request (name, phone, email, blood_group, state, city, address) VALUES ('$name', '$phone', '$email', '$bloodGroup', '$state', '$city', '$address')";
if ($conn->query($sql) === TRUE) {
    echo "Request submitted successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Send SMS to matching donors
// Twilio configuration
$sid = '';
$token = '';
$twilioNumber = '';
require_once 'twilio-php-8.0.1/src/Twilio/autoload.php'; // Include the Twilio PHP library

// Create Twilio client
$client = new Twilio\Rest\Client($sid, $token);

// Find donors matching the user's request
$sql = "SELECT * FROM user WHERE state = '$state' AND city = '$city'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through each matching donor
    while ($row = $result->fetch_assoc()) {
        $donorName = $row['name'];
        $donorPhone = $row['phone_number'];
        // SMS content
        $smsContent = "Hello $donorName,\n\nThere is an urgent blood donation request in your area.\n\nRequest Details:\nName: $name\nPhone: $phone\nEmail: $email\nBlood Group: $bloodGroup\nAddress: $address, $city, $state\n\nPlease consider donating.\n\n- Blood Donation Team";
        // Send SMS
        $client->messages->create(
            $donorPhone,
            array(
                'from' => $twilioNumber,
                'body' => $smsContent
            )
        );
    }
} else {
    echo "No matching donors found.";
}

// Close the database connection
$conn->close();
?>
