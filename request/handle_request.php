<?php
session_start();
include '../db_connect.php';

// Check if the buyer is logged in
if (!isset($_SESSION['buyer_id'])) {
    header("Location: ../Buyer/BuyerLogin.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

// Ensure the POST request contains product_id
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['action']) && $_POST['action'] == 'request') {
    $product_id = $_POST['product_id'];

    // Prepare SQL statement with error handling
    $sql = "INSERT INTO requests (buyer_id, product_id, status) VALUES (?, ?, 'Requested') 
            ON DUPLICATE KEY UPDATE status = 'Requested'";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    $stmt->bind_param("ii", $buyer_id, $product_id);
    
    if ($stmt->execute()) {
        // Get seller_id from the product
        $get_seller_sql = "SELECT seller_id FROM products WHERE id = ?";
        $get_seller_stmt = $conn->prepare($get_seller_sql);
        $get_seller_stmt->bind_param("i", $product_id);
        $get_seller_stmt->execute();
        $get_seller_result = $get_seller_stmt->get_result();
        $seller = $get_seller_result->fetch_assoc();
        $seller_id = $seller['seller_id'];
        $get_seller_stmt->close();

        // Insert notification for the seller
        $notification_sql = "INSERT INTO notifications (seller_id, buyer_id, product_id, message) VALUES (?, ?, ?, ?)";
        $notification_stmt = $conn->prepare($notification_sql);
        $message = "Buyer with ID $buyer_id has requested your product with ID $product_id.";
        $notification_stmt->bind_param("iiis", $seller_id, $buyer_id, $product_id, $message);
        
        if ($notification_stmt->execute()) {
    echo "Notification inserted successfully.";
    header("Location: ../Buyer/BuyerHomePage.php?message=Product requested successfully!");
    exit();
} else {
    echo "Error inserting notification: " . $notification_stmt->error;
}


        $notification_stmt->close();
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
