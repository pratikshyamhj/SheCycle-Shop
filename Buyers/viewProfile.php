<?php
session_start();
include '../db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['buyer_id'])) {
    header("Location: BuyerLogin.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

// Fetch buyer details from the database
$stmt = $conn->prepare("SELECT * FROM buyers WHERE id = ?");
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $buyer = $result->fetch_assoc();
} else {
    echo "No profile data found.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" type="text/css" href="../stylesheet.css">
</head>
<body>
    <header>
        <div class="navbar">
            <a href="BuyerHomePage.php">Home</a>
            <a href="editProfile.php">Edit Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>
    <main>
        <h1>Profile Details</h1>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($buyer['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($buyer['email']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($buyer['gender']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($buyer['location']); ?></p>
        <p><strong>Province:</strong> <?php echo htmlspecialchars($buyer['province']); ?></p>
        <p><strong>City:</strong> <?php echo htmlspecialchars($buyer['city']); ?></p>
        <p><strong>District:</strong> <?php echo htmlspecialchars($buyer['district']); ?></p>
    </main>
</body>
</html>
