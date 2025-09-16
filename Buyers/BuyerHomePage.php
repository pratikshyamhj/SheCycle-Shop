<?php
// Start the session
session_start();

// Include database connection file
include '../db_connect.php';

// Check if a product_id is passed in the URL
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch the details of the selected product
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Display the product details here
        $product = $result->fetch_assoc();
        echo "<h2>Product Details for: " . htmlspecialchars($product['product_name']) . "</h2>";
        // Add the rest of the product details here
    } else {
        echo "<p>Product not found.</p>";
    }
}

if (isset($_GET['message'])) {
    echo "<p>" . htmlspecialchars($_GET['message']) . "</p>";
}

$logged_in = isset($_SESSION['buyer_id']);
$buyer_id = $logged_in ? $_SESSION['buyer_id'] : null;
if (isset($_SESSION['buyer_id'])) {
    $logged_in = true;
} else {
    $logged_in = false;
} // Assuming user_id is set in session when logged in

$buyer_id = $_SESSION['buyer_id'] ?? null;
$logged_in = $buyer_id !== null;

// Fetch notifications for the buyer
$notifications_sql = "
    SELECT 
        notification_buyer.id, 
        notification_buyer.product_id, 
        notification_buyer.message, 
        notification_buyer.timestamp, 
        notification_buyer.status,
        products.product_name
    FROM notification_buyer
    JOIN products ON notification_buyer.product_id = products.id
    WHERE notification_buyer.buyer_id = ? 
    ORDER BY notification_buyer.timestamp DESC";
$notifications_stmt = $conn->prepare($notifications_sql);
if ($notifications_stmt === false) {
    die('Error preparing statement: ' . $conn->error);
}
$notifications_stmt->bind_param("i", $buyer_id);
$notifications_stmt->execute();
$notifications_result = $notifications_stmt->get_result();
$notifications = $notifications_result->fetch_all(MYSQLI_ASSOC);
$notifications_stmt->close();

// Count unread notifications
$unread_count_sql = "SELECT COUNT(*) AS unread_count FROM notification_buyer WHERE buyer_id = ? AND status = 'unread'";
$unread_count_stmt = $conn->prepare($unread_count_sql);
if ($unread_count_stmt === false) {
    die('Error preparing statement: ' . $conn->error);
}
$unread_count_stmt->bind_param("i", $buyer_id);
$unread_count_stmt->execute();
$unread_count_result = $unread_count_stmt->get_result();
$unread_count = $unread_count_result->fetch_assoc()['unread_count'];
$unread_count_stmt->close();

// Mark notifications as read
$update_notifications_sql = "UPDATE notification_buyer SET status = 'read' WHERE buyer_id = ? AND status = 'unread'";
$update_notifications_stmt = $conn->prepare($update_notifications_sql);
if ($update_notifications_stmt === false) {
    die('Error preparing statement: ' . $conn->error);
}
$update_notifications_stmt->bind_param("i", $buyer_id);
$update_notifications_stmt->execute();
$update_notifications_stmt->close();

// Fetch the selected category from the URL, default to 'All'
$category = isset($_GET['category']) ? $_GET['category'] : 'All';


