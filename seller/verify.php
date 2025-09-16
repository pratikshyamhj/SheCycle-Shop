<?php
include '../db_connect.php';

if (isset($_GET['email']) && isset($_GET['v_code'])) {
    $email = $_GET['email'];
    $v_code = $_GET['v_code'];

    // Prepare and bind statement
    $stmt = $conn->prepare("SELECT verification_code, is_verified FROM sellers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_v_code, $is_verified);
        $stmt->fetch();

        if ($is_verified == 0 && $v_code == $stored_v_code) {
            // Update the seller's verified status
            $stmt_update = $conn->prepare("UPDATE sellers SET is_verified = 1 WHERE email = ?");
            $stmt_update->bind_param("s", $email);
            if ($stmt_update->execute()) {
                 header("Location: SellerLogin.php?message=Email verification successful! You can now log in.");
            } else {
                header("Location: SellerLogin.php?message=Error: Could not update verification status.");
            }
            $stmt_update->close();
        } 
        else {
            header("Location: SellerLogin.php?message=Invalid verification code or email already verified.");
        }
    } else {
          header("Location: SellerLogin.php?message=No account found for this email.");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: SellerLogin.php?message=Invalid request.");
}
?>
