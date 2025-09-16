<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: SellerLogin.php");
    exit();
}

include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seller_id = $_SESSION['seller_id'];
    $buyer_id = $_POST['buyer_id'];
    $product_id = $_POST['product_id'];

    // Update the request to "approved" status in the requests table
    $approve_sql = "UPDATE requests SET request_status = 'approved' WHERE buyer_id = ? AND product_id = ? AND seller_id = ?";
    
    // Prepare and execute the query
    if ($approve_stmt = $conn->prepare($approve_sql)) {
        $approve_stmt->bind_param("iii", $buyer_id, $product_id, $seller_id);

        if ($approve_stmt->execute()) {
            // Success: Request status is updated
            $message = "Request approved successfully.";
        } else {
            // Error executing the query
            $message = "Error approving request: " . $approve_stmt->error;
        }

        $approve_stmt->close();
    } else {
        // Error preparing the query
        $message = "Error preparing statement: " . $conn->error;
    }

    // Create a notification for the buyer
    $buyer_notification_sql = "INSERT INTO notification_buyer (buyer_id, product_id, message, status, timestamp) 
                               VALUES (?, ?, 'Your request for the product has been approved', 'unread', NOW())";
    $buyer_notification_stmt = $conn->prepare($buyer_notification_sql);
    if ($buyer_notification_stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }
    $buyer_notification_stmt->bind_param("ii", $buyer_id, $product_id);
    $buyer_notification_stmt->execute();
    $buyer_notification_stmt->close();

    // Redirect to notifications.php with a success message
    header("Location: notifications.php?message=" . urlencode($message));
    exit();
}
?>
