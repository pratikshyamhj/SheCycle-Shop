
<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: ../Seller/SellerLogin.php");
    exit();
}

include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $product_id = $_POST['product_id_update'];
    $new_product_name = $_POST['new_product_name'];
    $new_product_image = $_FILES['new_product_image']['name'];
    $update_image = !empty($new_product_image);
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($new_product_image);

    if ($update_image && !is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if ($update_image && move_uploaded_file($_FILES['new_product_image']['tmp_name'], $target_file)) {
        $sql = "UPDATE products SET product_name = ?, product_image = ? WHERE id = ? AND seller_id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die('Error preparing statement: ' . $conn->error);
        }

        $stmt->bind_param("ssii", $new_product_name, $target_file, $product_id, $seller_id);
    } else {
        $sql = "UPDATE products SET product_name = ? WHERE id = ? AND seller_id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die('Error preparing statement: ' . $conn->error);
        }

        $stmt->bind_param("sii", $new_product_name, $product_id, $seller_id);
    }

    if ($stmt->execute()) {
        $message = "Product updated successfully!";
    } else {
        $message = "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
    header("Location: ../Seller/dashboard.php?message=" . urlencode($message));
    exit();
}

// Fetch Products
$sql = "SELECT id, product_name, product_image, description, price, type, rental_period, status FROM products WHERE seller_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();
$conn->close();
?>