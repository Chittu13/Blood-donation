<?php
// Database connection
$host = 'localhost';
$dbname = 'user_registration'; // Ensure this matches your actual database name
$username = 'root'; // Default username for XAMPP
$password = ''; // Default password for XAMPP
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize input data
$name = $conn->real_escape_string($_POST['name']);
$phone_number = $conn->real_escape_string($_POST['phone_number']);
$email = $conn->real_escape_string($_POST['email']);
$passwordInput = $conn->real_escape_string($_POST['password']); // Receive and sanitize the password
$date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);
$blood_group = $conn->real_escape_string($_POST['blood_group']);
$state = $conn->real_escape_string($_POST['state']);
$city = $conn->real_escape_string($_POST['city']);
$zipcode = $conn->real_escape_string($_POST['zipcode']);
$address = $conn->real_escape_string($_POST['address']);

// Prepend country code "+91" to the phone number
$phone_number = "+91" . $phone_number;

// Hash the password
$hashed_password = password_hash($passwordInput, PASSWORD_DEFAULT);

// Check if email already exists
$sql_email_check = "SELECT * FROM user WHERE email = ?";
$stmt_email_check = $conn->prepare($sql_email_check);
$stmt_email_check->bind_param("s", $email);
$stmt_email_check->execute();
$result_email_check = $stmt_email_check->get_result();

if ($result_email_check->num_rows > 0) {
    echo "Email already exists!";
    exit; // Exit the script if email already exists
}

// Check if phone number already exists
$sql_phone_check = "SELECT * FROM user WHERE phone_number = ?";
$stmt_phone_check = $conn->prepare($sql_phone_check);
$stmt_phone_check->bind_param("s", $phone_number);
$stmt_phone_check->execute();
$result_phone_check = $stmt_phone_check->get_result();

if ($result_phone_check->num_rows > 0) {
    echo "Phone number already exists!";
    exit; // Exit the script if phone number already exists
}

// SQL query to insert user data into the database
$sql = "INSERT INTO user (name, phone_number, email, password, date_of_birth, blood_group, state, city, zipcode, address) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("ssssssssss", $name, $phone_number, $email, $hashed_password, $date_of_birth, $blood_group, $state, $city, $zipcode, $address);
    
    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }
    // Close statement
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

// Close connection
$conn->close();
?>
