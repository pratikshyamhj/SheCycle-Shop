<?php
// edit_product.php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../Seller/SellerLogin.php");
    exit();
}

$seller_id = $_SESSION['seller_id'];
$product_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update the product details
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $type = $_POST['type'];
    $rental_period = $_POST['rental_period'];

    $update_sql = "UPDATE products SET product_name=?, description=?, price=?, category=?, type=?, rental_period=?, updated_at=NOW() WHERE id=? AND seller_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssii", $product_name, $description, $price, $category, $type, $rental_period, $product_id, $seller_id);

    if ($stmt->execute()) {
        header("Location: ../Seller/dashboard.php?message=Product updated successfully");
    } else {
        echo "Error updating product.";
    }
    $stmt->close();
} else {
    // Fetch the current product details
    $sql = "SELECT * FROM products WHERE id=? AND seller_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $product_id, $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Edit product details</title>
    <style>
    /* General styles for the form */
    form {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    /* Form labels */
    label {
        display: block;
        font-weight: bold;
        margin-bottom: 8px;
        color: #333;
    }

    /* Form inputs and textarea */
    input[type="text"],
    input[type="number"],
    textarea,
    select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;
    }

    /* Button styles */
    button[type="submit"] {
        width: 100%;
        padding: 12px;
        background-color: #28a745;
        color: #fff;
        font-size: 18px;
        font-weight: bold;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background-color: #218838;
    }

    /* Responsive styling for smaller screens */
    @media (max-width: 600px) {
        form {
            padding: 15px;
        }

        label {
            font-size: 14px;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            font-size: 14px;
        }

        button[type="submit"] {
            font-size: 16px;
        }
    }
</style>

</head>
<body>
    <!-- Edit Product Form -->
<form method="POST">
    <label>Product Name:</label>
    <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
    
    <label>Description:</label>
    <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

    <label>Price:</label>
    <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

    <label>Category:</label>
    <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>

    <label>Type:</label>
    <select name="type" required>
        <option value="Thrift" <?php if ($product['type'] == 'Thrift') echo 'selected'; ?>>Thrift</option>
        <option value="Rent" <?php if ($product['type'] == 'Rent') echo 'selected'; ?>>Rent</option>
    </select>

    <div id="rental_period_container" style="display:<?php echo ($product['type'] == 'Rent') ? 'block' : 'none'; ?>;">
        <label>Rental Period:</label>
        <input type="number" name="rental_period" value="<?php echo htmlspecialchars($product['rental_period']); ?>">
    </div>

    <button type="submit">Update Product</button>
</form>
</body>
</html>


