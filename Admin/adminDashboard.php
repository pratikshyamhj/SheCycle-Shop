<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
   
    <style>
        * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            .container {
                display: flex;
            }

            .header {
                background-color: red;
                height: 20vh;
                color: #fff;
                text-align: center;
            }

            .sidebar {
                background-color: black;
                color: #fff;
                width: 25vw;
                height: 90vh;
            }

            .main {
                width: 100%;
                background-color: white;
            }

            .row {
                display: flex;
                justify-content: space-between;
                height: 50%;
            }

            .box {
                background-color: ;
                width: 24%;
                height: 95%;
            }
            a{
                color: whitesmoke;
            }
            .sidebar li{
                padding: 30px;
            }
             .sidebar li:hover{
                background: red;
                color: #f05462;
            }

        
            nav {
                display: flex;
                align-items: center;
                background: maroon;
                height: 90px;
                position: relative;
            }
            .icon {
                cursor: pointer;
                margin-right: 50px;
                line-height: 60px;
            }
            .icon span {
                background: #f00;
                padding: 7px;
                border-radius: 50%;
                color: #fff;
                vertical-align: top;
                margin-left: -25px;
            }
            .icon img {
                display: inline-block;
                width: 40px;
                margin-top: 20px;
            }
            .icon:hover {
                opacity: .7;
            }

            .logo {
                flex: 1;
                margin-left: 50px;
                color: #eee;
                font-size: 20px;
                font-family: monospace;

            }


            .notifi-item {
                display: flex;
                border-bottom: 1px solid #eee;   
                cursor: pointer;
            }
            .notifi-item:hover {
                background-color: #eee;
            }
            .notifi-item img {
                display: block;
                width: 50px;
                margin-right: 10px;
                border-radius: 50%;
            }
            .notifi-item .text h4 {
                color: #777;
                font-size: 16px;
                margin-top: 10px;
            }
        .notifi-item .text p {
                color: #aaa;
                font-size: 12px;
            }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-top: 100px;
            }

        .summary-item {
            background-color: lightgrey;
            padding: 50px;
            border: 2px solid maroon;
            border-radius: 5px;
            flex-basis: 48%;
            text-align: center;
        }

        .recent-activity {
            margin-top: 20px;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .welcome-message {
            text-align: center;
            margin-bottom: 20px;
            background-color: orangered;
            padding: 30px;   
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column; 
            }

            .sidebar {
                width: 100%; 
                height: auto; 
            }
        @media (max-width: 500px) {
            .logo {
                margin-left: 0; 
                font-size: 15px;
                padding: 10px 0;
            }
        }
        @media (max-width: 500px) {
            .summary {
                flex-direction: column;
                align-items: center;
            }

            .summary-item {
                width: 80%;
                margin: 10px 0;
                padding: 15px;
            }

            .summary-item h3 {
                font-size: 20px;
            }

            .summary-item p {
                font-size: 16px;
            }
        }
       
        

    </style>
</head>
<body>
    <?php
    include('../db_connect.php');
    ?>
    <nav>
        <div class="logo"> 
            <h1 style="text-align: center;">SheCycle Shop</h1>
            <h3 style="text-align: center;">Admin Panel</h3>
        </div>
    </div>
    </nav>            
    <div class="container">
             
            <div class="sidebar">
                <h1 style="color: red;">Sidebar</h1>
                    <ul>
            <li><a href="manageSellers.php">Manage Sellers</a></li>
            <li><a href="manageBuyers.php">Manage Buyers</a></li>
            <li><a href="logouta.php">Logout</a></li>
        </ul>
            </div>
            <div class="main">
                <div class="welcome-message">
                    <h2 style="margin-right: 190px;">Hello, Admin!</h2>
                </div>
                <div class="summary">
                    <div class="summary-item">
                        <h3>Total Sellers</h3>
                            <?php
                                // Database connection code
                                $dsn = "mysql:host=localhost;dbname=shecycleshop";
                                $username = "root";
                                $password = "";
                                $tableName = "sellers";

                                try {
                                    $pdo = new PDO($dsn, $username, $password);
                                } catch (PDOException $e) {
                                    die("Database connection failed: " . $e->getMessage());
                                }

                                // SQL query to count the rows
                                $sql = "SELECT COUNT(*) as row_count FROM $tableName";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();

                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                $rowCount = $result['row_count'];

                                // Output the row count within the <p> tag
                                echo "<p>$rowCount</p>";
                            ?>
                    </div>
                    <div class="summary-item">
                        <h3>Total Buyers</h3>
                            <?php
                        // Database connection code
                                $dsn = "mysql:host=localhost;dbname=shecycleshop";
                                $username = "root";
                                $password = "";
                                $tableName = "buyers";

                                try {
                                    $pdo = new PDO($dsn, $username, $password);
                                } catch (PDOException $e) {
                                    die("Database connection failed: " . $e->getMessage());
                                }

                                // SQL query to count the rows
                                $sql = "SELECT COUNT(*) as row_count FROM $tableName";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();

                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                $rowCount = $result['row_count'];

                                // Output the row count within the <p> tag
                                echo "<p>$rowCount</p>";
                            ?>
                    </div>
                </div>
            </div>
            
<script >
    
       
         function logout() {
        // You can perform any necessary logout actions here, such as clearing session data or redirecting to a login page.
        // For demonstration purposes, we'll simply redirect to the login page.
        window.location.href = "index.php"; // Replace with the actual logout URL
    }

    // Attach the logout function to the "Logout" link's click event
    document.getElementById('logout').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default link behavior
        logout(); // Call the logout function
    });
    

    
</script>
</body>
</html>
