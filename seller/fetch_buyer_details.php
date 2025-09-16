<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: SellerLogin.php");
    exit();
}

include '../db_connect.php';

if (isset($_POST['buyer_id'])) {
    $buyer_id = $_POST['buyer_id'];

    // Prepare and execute the query
    $buyer_sql = "SELECT * FROM buyers WHERE id = ?";
    $buyer_stmt = $conn->prepare($buyer_sql);
    $buyer_stmt->bind_param("i", $buyer_id);
    $buyer_stmt->execute();
    $buyer_result = $buyer_stmt->get_result();

    if ($buyer_result->num_rows > 0) {
        $buyer_details = $buyer_result->fetch_assoc();
    } else {
        $buyer_details = null;
    }
    $buyer_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buyer Details</title>
    <link rel="stylesheet" type="text/css" href="stylesheets.css">
    <style>
        .buyer-details {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-width: 600px;
            margin: 20px auto;
        }
    </style>
</head>
<body style="background-image: url('../sl.png');">
    <div class="buyer-details-container">
        <h1>Buyer Details</h1>
        
        <?php if (isset($buyer_details)) { ?>
            <div class="buyer-details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($buyer_details['name']); ?></p>
                <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($buyer_details['email']); ?>"><?php echo htmlspecialchars($buyer_details['email']); ?></a></p>

                <p><strong>Gender:</strong> <?php echo htmlspecialchars($buyer_details['gender']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($buyer_details['location']); ?></p>
                <p><strong>Province:</strong> <?php echo htmlspecialchars($buyer_details['province']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($buyer_details['city']); ?></p>
                <p><strong>District:</strong> <?php echo htmlspecialchars($buyer_details['district']); ?></p>
                <p><strong>Created At:</strong> <?php echo htmlspecialchars($buyer_details['created_at']); ?></p>
            </div>
        <?php } else { ?>
            <p>No buyer details found.</p>
        <?php } ?>
        
        <a href="notifications.php">Back to Notifications</a>
    </div>
</body>
</html>
