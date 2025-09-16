<?php
// Start the session
session_start();

// Check if the seller is logged in
if (!isset($_SESSION['seller_id'])) {
    header("Location: sellerLogin.php");
    exit();
}

// Include database connection file
include '../db_connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch the seller ID from the session
    $seller_id = $_SESSION['seller_id'];
    
    // Get the submitted form data and sanitize it
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $province = mysqli_real_escape_string($conn, $_POST['province']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    
    // Hash the password for security purposes
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare the SQL query to update the seller's information
    $sql = "UPDATE sellers SET name = ?, password = ?, email = ?, gender = ?, location = ?, province = ?, city = ?, district = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $name, $hashed_password, $email, $gender, $location, $province, $city, $district, $seller_id);

    // Execute the query and check for success
    if ($stmt->execute()) {
        // Update successful, redirect to the profile page or dashboard
        $_SESSION['message'] = "Profile updated successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        // If there's an error, store the error message
        $_SESSION['error'] = "Error updating profile: " . $stmt->error;
        header("Location: editProfileS.php");
        exit();
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
