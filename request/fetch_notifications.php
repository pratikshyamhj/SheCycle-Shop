<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: ../Seller/SellerLogin.php");
    exit();
}

include '../db_connect.php';

$seller_id = $_SESSION['seller_id'];

// Fetch Notifications
$notification_sql = "SELECT n.id, n.buyer_id, n.product_id, n.message, n.timestamp, b.name AS buyer_name
                      FROM notifications n
                      JOIN buyers b ON n.buyer_id = b.id
                      WHERE n.seller_id = ?
                      ORDER BY n.timestamp DESC";
$notification_stmt = $conn->prepare($notification_sql);
$notification_stmt->bind_param("i", $seller_id);
$notification_stmt->execute();
$notifications_result = $notification_stmt->get_result();
$notifications = $notifications_result->fetch_all(MYSQLI_ASSOC);
$notification_stmt->close();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications - SheCycle Shop</title>
    <link rel="stylesheet" type="text/css" href="../Seller/stylesheets.css">
</head>
<body>
    <h1>Your Notifications</h1>

    <?php if (!empty($notifications)) { ?>
        <?php foreach ($notifications as $notification) { ?>
            <div class="notification-item">
                <p><strong>Buyer:</strong> <?php echo htmlspecialchars($notification['buyer_name']); ?> has requested your product with Product ID: <a href="../Product/product_detail.php?id=<?php echo htmlspecialchars($notification['product_id']); ?>"><?php echo htmlspecialchars($notification['product_id']); ?></a></p>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>No new notifications.</p>
    <?php } ?>
</body>
</html>
