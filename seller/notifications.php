<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: SellerLogin.php");
    exit();
}

include '../db_connect.php';

$seller_id = $_SESSION['seller_id'];
$message = '';

// Mark notifications as read
$update_status_sql = "UPDATE notifications SET status = 'read' WHERE seller_id = ? AND status = 'unread'";
$update_status_stmt = $conn->prepare($update_status_sql);
$update_status_stmt->bind_param("i", $seller_id);
$update_status_stmt->execute();
$update_status_stmt->close();

// Fetch Notifications
$notification_sql = "SELECT n.id, n.buyer_id, n.product_id, n.message, n.timestamp, n.status, 
                            r.request_status, b.name AS buyer_name
                     FROM notifications n
                     JOIN buyers b ON n.buyer_id = b.id
                     JOIN requests r ON r.buyer_id = n.buyer_id AND r.product_id = n.product_id
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
    <title>Notifications</title>
    <link rel="stylesheet" type="text/css" href="stylesheets.css">
    <style>
        .notification-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
        }

        .notification-item.unread {
            border-left: 5px solid #007bff; /* Highlight unread notifications */
            background-color: #e9f6ff; /* Light blue for unread notifications */
        }

        .approve-button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .approve-button:disabled {
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <div class="notifications-container">
        <h1>Notifications</h1>

        <?php if (!empty($notifications)) { ?>
            <?php foreach ($notifications as $notification) { ?>
                <div class="notification-item <?php echo htmlspecialchars($notification['request_status']); ?>">
                    <p>
                        <form action="fetch_buyer_details.php" method="POST" style="display:inline;">
                            <input type="hidden" name="buyer_id" value="<?php echo htmlspecialchars($notification['buyer_id']); ?>">
                            <button type="submit" style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer;">
                                <strong><?php echo htmlspecialchars($notification['buyer_name']); ?></strong>
                            </button>
                        </form>
                        has requested your product with Product ID: 
                        <a href="../Product/product_detail.php?id=<?php echo htmlspecialchars($notification['product_id']); ?>">
                            <?php echo htmlspecialchars($notification['product_id']); ?>
                        </a>
                    </p>
                    <p><small><?php echo htmlspecialchars($notification['timestamp']); ?></small></p>
                    <form action="approve_request.php" method="POST" style="display:inline;">
                        <input type="hidden" name="buyer_id" value="<?php echo htmlspecialchars($notification['buyer_id']); ?>">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($notification['product_id']); ?>">
                        <?php if ($notification['request_status'] != 'approved') { ?>
                            <button type="submit" class="approve-button">Approve</button>
                        <?php } else { ?>
                            <button type="button" class="approve-button" disabled>Approved</button>
                        <?php } ?>
                    </form>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No new notifications.</p>
        <?php } ?>

        <div id="buyerModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
            <div style="background:white; margin:auto; padding:20px; width:300px; position:relative; top:50%; transform:translateY(-50%);">
                <span onclick="closeModal()" style="cursor:pointer; position:absolute; top:10px; right:10px;">&times;</span>
                <h2>Buyer Details</h2>
                <div id="buyerDetails"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    // Show alert if message is present in the URL
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    if (message) {
        alert(decodeURIComponent(message));
    }

    function showBuyerDetails(buyerId) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_buyer_details.php?buyer_id=" + buyerId, true);
        xhr.onload = function() {
            if (this.status === 200) {
                document.getElementById("buyerDetails").innerHTML = this.responseText;
                document.getElementById("buyerModal").style.display = "block";
            } else {
                alert("Error fetching buyer details.");
            }
        };
        xhr.send();
    }

    function closeModal() {
        document.getElementById("buyerModal").style.display = "none";
    }
    </script>
</body>
</html>
