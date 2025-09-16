<!DOCTYPE html>
<html>
<head>
    <title>Seller Registration - SheCycle Shop</title>
    <link rel="stylesheet" type="text/css" href="../stylesheet.css">
    <link href="https://fonts.googleapis.com/css?family=Cormorant+Infant&display=swap" rel="stylesheet">
    <style>
        .form-container input {
  
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
        header{
                    background: transparent;
                    font-size: 30px;
                    text-align: left;

                }

    </style>

    <script>
        // Data for provinces, cities, and districts
        const data = {
            "Koshi": {
                "Biratnagar": ["Morang", "Sunsari", "Jhapa"],
                "Ilam": ["Ilam", "Jhapa"],
                "Koshi": ["Khotang", "Okhaldhunga", "Bhojpur"],
                "Udayapur": ["Udayapur", "Sunsari"],
                "Dhankuta": ["Dhankuta", "Ilam"],
                
            },
            "Madhesh": {
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
    </script>

</head>
<body style="background-image: url('../Picture/sl.png');">
    <header>
        
            
                <a href="../index.php"><img src="../Picture/home.png"></a>
            
    </header>
    
    <div class="form-container">
        <h1>Seller Registration</h1>
        <form action="seller_register_process.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" pattern="^(?=.*[a-zA-Z])(?=.*\d.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" required
                   title="Password must be at least 8 characters long, with at least one special character, and at least two numbers">
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            

            <label for="province">Province:</label>
            <select id="province" name="province" onchange="populateCities()" required>
                <option value="">Select Province</option>
                <option value="Province 1">Koshi Province</option>
                <option value="Province 2">Madhesh Province</option>
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
            </select><br>
            <label for="location">Address:</label>
            <input type="text" id="location" name="location" required><br>
            
            
            
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
