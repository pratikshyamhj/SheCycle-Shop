<?php
// Start the session
session_start();

// Include database connection file
include '../db_connect.php';

// Check if the buyer is logged in
if (!isset($_SESSION['buyer_id'])) {
    header("Location: BuyerLogin.php");
    exit();
}

// Fetch the buyer's current details using their session buyer_id
$buyer_id = $_SESSION['buyer_id'];
$sql = "SELECT * FROM buyers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();
$buyer = $result->fetch_assoc();

// Close the connection after fetching
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Buyer Profile - SheCycle Shop</title>
    <link rel="stylesheet" type="text/css" href="../stylesheet.css">
    <link href="https://fonts.googleapis.com/css?family=Cormorant+Infant&display=swap" rel="stylesheet">

    <script>
        const data = {
             "Province 1": {
                "Biratnagar": ["Morang", "Sunsari", "Jhapa"],
                "Ilam": ["Ilam", "Jhapa"],
                "Koshi": ["Khotang", "Okhaldhunga", "Bhojpur"],
                "Udayapur": ["Udayapur", "Sunsari"],
                "Dhankuta": ["Dhankuta", "Ilam"],
                
            },
            "Province 2": {
                "Janakpur": ["Dhanusa", "Mahottari", "Sarlahi"],
                "Birgunj": ["Parsa", "Rautahat", "Bara"],
                "Saptari": ["Saptari", "Siraha"],
                "Sarlahi": ["Sarlahi", "Mahottari"],
                "Rautahat": ["Rautahat", "Bara"],
                // Add more cities and districts if necessary
            },
            "Bagmati Province": {
                "Kathmandu": ["Kathmandu", "Bhaktapur", "Lalitpur"],
                "Chitwan": ["Chitwan", "Nawalpur"],
                "Dolakha": ["Dolakha", "Ramechhap"],
                "Rasuwa": ["Rasuwa", "Sindhupalchok"],
                "Sindhuli": ["Sindhuli", "Sarlahi"],
                
            },
            "Gandaki Province": {
                "Pokhara": ["Kaski", "Syangja", "Parbat"],
                "Gorkha": ["Gorkha", "Lamjung"],
                "Baglung": ["Baglung", "Myagdi"],
                "Manang": ["Manang"],
                "Mustang": ["Mustang"],
                
            },
            "Lumbini Province": {
                "Butwal": ["Rupandehi", "Nawalparasi"],
                "Bhairahawa": ["Rupandehi", "Kapilvastu"],
                "Palpa": ["Palpa", "Nawalparasi"],
                "Rupandehi": ["Rupandehi"],
                "Kapilvastu": ["Kapilvastu"],
                
            },
            "Karnali Province": {
                "Birendranagar": ["Surkhet", "Dailekh", "Jumla"],
                "Jumla": ["Jumla"],
                "Mugu": ["Mugu"],
                "Dolpa": ["Dolpa"],
                "Kalaiya": ["Kalaiya", "Bardiya"],
                
            },
            "Sudurpashchim Province": {
                "Mahendranagar": ["Kanchanpur", "Baitadi"],
                "Dhangadhi": ["Doti", "Achham"],
                "Dadeldhura": ["Dadeldhura"],
                "Baitadi": ["Baitadi"],
                "Kanchanpur": ["Kanchanpur"],
                
            }
        };

        function populateCities() {
            const province = document.getElementById('province').value;
            const citySelect = document.getElementById('city');
            citySelect.innerHTML = '<option value="">Select City</option>';

            if (province && data[province]) {
                for (const city in data[province]) {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    citySelect.appendChild(option);
                }
            }
            populateDistricts();
        }

        function populateDistricts() {
            const province = document.getElementById('province').value;
            const city = document.getElementById('city').value;
            const districtSelect = document.getElementById('district');
            districtSelect.innerHTML = '<option value="">Select District</option>';

            if (province && city && data[province][city]) {
                data[province][city].forEach(district => {
                    const option = document.createElement('option');
                    option.value = district;
                    option.textContent = district;
                    districtSelect.appendChild(option);
                });
            }
        }

        // Function to set the selected values based on the existing profile data
        function setSelectedValues() {
            document.getElementById('province').value = '<?php echo $buyer['province']; ?>';
            populateCities();
            document.getElementById('city').value = '<?php echo $buyer['city']; ?>';
            populateDistricts();
            document.getElementById('district').value = '<?php echo $buyer['district']; ?>';
        }

        window.onload = setSelectedValues;
    </script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="form-container">
        <h1>Edit Buyer Profile</h1>
        <form action="update_profile_process.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($buyer['name']); ?>" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" pattern="^(?=.*[a-zA-Z])(?=.*\d.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($buyer['email']); ?>" required>
            
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo ($buyer['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($buyer['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($buyer['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
            
            <label for="location">Address:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($buyer['location']); ?>" required>

            <label for="province">Province:</label>
            <select id="province" name="province" onchange="populateCities()" required>
                <option value="">Select Province</option>
                <option value="Province 1">Province 1</option>
                <option value="Province 2">Province 2</option>
                <option value="Bagmati Province">Bagmati Province</option>
                <option value="Gandaki Province">Gandaki Province</option>
                <option value="Lumbini Province">Lumbini Province</option>
                <option value="Karnali Province">Karnali Province</option>
                <option value="Sudurpashchim Province">Sudurpashchim Province</option>
            </select>

            <label for="city">City:</label>
            <select id="city" name="city" onchange="populateDistricts()" required>
                <option value="">Select City</option>
            </select>

            <label for="district">District:</label>
            <select id="district" name="district" required>
                <option value="">Select District</option>
            </select>
            
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