// Prepare the SQL query based on the selected category
if ($category == 'All') {
    // If 'All' is selected, fetch all products
    $sql = "
    SELECT 
        products.id, product_name, product_image, description, price, type, category, rental_period, product_image2, product_image3, product_image4,
        sellers.id AS seller_id, sellers.name AS seller_name, sellers.province, sellers.city, sellers.district, sellers.gender
    FROM products
    JOIN sellers ON products.seller_id = sellers.id";
    $stmt = $conn->prepare($sql);
} else {
    // If a specific category is selected, fetch products for that category
    $sql = "
        SELECT 
            products.id, product_name, product_image, description, price, type, category,rental_period, product_image2, product_image3, product_image4,
            sellers.id AS seller_id, sellers.name AS seller_name, sellers.province, sellers.city, sellers.district, sellers.gender
        FROM products
        JOIN sellers ON products.seller_id = sellers.id
        WHERE products.category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category); // Bind the category parameter
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SheCycle Shop - Buyer Home</title>

    <link rel="stylesheet" type="text/css" href="../stylesheet.css">

    <style>
        body {
            font-family: 'Cormorant Infant', serif;
            background-color: #f4f4f9;
            margin: 0;
        }

        /* Header and navigation bar styles */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #794c34;
            color: white;
            padding: 5px;
            z-index: 1000;
        }

        .navbar {
            background-color: #794c34;
            padding: 5px;
        }

        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .dropdown {
            float: left;
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

        
        main {
            margin-top: 80px; 
            padding: 20px;
        }

        /* Product grid styles */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 0 50px;
        }

        .product-card {
            background-color: white;
            padding: 15px;
            text-align: center;
        }

        .product-info {
            margin-top: 10px;
        }

        .product-info h3 {
            margin: 0;
        }

        .request-button {
            background-color: #838562;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .seller-name-link:hover {
            background-color: #483D8B;
        }

        /* Modal styles for seller details */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8); 
            justify-content: center;
            align-items: center;
}


        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }

        .close {
            position: absolute;
            top: 15px; 
            right: 25px;
            color: white; 
            font-size: 30px; 
            font-weight: bold; 
            cursor: pointer; 
            z-index: 1001; 
        }

        .close:hover {
            color: #bbb; 
        }


        .lightbox {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .notification-icon {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .notification-icon {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .notification-icon .count {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 12px;
            font-weight: bold;
        }

        .notification-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Increased shadow for better visibility */
    z-index: 1000;
    width: 350px; /* Adjust width */
    max-height: 400px;
    overflow-y: auto;
    padding: 10px 0; /* Added padding to dropdown container */
}

.notification-dropdown p {
    margin: 0;
    padding: 10px;
    border-bottom: 1px solid #ddd;
    color: black; 
    transition: background-color 0.3s ease; 
}

.notification-dropdown p:hover {
    background-color: #f5f5f5; 
}

.notification-dropdown p:last-child {
    border-bottom: none; 
}

.notification-dropdown a {
    color: #007bff; 
    text-decoration: none;
}

.notification-dropdown a:hover {
    text-decoration: underline; 
}

.notification-dropdown small {
    display: block;
    color: gray;
    font-size: 12px;
    margin-top: 5px; 
}


/* Carousel Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.8);
    justify-content: center;
    align-items: center;
    border: 2px solid red;
}

.carousel-container {
    position: relative;
    width: 80%;
    max-width: 500px;
    margin: auto;
}

.carousel-container img {
    width: 100%;
    height: auto;
    display: block;
}

/* Updated Arrow Styles */
.prev, .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    width: auto;
    padding: 16px;
    margin-top: -22px;
    color: white;
    background-color: rgba(0, 0, 0, 0.5); 
    border-radius: 50%; 
    font-weight: bold;
    font-size: 36px; 
    transition: 0.6s ease;
    user-select: none;
    border: 2px solid white; 
}

.prev {
    left: 0;
}

.next {
    right: 0;
}


.prev:hover, .next:hover {
    background-color: rgba(255, 255, 255, 0.7); 
    color: black; 
}

.close {
    position: absolute;
    top: 10px;
    right: 20px;
    color: white;
    font-size: 30px;
    font-weight: bold;
    border: 2px solid blue;
}

.close:hover, .close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}


.carousel-container .close {
    position: absolute;
    top: 10px;
    right: 20px;
    color: white;
    font-size: 30px;
    font-weight: bold;
    cursor: pointer;
}



    </style>
