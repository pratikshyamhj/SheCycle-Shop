<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database_name = 'shecycleshop';

$conn = new mysqli($hostname, $username, $password, $database_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteSql = "DELETE FROM buyers WHERE id = $deleteId";

    if ($conn->query($deleteSql) === TRUE) {
        // Record deleted successfully
    } else {
        echo "Error deleting record: " . $conn->error;
    }


    $resetAutoIncrementSql = "ALTER TABLE buyers AUTO_INCREMENT = 1";
    if ($conn->query($resetAutoIncrementSql) === TRUE) {
        
    } else {
        echo "Error resetting primary key: " . $conn->error;
    }
}

$sql = "SELECT * FROM buyers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        *{
            margin: 0; padding: 0; box-sizing: border-box; 
            font-family: sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            border-color: darkred;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            background-color: lavenderblush;
        }

        th {
            background-color: lightcoral;
        }
        h1{
            text-align: center;
        }
        body{
            background-color: maroon;
        }
        @media (max-width: 850px) {
            table {
                font-size: 14px; /* Reduce font size for smaller screens */
            }

            th, td {
                padding: 6px; /* Adjust padding for smaller screens */
            }
        }
        


    </style>
</head>
<body>
    <h1 >Buyer list</h1>
    <a href="adminDashboard.php" style="font-weight: bold; color: white;">Back to dashboard</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Location</th>
            <th>Province</th>
            <th>city</th>
            <th>Distrinct</th>
            <th>created at</th>
            <th>is verified</th>
            <th>operation</th>

            

            <!-- Add more table headers as needed -->
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['gender'] . "</td>";
                echo "<td>" . $row['location'] . "</td>";
                echo "<td>" . $row['province'] . "</td>";
                echo "<td>" . $row['city'] . "</td>";
                echo "<td>" . $row['district'] . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "<td>" . $row['is_verified'] . "</td>";
                echo "<td class='action-column'>";
                echo "<a href='../Buyer/editProfile.php?id=" . $row['id'] . "' class='action-button edit'>Edit</a>";
                echo " ";
                 echo "<a href='?delete_id=" . $row['id'] . "' class='action-button delete'> Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No data found</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
