//seller_register_process.php
<?php
include '../db_connect.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($email, $v_code)
{
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
    require 'PHPMailer/Exception.php';

    $mail = new PHPMailer(true);

    try {
        //Server settings
        
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'nirubhandari04@gmail.com';             // SMTP username
        $mail->Password   = 'ogsrsjrwctmurjao';               // SMTP password, preferably use an App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
        $mail->Port       = 465;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('nirubhandari04@gmail.com', 'SheCycle Shop');
        $mail->addAddress($email);                                  // Add recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Email Verification from SheCycle Shop';
        $mail->Body    = "Thanks for registering!<br><br>Click the link below to verify your email address:<br><a href='http://localhost/Shecycle/Seller/verify.php?email=$email&v_code=$v_code'>Verify</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $v_code = bin2hex(random_bytes(16)); // Generate verification code
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $location = $_POST['location'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $is_verified = 0; // Default to not verified

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO sellers (name, password, email, gender, location, province, city, district, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssi", $name, $password, $email, $gender, $location, $province, $city, $district, $v_code, $is_verified);

      // Execute the statement
    if ($stmt->execute()) {
        if (sendMail($email, $v_code)) {
            // Redirect to SellerLogin.php with an alert message for non-existent email
        header("Location: SellerLogin.php?message=" . urlencode('Registration successful! A verification email has been sent to email!'));
        exit();

        } else {
            echo "Error: Verification email could not be sent.";
        }
    } else {
        echo "Error: " . $stmt->error;
    }
    

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
