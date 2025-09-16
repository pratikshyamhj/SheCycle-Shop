<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: ../Seller/SellerLogin.php");
    exit();
}

include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id_update'];
    $new_product_name = $_POST['new_product_name'];
    $new_product_image = $_FILES['new_product_image']['name'];
    $seller_id = $_SESSION['seller_id'];

    // Check if a new image is provided
    if ($new_product_image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($new_product_image);

        // Check if directory exists, if not, create it
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['new_product_image']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE products SET product_name = ?, product_image = ?, operation_type = 'updated' WHERE id = ? AND seller_id = ?");
            $stmt->bind_param("ssii", $new_product_name, $target_file, $product_id, $seller_id);
        } else {
            echo "Sorry, there was an error uploading your new file.";
            exit();
        }
    } else {
        $stmt = $conn->prepare("UPDATE products SET product_name = ?, operation_type = 'updated' WHERE id = ? AND seller_id = ?");
        $stmt->bind_param("sii", $new_product_name, $product_id, $seller_id);
    }

    if ($stmt->execute()) {
        echo "Product updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}
$conn->close();
?>
