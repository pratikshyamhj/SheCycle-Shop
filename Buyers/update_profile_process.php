<?php
// Start session
session_start();

// Include database connection
include '../db_connect.php';

// Check if buyer is logged in
if (!isset($_SESSION['buyer_id'])) {
    // If not logged in, redirect to login page
    header("Location: BuyerLogin.php");
    exit;
}

// Get the buyer ID from the session
$buyer_id = $_SESSION['buyer_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $location = $_POST['location'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $password = $_POST['password'];

    // Optional: Add data validation here (e.g., validate email, password, etc.)

    // Encrypt the password (if it's being updated)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query to update the buyer's profile
    $sql = "
        UPDATE buyers
        SET name = ?, email = ?, gender = ?, location = ?, province = ?, city = ?, district = ?, password = ?
        WHERE id = ?
    ";

    // Prepare and bind the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssi', $name, $email, $gender, $location, $province, $city, $district, $hashed_password, $buyer_id);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to profile page with success message
        $_SESSION['profile_update_success'] = "Profile updated successfully.";
        header("Location: viewProfile.php");
    } else {
        // Redirect to profile edit page with error message
        $_SESSION['profile_update_error'] = "Failed to update profile. Please try again.";
        header("Location: editProfile.php");
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If the form is not submitted, redirect to profile edit page
    header("Location: editProfile.php");
    exit;
}
?>
