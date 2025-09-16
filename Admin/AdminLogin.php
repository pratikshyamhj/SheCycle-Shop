
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADMIN</title>
    <link rel="stylesheet" type="text/css" href="../stylesheet.css">
    <style>
        body {
    font-family: 'Cormorant Infant', serif;
    text-align: center;
}
header{
                    background: transparent;
                    font-size: 30px;
                    text-align: left;

                } 



    </style>
</head>
<body style="background-image: url('../Picture/sl.png');">
    
<?php 
include('../db_connect.php');

?>

        <header><a href="../index.php"><img src="../Picture/home.png"></a></header>
    
    <div class="form-container">
        <h1>Admin Login</h1>
        
        <form action="adminDashboard.php" method="POST">

             <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            
            <button type="submit" value=Login onclick="checkLogin()">Login</button>
        </form>
    </div>
<script>
        // Define your admin credentials (not secure in production)
        const adminUsername = "admin";
        const adminPassword = "adminpassword";

        function checkLogin() {
            const usernameInput = document.getElementById("username").value;
            const passwordInput = document.getElementById("password").value;

            if (usernameInput === adminUsername && passwordInput === adminPassword) {
                
                window.location.href = "adminDashboard.php";
                // Redirect to admin panel or perform admin actions here
            } else {
                alert("Invalid username or password. Please try again.");
                document.getElementById("password").value = "";
            }
        }
    </script>
</body>
</html>