</head>
<body>

    <header>
    <div class="navbar">
        <a href="../index.php">Home</a>

        <div class="dropdown">
            <button class="dropbtn">Categories</button>
            <div class="dropdown-content">
                <a href="BuyerHomePage.php?category=All">All</a>
                <a href="BuyerHomePage.php?category=Skirt">Skirt</a>
                <a href="BuyerHomePage.php?category=Shirt">Shirt</a>
                <a href="BuyerHomePage.php?category=Pant">Pant</a>
                <a href="BuyerHomePage.php?category=Lehenga">Lehenga</a>
                <a href="BuyerHomePage.php?category=kurtha">Kurtha</a>
                <a href="BuyerHomePage.php?category=jacket">Jacket</a>
                
            </div>
        </div>
        <?php if ($logged_in): ?>
            
        <!-- Profile Icon Dropdown -->
        <div class="dropdown" style="float: right;">
    <button class="dropbtn">
        <img src="../Picture/default-profile.jpg" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%;">
    </button>
    <div class="dropdown-content">
        <a href="viewProfile.php">View Profile</a>
        <a href="editProfile.php">Edit Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="notification-icon" style="float: right; margin-right: 10px; padding-top: 14px;">
    <img src="../Picture/notification-icon.png" alt="Notifications" style="width: 30px; height: 30px;" onclick="toggleDropdown()">
    <?php if ($unread_count > 0): ?>
        <span class="count"><?php echo $unread_count; ?></span>
    <?php endif; ?>
    <div class="notification-dropdown" id="notificationDropdown">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
                <p><a href="productGrid.php?product_id=<?php echo htmlspecialchars($notification['product_id']); ?>">Your request for product: <?php echo htmlspecialchars($notification['product_id']); ?> has been approved.</a>
                <small><?php echo htmlspecialchars($notification['timestamp']); ?></small></p>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No new notifications.</p>
        <?php endif; ?>
    </div>
</div>



<?php else: ?>
        <!-- Profile Icon for non-logged in users -->
        <div class="dropdown" style="float: right;">
            <button class="dropbtn" onclick="redirectToLogin()">
                <img src="../Picture/default-profile.jpg" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%;">
            </button>
        </div>
        <?php endif; ?>

    </div>
    <h1>SheCycle Shop - Browse Products</h1>
</header>


    <main>
        <!-- Product Grid -->
        <section class="product-grid" id="productGrid">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $product_id = htmlspecialchars($row['id']);
                $request_status = $logged_in ? "Requested" : "Request";
                ?>
                <div class="product-card">

    <a href="javascript:void(0);" onclick="openCarousel('<?php echo htmlspecialchars($row['product_image']); ?>', '<?php echo htmlspecialchars($row['product_image2']); ?>', '<?php echo htmlspecialchars($row['product_image3']); ?>', '<?php echo htmlspecialchars($row['product_image4']); ?>')">
        

        <img src="<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product Image" style="width: 50%;">
    </a>
    <div class="product-info">
        <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
        <p><?php echo htmlspecialchars($row['description']); ?></p>
        <div class="product-price">Rs. <?php echo number_format($row['price']); ?></div>
        <p>Type: <?php echo htmlspecialchars($row['type']); ?></p>
        <p>Category: <?php echo htmlspecialchars($row['category']); ?></p>
        <p>Rental: <?php echo htmlspecialchars($row['rental_period']); ?></p>

        <p>Seller: 
            <a href="javascript:void(0);" class="seller-name-link" 
               onclick="showSellerDetails('<?php echo htmlspecialchars($row['seller_name']); ?>', 
                                          '<?php echo htmlspecialchars($row['province']); ?>', 
                                          '<?php echo htmlspecialchars($row['city']); ?>', 
                                          '<?php echo htmlspecialchars($row['district']); ?>', 
                                          '<?php echo htmlspecialchars($row['gender']); ?>')">
                <?php echo htmlspecialchars($row['seller_name']); ?>
            </a>
        </p>
        
        <form action="../Request/handle_request.php" method="POST" style="display:inline;">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
            <input type="hidden" name="action" value="request">
            <?php
            // Check if the user has already requested this product
            $buyer_id = isset($_SESSION['buyer_id']) ? $_SESSION['buyer_id'] : null;
            if ($buyer_id) {
                $check_sql = "SELECT * FROM requests WHERE buyer_id = ? AND product_id = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("ii", $buyer_id, $row['id']);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                if ($check_result->num_rows > 0) {
                    echo '<button type="button" class="request-button" disabled>Requested</button>';
                } else {
                    echo '<button type="submit" class="request-button">Request</button>';
                }
                $check_stmt->close();
            } else {
                echo '<button type="submit" class="request-button">Request</button>';
            }
            ?>
        </form>
    </div>
