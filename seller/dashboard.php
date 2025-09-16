<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: SellerLogin.php");
    exit();
}

include '../db_connect.php';

$seller_id = $_SESSION['seller_id'];
$message = '';

// Fetch Notifications
$notification_sql = "SELECT n.id, n.buyer_id, n.product_id, n.message, n.timestamp, b.name AS buyer_name
                      FROM notifications n
                      JOIN buyers b ON n.buyer_id = b.id
                      WHERE n.seller_id = ?
                      ORDER BY n.timestamp DESC";
$notification_stmt = $conn->prepare($notification_sql);
$notification_stmt->bind_param("i", $seller_id);
$notification_stmt->execute();
$notifications_result = $notification_stmt->get_result();
$notifications = $notifications_result->fetch_all(MYSQLI_ASSOC);
$notification_stmt->close();

// Count Unread Notifications
$unread_sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE seller_id = ? AND status = 'unread'";
$unread_stmt = $conn->prepare($unread_sql);
$unread_stmt->bind_param("i", $seller_id);
$unread_stmt->execute();
$unread_result = $unread_stmt->get_result();
$unread_notifications = $unread_result->fetch_assoc();
$unread_count = $unread_notifications['unread_count'];
$unread_stmt->close();



// Handle Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload'])) {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = intval($_POST['price']);
    $type = $_POST['type'];
    $category = $_POST['category'];
    $rental_period = isset($_POST['rental_period']) ? $_POST['rental_period'] : null;
    $status = 'Available'; // Default status

    // Handle multiple image uploads
    $image_paths = [];
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    foreach ($_FILES['product_images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['product_images']['error'][$key] === UPLOAD_ERR_OK) {
            $image_name = $_FILES['product_images']['name'][$key];
            $target_file = $upload_dir . basename($image_name);
            
            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_paths[] = $target_file;
            } else {
                $message = "Error moving file " . $image_name;
            }
        } else {
            $message = "Error uploading file " . $_FILES['product_images']['name'][$key];
        }
    }

    // Ensure we have up to 4 images, fill in empty slots with null
    $image_paths = array_pad($image_paths, 4, null);

    // Insert into the database
    $sql = "INSERT INTO products (seller_id, product_name, product_image, product_image2, product_image3, product_image4, description, price, category, type, rental_period, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die('Error preparing statement: ' . $conn->error);
    }

    // Bind parameters and execute statement
    $stmt->bind_param("isssssssssss", $seller_id, $product_name, $image_paths[0], $image_paths[1], $image_paths[2], $image_paths[3], $description, $price, $category, $type, $rental_period, $status);

    if ($stmt->execute()) {
        $message = "Product uploaded successfully!";
    } else {
        $message = "Error executing statement: " . $stmt->error;
    }

    $stmt->close();

    // Redirect to the dashboard with a message
    header("Location: dashboard.php?message=" . urlencode($message));
    exit();
}



// Fetch Products
$sql = "SELECT * FROM products WHERE seller_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Error preparing statement: ' . $conn->error);
}

$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Dashboard - SheCycle Shop</title>
    <link rel="stylesheet" type="text/css" href="stylesheets.css">
    <link href="https://fonts.googleapis.com/css?family=Cormorant+Infant&display=swap" rel="stylesheet">
    <script src="script.js" defer></script>
    <style>
        body {
            font-family: 'Cormorant Infant', serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        a{
            text-align: center;
        }

        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }

        .dashboard-menu {
            background-color: #795c34;
            width: 100px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 20px;
            z-index: 1000;
        }
        /* Notification Dropdown */
