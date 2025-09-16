<?php
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: SellerLogin.php");
    exit();
}

include 'db_connect.php';

$seller_id = $_SESSION['seller_id']; 
$message = '';

// Handle Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload'])) {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $rental_period = isset($_POST['rental_period']) ? $_POST['rental_period'] : null;
    $product_image = $_FILES['product_image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($product_image);

    // Convert price to an integer
    $price = intval($price);

    // Format price in Nepali Rupees
    $formatted_price = 'Rs. ' . number_format($price);

    // Create upload directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO products (seller_id, product_name, product_image, description, price, type, category, rental_period) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die('Error preparing statement: ' . $conn->error);
        }

        // Bind the parameters and execute the statement
        $stmt->bind_param("isssdssi", $seller_id, $product_name, $target_file, $description, $price, $type, $category, $rental_period);

        if ($stmt->execute()) {
            $message = "Product uploaded successfully!";
        } else {
            $message = "Error executing statement: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Sorry, there was an error uploading your file.";
    }

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

        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }

        .dashboard-menu {
            background-color: #6a1b9a;
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

         .dropdown {
            float: right;
            overflow: hidden;
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

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            float: none;
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        .dropdown:hover .dropdown-content {
            display: block;
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
    </script>
</head>
<body>
    <div class="dropdown" style="float: right;">
    <button class="dropbtn">
        <img src="default-profile.jpg" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%;">
    </button>
    <div class="dropdown-content">
        <a href="viewProfileS.php">View Profile</a>
        <a href="editProfileS.php">Edit Profile</a>
        <a href="logout_S.php">Logout</a>
    </div>
</div>
    <div class="dashboard-menu">
        
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#" data-form="upload-form">Upload Product</a></li>
            <!-- Remove Update Product and Delete Product links -->
        </ul>

    </div>

    <div class="dashboard-content">
        <h1>Welcome to Your Dashboard</h1>
        <?php if (isset($_GET['message'])): ?>
            <p><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>

        <!-- Upload Product Form -->
        <div id="upload-form" class="popup-form">
            <h2>Upload Product</h2>
            <form action="dashboard.php" method="post" enctype="multipart/form-data">
                <label for="product_name">Product Name:</label>
                <input type="text" name="product_name" id="product_name" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required></textarea>

                <label for="price">Price (in Nepali Rupees):</label>
                <input type="number" name="price" id="price" required>

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
    <!-- Add more categories as needed -->
</datalist>


                <div id="rental_period_container" style="display:none;">
                    <label for="rental_period">Rental Period (days):</label>
                    <input type="number" name="rental_period" id="rental_period">
                </div>

                <label for="product_image">Product Image:</label>
                <input type="file" name="product_image" id="product_image" required>

                <button type="submit" name="upload">Upload</button>
            </form>
        </div>

        <!-- Display Products -->
        <div>
            <h2>Your Products</h2>
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <div class="details">
                            <span><?php echo htmlspecialchars($product['product_name']); ?></span>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <p>Price: <?php echo htmlspecialchars($product['price']); ?></p>
                            <p>Type: <?php echo htmlspecialchars($product['type']); ?></p>
                            <p>Category: <?php echo htmlspecialchars($product['category']); ?></p>
                            <?php if ($product['type'] === 'Rent'): ?>
                                <p>Rental Period: <?php echo htmlspecialchars($product['rental_period']); ?> days</p>
                            <?php endif; ?>
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
    </div>
</body>
</html>
