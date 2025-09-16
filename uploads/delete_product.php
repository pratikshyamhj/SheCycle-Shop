// delete_product.php
<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: ../Seller/SellerLogin.php");
    exit();  
}

include '../db_connect.php';

$seller_id = $_SESSION['seller_id']; // Current logged-in seller
$product_id = $_GET['id']; // Product ID passed in the URL

// Ensure the product belongs to the seller before deleting
$sql = "DELETE FROM products WHERE id = ? AND seller_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Error preparing statement: ' . $conn->error);
}

$stmt->bind_param("ii", $product_id, $seller_id);

if ($stmt->execute()) {
    $message = "Product deleted successfully!";
} else {
    $message = "Error deleting product: " . $stmt->error;
}

$stmt->close();
header("Location: ../Seller/dashboard.php?message=" . urlencode($message));
exit();
?>