.dropdown {
    float: right;
    position: relative;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.dropbtn {
    font-size: 16px;
    border: none;
    outline: none;
    background-color: inherit;
    margin: 0;
    cursor: pointer;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
    right: 0;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #ddd;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: 10px;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: bold;
}


         

        .dropdown .dropbtn {
            font-size: 16px;  
            border: none;
            outline: none;
            color: white;
            padding: 14px 16px;
            background-color: inherit;
            font-family: inherit;
            margin: 0;
        }

        .navbar a, .dropdown .dropbtn {
            cursor: pointer;
        }

        
        
        

        .dashboard-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .dashboard-menu ul li {
            margin-bottom: 10px;
        }

        .dashboard-menu ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            display: block;
            width: 100%;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .dashboard-menu ul li a:hover {
            background-color: #4a148c;
        }

        .dashboard-content {
            margin-left: 120px;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
            text-align: center;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f5f5f5; /* Light grey background */
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-item img {
            max-width: 200px;
            max-height: 200px;
            margin-right: 15px;
            border-radius: 5px;
        }

        .product-item .details {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-item .details span {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .product-item .details p {
            margin: 5px 0;
            color: red;
        }

        .product-item .status {
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            background-color: #8B4513; /* Dark brown background */
            padding: 5px 10px;
            border-radius: 5px;
            margin-left: 15px;
        }

        /* Styling for "Sold" status */
        .product-item .status.sold {
            background-color: #B22222; /* Dark red background */
        }

        .popup-form {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
        }

        .popup-form.active {
            display: block;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"], input[type="file"], input[type="number"], select, textarea {
            width: calc(100% - 22px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-family: 'Cormorant Infant', serif;
        }

        button[type="submit"] {
            background-color: #6a1b9a;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #4a148c;
        }
        .form-group {
            margin-bottom: 15px;
        }

        .form-group button {
            padding: 10px 20px;
            background-color: #6a1b9a;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #4a148c;
        }

        .notification-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
            max-width: 300px; /* You can set a width limit to avoid too-wide notifications */
        }

        .notification-item a {
            color: #007bff;
            text-decoration: none;
        }

        .notification-item a:hover {
            text-decoration: underline;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: 10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
        }

        /* Styling for Carousel */
.image-carousel {
    position: relative;
    width: 100%;
    max-width: 500px; /* Adjust based on your layout */
    overflow: hidden;
}

.carousel-slide {
    display: none;
}

.carousel-slide img {
    width: 100%;
    height: auto;
}

.carousel-slide.active {
    display: block;
}

.carousel-nav {
    position: absolute;
    top: 50%;
    width: 100%;
    display: flex;
    justify-content: space-between;
    transform: translateY(-50%);
}

.carousel-nav button {
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
}

.actions {
    margin-top: 10px;
}

/* Button styles */
.edit-btn, .delete-btn {
    background-color: #4caf50; /* Green */
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 5px;
    margin-right: 10px;
    display: inline-block;
    cursor: pointer;
}

.delete-btn {
    background-color: #f44336;
}

.edit-btn:hover {
    background-color: #45a049;
}

.delete-btn:hover {
    background-color: #d32f2f;
}

/* Flex container for the buttons */
.actions {
    display: flex;
    justify-content: flex-start;
    margin-top: 20px;
}
.close-icon {
    font-size: 24px;
    color: #333;
    margin-bottom: 10px; /* Adjust for spacing */
}







    </style>
    <script>

        

        document.addEventListener("DOMContentLoaded", function() {
            const menuLinks = document.querySelectorAll('.dashboard-menu ul li a');
            const popupForms = document.querySelectorAll('.popup-form');

            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const formId = this.getAttribute('data-form');
                    popupForms.forEach(form => {
                        form.classList.remove('active');
                    });
                    document.getElementById(formId).classList.add('active');
                });
            });

            const typeSelect = document.getElementById('type');
            const rentalPeriodContainer = document.getElementById('rental_period_container');

            typeSelect.addEventListener('change', function() {
                if (this.value === 'Rent') {
                    rentalPeriodContainer.style.display = 'block';
                } else {
                    rentalPeriodContainer.style.display = 'none';
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
    const carousels = document.querySelectorAll('.image-carousel');
    
    carousels.forEach(carousel => {
        const slides = carousel.querySelectorAll('.carousel-slide');
        let currentIndex = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(currentIndex);
        }

        // Initial display
        showSlide(currentIndex);

        // Create navigation buttons
        const nav = document.createElement('div');
        nav.className = 'carousel-nav';
        nav.innerHTML = `
            <button class="prev">&lt;</button>
            <button class="next">&gt;</button>
        `;
        carousel.appendChild(nav);

        nav.querySelector('.next').addEventListener('click', nextSlide);
        nav.querySelector('.prev').addEventListener('click', prevSlide);
    });
});

        function validateForm() {
    const rentalDays = parseFloat(document.getElementById('rental_period').value);
    const price = parseFloat(document.getElementById('price').value);

    if (rentalDays < 0) {
        alert("Rental days must be a non-negative number.");
        return false;
    }

    if (price < 0) {
        alert("Price must be a non-negative number.");
        return false;
    }

    return true; // All checks passed
}
function closeForm() {
    document.getElementById('upload-form').classList.remove('active');
}


    </script>
</head>
<body>

     <div class="dropdown" style="float: right; position: relative;">
    <button class="dropbtn">
        <img src="../Picture/notification-icon.png" alt="Notifications" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;">
        <?php if ($unread_count > 0): ?>
            <span class="notification-badge"><?php echo $unread_count; ?></span>
        <?php endif; ?>
    </button>
    <div class="dropdown-content">
        <a href="notifications.php">View Notifications</a>
    </div>
</div>



    <div class="dropdown" style="float: right;">
    <button class="dropbtn">
        <img src="../Picture/default-profile.jpg" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%;">
    </button>
    <div class="dropdown-content">
        <a href="viewProfileS.php">View Profile</a>
        <a href="editProfileS.php">Edit Profile</a>
        <a href="logout_S.php">Logout</a>
    </div>
</div>
    <div class="dashboard-menu">

        <ul>
            <a href="../index.php"><li><img src="../Picture/home.png"></li></a>
            <li><a href="#" data-form="upload-form">Upload Product</a></li>
            
        </ul>

    </div>

    <div class="dashboard-content">
        <h1>Welcome to Your Dashboard</h1>

        <?php if (!empty($message)) { ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>

       

        <!-- Upload Product Form -->
        <div id="upload-form" class="popup-form">
            <span class="close-icon" onclick="closeForm()" style="cursor:pointer; float:right;">&times;</span>
            <h2>Upload Product</h2>
            <form action="dashboard.php" method="post" onsubmit="return validateForm()" enctype="multipart/form-data">
                <label for="product_name">Product Name:</label>
                <input type="text" name="product_name" id="product_name" required>


                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required></textarea>

                <label for="price">Price (in Nepali Rupees):</label>
                <input type="number" name="price" id="price" placeholder="Price" required step="1" min="0">

                <label for="type">Product Type:</label>
                <select name="type" id="type" required>
                    <option value="Thrift">Thrift</option>
                    <option value="Rent">Rent</option>
                </select>

                <label for="category">Category:</label>
<input list="categories" id="category" name="category" required>
<datalist id="categories">
    <option value="Skirt">
    <option value="Shirt">
    <option value="Pant">
    <option value="Lehenga">
    <option value="kurtha">
    <option value="jacket">
    <option value="Tshirt">
    <!-- Add more categories as needed -->
</datalist>


                <div id="rental_period_container" style="display:none;">
                    <label for="rental_period">Rental Period (days):</label>
                    <input type="number" name="rental_period" id="rental_period" placeholder="Rental Period" required step="1" min="0">
                </div>

                <label for="product_image">Product Images (up to 4):</label>
                <input type="file" name="product_images[]" id="product_images" multiple required>

                <button type="submit" name="upload">Upload</button>
            </form>
        </div>

        <!-- Display Products -->
<div>
    <h2>Your Products</h2>
    <?php if (count($products) > 0): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <div class="image-carousel">
                    <?php if (!empty($product['product_image'])): ?>
                        <div class="carousel-slide">
                            <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['product_image2'])): ?>
                        <div class="carousel-slide">
                            <img src="<?php echo htmlspecialchars($product['product_image2']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['product_image3'])): ?>
                        <div class="carousel-slide">
                            <img src="<?php echo htmlspecialchars($product['product_image3']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['product_image4'])): ?>
                        <div class="carousel-slide">
                            <img src="<?php echo htmlspecialchars($product['product_image4']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="details">
                    <span><?php echo htmlspecialchars($product['product_name']); ?></span>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($product['price']); ?></p>
                    <p>Type: <?php echo htmlspecialchars($product['type']); ?></p>
                    <?php if ($product['type'] === 'Rent'): ?>
                        <p>Rental Period: <?php echo htmlspecialchars($product['rental_period']); ?> days</p>
                    <?php endif; ?>
                    <p>Category: <?php echo htmlspecialchars($product['category']); ?></p>
                    <!-- Add Edit and Delete Buttons -->
                <div class="actions">
                    <a href="../Product/edit_product.php?id=<?php echo $product['id']; ?>" class="edit-btn">Edit</a>
                    <a href="../Product/delete_product.php?id=<?php echo $product['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>


                </div>
                    
                </div>

                <div class="status <?php echo ($product['status'] === 'Sold') ? 'sold' : ''; ?>">
                    <?php echo htmlspecialchars($product['status']); ?>
                </div>
                
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have no products listed.</p>
    <?php endif; ?>
</div>

</body>
</html>
