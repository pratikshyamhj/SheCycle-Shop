<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: SellerLogin.php");
    exit();
}

include '../db_connect.php';

// Get the seller ID from the session
$seller_id = $_SESSION['seller_id'];

// Fetch seller details from the database
$sql = "SELECT name, email, gender, location, province, city, district FROM sellers WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Error preparing statement: ' . $conn->error);
}

$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the seller data
if ($result->num_rows > 0) {
    $seller = $result->fetch_assoc();
} else {
    echo "Error: Seller not found.";
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Profile - SheCycle Shop</title>
    <link rel="stylesheet" type="text/css" href="stylesheets.css">
    <style>
        body {
            font-family: 'Cormorant Infant', serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 50%;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #6a1b9a;
        }

        p {
            font-size: 18px;
            color: #333;
        }

        .profile-details {
            margin-top: 20px;
        }

        .profile-details span {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Seller Profile</h1>
        <div class="profile-details">
            <p><span>Name:</span> <?php echo htmlspecialchars($seller['name']); ?></p>
            <p><span>Email:</span> <?php echo htmlspecialchars($seller['email']); ?></p>
            <p><span>Gender:</span> <?php echo htmlspecialchars($seller['gender']); ?></p>
            <p><span>Location:</span> <?php echo htmlspecialchars($seller['location']); ?></p>
            <p><span>Province:</span> <?php echo htmlspecialchars($seller['province']); ?></p>
            <p><span>City:</span> <?php echo htmlspecialchars($seller['city']); ?></p>
            <p><span>District:</span> <?php echo htmlspecialchars($seller['district']); ?></p>
        </div>
    </div>
</body>
</html>
