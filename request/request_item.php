<?php
session_start();
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $seller_id = $_POST['seller_id'];
    $buyer_id = $_SESSION['buyer_id']; // Assuming buyer ID is stored in session

    // Insert request into database (assuming you have a table for requests)
    $sql = "INSERT INTO requests (seller_id, buyer_id, request_date) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $seller_id, $buyer_id);

    if ($stmt->execute()) {
        // Request successfully inserted into database
        echo "<script>alert('Request sent successfully');</script>";
        echo "<script>window.location.replace('../index.php');</script>"; // Redirect back to home page
    } else {
        // Error inserting request
        echo "<script>alert('Failed to send request. Please try again.');</script>";
        echo "<script>window.location.replace('../index.php');</script>"; // Redirect back to home page
    }

    $stmt->close();
    $conn->close();
} else {
    // If not a POST request, handle accordingly (optional)
    echo "Invalid request method";
}
header('location:../Buyer/BuyerHomePage.php')
?>
