<?php
include '../db_connect.php'; // Include your database connection

// Get the product_id from the URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id > 0) {
    // Query to get product details
    $product_query = "SELECT * FROM products WHERE id = $product_id";
    $product_result = mysqli_query($conn, $product_query);
    $product = mysqli_fetch_assoc($product_result);

    // If the product exists, get the seller details
    if ($product) {
        $seller_id = $product['seller_id']; // Assuming there's a seller_id field
        $seller_query = "SELECT * FROM sellers WHERE id = $seller_id";
        $seller_result = mysqli_query($conn, $seller_query);
        $seller = mysqli_fetch_assoc($seller_result);
    }
}
?>

<!-- HTML to display product and seller details -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <style>
                /* Styling for image container and navigation */
                .image-container {
                    position: relative;
                    width: 400px;
                    height: 400px;
                    margin: auto;
                }

                .image-container img {
                    width: 100%;
                    height: 100%;
                    display: none;
                    object-fit: cover;
                }

                .image-container img.active {
                    display: block;
                }

                .nav-button {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    background-color: rgba(0, 0, 0, 0.5);
                    color: white;
                    border: none;
                    padding: 10px;
                    cursor: pointer;
                }

                .nav-button.left {
                    left: 0;
                }

                .nav-button.right {
                    right: 0;
                }
            </style>

</head>
<script>
                let currentImageIndex = 0;
                const images = document.querySelectorAll('.image-container img');

                function showImage(index) {
                    images.forEach((img, i) => {
                        img.classList.toggle('active', i === index);
                    });
                }

                function nextImage() {
                    currentImageIndex = (currentImageIndex + 1) % images.length;
                    showImage(currentImageIndex);
                }

                function prevImage() {
                    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
                    showImage(currentImageIndex);
                }
            </script>
<body>

<?php if ($product && $seller): ?>
    <div id="product-<?php echo $product_id; ?>">
        <div class="product-detail">
                <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                <div class="image-container">
                    <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="Image 1" class="active">
                    <?php if (!empty($product['product_image2'])) { ?>
                        <img src="<?php echo htmlspecialchars($product['product_image2']); ?>" alt="Image 2">
                    <?php } ?>
                    <?php if (!empty($product['product_image3'])) { ?>
                        <img src="<?php echo htmlspecialchars($product['product_image3']); ?>" alt="Image 3">
                    <?php } ?>
                    <?php if (!empty($product['product_image4'])) { ?>
                        <img src="<?php echo htmlspecialchars($product['product_image4']); ?>" alt="Image 4">
                    <?php } ?>
                    <button class="nav-button left" onclick="prevImage()">&#10094;</button>
                    <button class="nav-button right" onclick="nextImage()">&#10095;</button>
                </div>
        <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
        <p><strong>Price:</strong> Rs<?php echo htmlspecialchars($product['price']); ?></p>
        <h3>Seller Information</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($seller['name']); ?></p>
        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($sellerx['email']); ?>"><?php echo htmlspecialchars($seller['email']); ?></a></p>
        
        <p><strong>Location:</strong> <?php echo htmlspecialchars($seller['location'] . ', ' . $seller['city'] . ', ' . $seller['province']); ?></p>

    </div>
<?php else: ?>
    <p>Product or seller not found.</p>
<?php endif; ?>
<a href="../Buyer/BuyerHomePage.php">Back to Dashboard</a>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

