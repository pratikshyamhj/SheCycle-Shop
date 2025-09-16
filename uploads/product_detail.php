<?php
include '../db_connect.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id > 0) {
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Error preparing statement: ' . $conn->error);
    }

    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    $product = $product_result->fetch_assoc();
    $stmt->close();

    if ($product) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Product Detail - SheCycle Shop</title>
            <link rel="stylesheet" type="text/css" href="../Seller/stylesheets.css">
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
        <body>
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
                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>Price:</strong> <?php echo htmlspecialchars($product['price']); ?></p>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($product['type']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                <?php if (!empty($product['rental_period'])) { ?>
                    <p><strong>Rental Period:</strong> <?php echo htmlspecialchars($product['rental_period']); ?></p>
                <?php } ?>
                <a href="../Seller/dashboard.php">Back to Dashboard</a>
            </div>

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
        </body>
        </html>
        <?php
    } else {
        echo "Product not found.";
    }
} else {
    echo "Invalid product ID.";
}
?>