</div>
 <!-- Image Carousel Modal -->
    <div id="carouselModal" class="modal">
       <span class="close" onclick="closeCarousel()">&times;</span>  
        <div class="carousel-container">
            <img id="carouselImage" src="" alt="Product Image">
            <a class="prev" onclick="changeSlide(-1)">&#10094;</a>
            <a class="next" onclick="changeSlide(1)">&#10095;</a>
        </div>
    </div>


                <?php
            }
        } else {
            echo "<p>No products available in this category.</p>";
        }
        ?>
    </section>
    </main>

    <!-- Seller Details Modal -->
    <div id="sellerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Seller Details</h2>
            <p><strong>Name:</strong> <span id="sellerName"></span></p>
            <p><strong>Province:</strong> <span id="sellerProvince"></span></p>
            <p><strong>City:</strong> <span id="sellerCity"></span></p>
            <p><strong>District:</strong> <span id="sellerDistrict"></span></p>
            <p><strong>Gender:</strong> <span id="sellerGender"></span></p>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="lightbox">
        <img id="lightboxImage" src="" alt="Lightbox Image">
    </div>

    <script>
        // Function to show the modal with seller details
        function showSellerDetails(name, province, city, district, gender) {
            document.getElementById('sellerName').textContent = name;
            document.getElementById('sellerProvince').textContent = province;
            document.getElementById('sellerCity').textContent = city;
            document.getElementById('sellerDistrict').textContent = district;
            document.getElementById('sellerGender').textContent = gender;

            document.getElementById('sellerModal').style.display = "block";
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('sellerModal').style.display = "none";
            document.getElementById('lightbox').style.display = "none";
        }

        // Close the modals when clicking outside of the modal content
        window.onclick = function(event) {
            var sellerModal = document.getElementById('sellerModal');
            var lightbox = document.getElementById('lightbox');
            if (event.target == sellerModal || event.target == lightbox) {
                closeModal();
            }
        }

        function toggleDropdown() {
    var dropdown = document.getElementById('notificationDropdown');
    if (dropdown.style.display === "block") {
        dropdown.style.display = "none";
    } else {
        dropdown.style.display = "block";
    }
}


window.onclick = function(event) {
    var dropdown = document.getElementById('notificationDropdown');
    if (!event.target.closest('.notification-icon')) {
        dropdown.style.display = "none";
    }
}
document.querySelectorAll('.notification-item').forEach(notification => {
    notification.addEventListener('click', function() {
        // Scroll to the product-grid section on the page
        document.getElementById('productGrid').scrollIntoView({
            behavior: 'smooth'
        });
    });
});



        // Function to open the lightbox with the image
        function openLightbox(imageSrc) {
            var lightbox = document.getElementById('lightbox');
            var lightboxImage = document.getElementById('lightboxImage');
            lightboxImage.src = imageSrc;
            lightbox.style.display = "flex";
        }
        // Function to redirect to login page
    function redirectToLogin() {
        window.location.href = 'BuyerLogin.php';
    }
    

let slideIndex = 0;
let slides = [];

function openCarousel(image1, image2, image3, image4) {
    slides = [image1, image2, image3, image4].filter(img => img); 
    slideIndex = 0;
    showSlide(slideIndex);

    document.getElementById('carouselModal').style.display = 'flex'; 
}

function closeCarousel() {
    document.getElementById('carouselModal').style.display = 'none';
}

function changeSlide(n) {
    slideIndex += n;
    if (slideIndex >= slides.length) {
        slideIndex = 0;
    }
    if (slideIndex < 0) {
        slideIndex = slides.length - 1;
    }
    showSlide(slideIndex);
}

function showSlide(index) {
    let carouselImage = document.getElementById('carouselImage');
    carouselImage.src = slides[index]; 
}


    </script>

</body>
</html>

<?php

$conn->close();
?>
