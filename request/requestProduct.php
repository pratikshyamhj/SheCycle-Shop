<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['buyer_id'])) {
    echo "not_logged_in";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $buyer_id = $_SESSION['buyer_id'];
    $product_id = $_POST['product_id'];

    // Get current timestamp for the requested time
    $requested_time = date('Y-m-d H:i:s'); // Current date and time in MySQL format

    // Insert or update request in the database, with requested_time
    $sql = "INSERT INTO requests (buyer_id, product_id, status, requested_time) 
            VALUES (?, ?, 'Requested', ?)
            ON DUPLICATE KEY UPDATE status = 'Requested', requested_time = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $buyer_id, $product_id, $requested_time, $requested_time);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
}

$conn->close();
?>
