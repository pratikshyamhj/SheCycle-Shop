
<!DOCTYPE html>
<html>
<head>
    <title>Buyer Login - SheCycle Shop</title>
    <link rel="stylesheet" type="text/css" href="../stylesheet.css">
    <link href="https://fonts.googleapis.com/css?family=Cormorant+Infant&display=swap" rel="stylesheet">
    <style type="text/css">
        header{
                    background: transparent;
                    font-size: 30px;
                    text-align: left;

                }   
    </style>
</head>
<body style="background-image: url('../Picture/sl.png');">
   <header><a href="../index.php"><img src="../Picture/home.png"></a></header> 
    
    <div class="form-container">
        <h1>Buyer Login</h1>

        <!-- Check for a message in the URL query string -->
        <?php
        if (isset($_GET['message'])) {
            $message = htmlspecialchars($_GET['message']);
            echo "<script>alert('$message');</script>";
        }
        ?>
        
        <form action="buyer_login_process.php" method="POST">

             <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="BuyerRegister.php">Register here</a></p>
    </div>
</body>
</html>
