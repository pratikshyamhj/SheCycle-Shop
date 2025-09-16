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

    // Validate rental days
if (!is_numeric($rental_period) || $rental_period < 0) {
    die("Rental days must be a non-negative number.");
}

// Validate price
if (!is_numeric($price) || $price < 0) {
    die("Price must be a non-negative number.");
}

// Convert to float
$rental_days = floatval($rental_period);
$price = floatval($price);

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
