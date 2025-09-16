<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SheCycle Shop</title>
    <style type="text/css">
        
        /* Basic reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body, html {
    height: 100%;
    font-family: 'Arial', sans-serif;
}

.main {
    background-image: url('Picture/pic.png'); 
    height: 100vh;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    overflow: hidden;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); 
}

.content {
    position: relative;
    z-index: 1;
}

h1 {
    font-size: 6rem; 
    margin-bottom: 20px;
    font-weight: bold;
    font-family: 'Cursive', sans-serif;
    padding-left: 500px;
    text-shadow: 0 0 10px #fca311, 0 0 20px #fca311; 
}

p {
    font-size: 2.8rem; 
    margin-bottom: 30px;
    line-height: 1.6;
    padding-left: 500px;
   
    font-family: cursive;
}



.button-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    padding-left: 500px;
}

.button {
    background-color: #838562;
    color: white;
    padding: 10px 20px;
    border: hotpink;
    border-radius: 30px;
    text-decoration: none;
    font-size: 2rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    padding-top: 18px;
    padding-bottom: 18px;
}

.button:hover {
    background-color: #e09b0d;
}
.admin-icon {
    position: absolute;
    top: 20px; /* Adjust as needed */
    right: 20px; /* Adjust as needed */
    width: 50px; /* Adjust the size as needed */
    height: auto;
    cursor: pointer;
    z-index: 2; /* Ensure it’s above other elements */
}



    </style>
</head>
<body>
    <div class="main">
        <div class="overlay"></div>
        <div class="content">
            <h1>SheCycle Shop</h1>
            <p>Thrift like a Queen.<br>Rent like a Boss...</p>
            


            <div class="button-container">
                <a href="Seller/SellerLogin.php" class="button">SELLER</a>
                <a href="Buyer/BuyerHomePage.php" class="button">BUYER</a>
            </div>
        </div>
    </div>

    <a href="Admin/AdminLogin.php"><img src="Picture/admin-icon.png" alt="Admin" class="admin-icon"></a>

</body>
</html>
