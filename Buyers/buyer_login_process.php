<?php
include '../db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind statement to check email and password from sellers table
    $stmt = $conn->prepare("SELECT id, password, is_verified FROM buyers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $is_verified);
        $stmt->fetch();

        if ($is_verified == 1) {
            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Only store the login data if the user is verified and the password is correct
                $stmt_insert = $conn->prepare("INSERT INTO buyer_login (email, password, login_time) VALUES (?, ?, NOW())");
                $stmt_insert->bind_param("ss", $email, $hashed_password);

                if ($stmt_insert->execute()) {
                    $_SESSION['buyer_id'] = $id;
                    header("Location: BuyerHomePage.php");
                    exit();
                } else {
                    echo "Error: " . $stmt_insert->error;
                }

                $stmt_insert->close();
            } else {
                // Redirect to SellerLogin.php with an alert message for invalid password
                header("Location: BuyerLogin.php?message=" . urlencode('Invalid password!'));
                exit();
            }
        } else {
            // Redirect to SellerLogin.php with the alert message for email verification
            header("Location: BuyerLogin.php?message=" . urlencode('Please verify your email before logging in.'));
            exit();
        }
    } else {
        // Redirect to SellerLogin.php with an alert message for non-existent email
        header("Location: BuyerLogin.php?message=" . urlencode('No user found with that email!'));
        exit();
    }

    // Close the select statement and connection
    $stmt->close();
    $conn->close();
}
?>
