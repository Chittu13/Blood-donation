<?php
session_start();
header('Content-Type: application/json');

// Assuming user is logged in, retrieve user's information from session
$user_id = $_SESSION['user_id'] ?? null;

// Establish a connection to MySQL database
$servername = "localhost";
$username = "root";
$password = ""; // Enter your MySQL password here
$dbname = "user_registration";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the donor is logged in
if($user_id) {
    // Retrieve donor's information from the user table
    $sql_user = "SELECT city, blood_group FROM user WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        // Fetch the donor's information
        $row_user = $result_user->fetch_assoc();
        $donor_city = $row_user['city'];
        $donor_bloodgroup = $row_user['blood_group'];

        // Prepare a SQL statement to retrieve matching data from the request table based on compatibility rules and city
        $sql_request = "SELECT * FROM request WHERE city=? AND blood_group IN ";

        // Define the compatible blood groups based on the donor's blood group
        $compatible_blood_groups = array();
        switch ($donor_bloodgroup) {
            case "A+":
                $compatible_blood_groups = array("A+", "AB+");
                break;
            case "A-":
                $compatible_blood_groups = array("A+", "A-", "AB+", "AB-");
                break;
            case "B+":
                $compatible_blood_groups = array("B+", "AB+");
                break;
            case "B-":
                $compatible_blood_groups = array("B+", "B-", "AB+", "AB-");
                break;
            case "O+":
                $compatible_blood_groups = array("O+", "A+", "B+", "AB+");
                break;
            case "O-":
                $compatible_blood_groups = array("O+", "O-", "A+", "A-", "B+", "B-", "AB+", "AB-");
                break;
            case "AB+":
                $compatible_blood_groups = array("AB+");
                break;
            case "AB-":
                $compatible_blood_groups = array("AB+", "AB-");
                break;
            default:
                $compatible_blood_groups = array(); // Empty array if unknown blood group
        }

        // Generate the SQL query dynamically based on compatible blood groups and donor's city
        $sql_request .= "('" . implode("','", $compatible_blood_groups) . "') AND city=?";
        $stmt_request = $conn->prepare($sql_request);
        $stmt_request->bind_param("ss", $donor_city, $donor_city);
        $stmt_request->execute();
        $result_request = $stmt_request->get_result();

        if ($result_request->num_rows > 0) {
            $output = array();
            while($row = $result_request->fetch_assoc()) {
                $output[] = $row;
            }
            echo json_encode($output);
        } else {
            echo json_encode(array("error" => "No matching results found"));
        }
    } else {
        echo json_encode(array("error" => "Donor information not found"));
    }

    // Close the statement
    $stmt_user->close();
    $stmt_request->close();
} else {
    // Redirect to login page if not logged in
    echo json_encode(array("error" => "User not logged in"));
}

// Close the database connection
$conn->close();
?>
