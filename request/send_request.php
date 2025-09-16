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
        echo "Request sent successfully";
    } else {
        // Error inserting request
        echo "Error sending request: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // If not a POST request, handle accordingly (optional)
    echo "Invalid request method";
}
?>
<script>
    // JavaScript function to fetch and display notifications for the seller
function fetchNotifications() {
    // Perform AJAX request to fetch notifications
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_notifications.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Handle response and update UI with notifications
                let notifications = JSON.parse(xhr.responseText);
                notifications.forEach(notification => {
                    // Display each notification (buyer details, request date, etc.)
                    console.log(notification);
                    // You can append this data to a notification area or use a toast library for better UX
                });
            } else {
                // Request failed
                console.error('Failed to fetch notifications');
            }
        }
    };
    xhr.send();
}

</script>