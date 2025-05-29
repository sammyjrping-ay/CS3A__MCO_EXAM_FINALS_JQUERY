<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_SESSION['email']);
    $otp = trim($_POST['otp']);

    // Get user_id from email
    $get_user_sql = "SELECT id FROM users WHERE email = ?";
    $get_user_stmt = $conn->prepare($get_user_sql);
    $get_user_stmt->bind_param("s", $email);
    $get_user_stmt->execute();
    $user_result = $get_user_stmt->get_result();

    if ($user_result->num_rows === 1) {
        $user = $user_result->fetch_assoc();
        $user_id = $user['id'];

        // Check OTP from emailverify
        $check_sql = "SELECT otp_code, otp_expiry, NOW() as current_db_time FROM emailverify WHERE user_id = ?";
        $check_stmt = $conn->prepare($check_sql);

        if (!$check_stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Debug output
            echo "<pre>Database Record:";
            print_r($row);
            echo "Current Time: " . date('Y-m-d H:i:s') . "</pre>";

            // Verify OTP and check expiry
            if ($row['otp_code'] == $otp && strtotime($row['otp_expiry']) >= time()) {
                // Mark as verified
                $update_sql = "UPDATE emailverify SET is_verified = 1 WHERE user_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $user_id);

                if ($update_stmt->execute()) {
                    header("Location: ../feed.php?email=" . urlencode($email));
                    exit();
                } else {
                    echo "Verification update failed: " . $update_stmt->error;
                }
            } else {
                if ($row['otp_code'] != $otp) {
                    echo "OTP code mismatch. Please try again.";
                } else {
                    echo "OTP has expired. Please request a new one.";
                }
            }
        } else {
            echo "No OTP found for this email. Please request a new OTP.";
        }

        $check_stmt->close();
    } else {
        echo "User not found.";
    }
}
?>
