<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'config.php';

session_start();
date_default_timezone_set("Asia/Manila");

// Check if email is set in session
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    die("Email not set in session.");
}

$email = trim($_SESSION['email']);
$otp = rand(100000, 999999);
$otp_expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Get user ID
$get_user_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$get_user_stmt) {
    die("Prepare failed (SELECT): " . $conn->error);
}
$get_user_stmt->bind_param("s", $email);
$get_user_stmt->execute();
$get_user_result = $get_user_stmt->get_result();

if ($get_user_result && $get_user_result->num_rows === 1) {
    $user = $get_user_result->fetch_assoc();
    $user_id = $user['id'];

    // Delete any existing OTPs for this user
    $clear_stmt = $conn->prepare("DELETE FROM emailverify WHERE user_id = ?");
    if (!$clear_stmt) {
        die("Prepare failed (DELETE): " . $conn->error);
    }
    $clear_stmt->bind_param("i", $user_id);
    $clear_stmt->execute();

    // Insert new OTP
    $insert_stmt = $conn->prepare("INSERT INTO emailverify (user_id, otp_code, otp_expiry) VALUES (?, ?, ?)");
    if (!$insert_stmt) {
        die("Prepare failed (INSERT): " . $conn->error);
    }
    $insert_stmt->bind_param("iss", $user_id, $otp, $otp_expiry);

    if ($insert_stmt->execute()) {
        // Send email
        if (sendOtpEmail($email, $otp)) {
            header("Location: ../verify.php?email=" . urlencode($email));
            exit();
        } else {
            echo "Failed to send OTP email.";
        }
    } else {
        echo "Error inserting OTP: " . $insert_stmt->error;
    }
} else {
    echo "No user found with that email.";
}

// Function to send OTP email
function sendOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sammyjrpingay@gmail.com';
        $mail->Password   = 'xhlq opyt jyap eque'; // ⚠️ Use a Gmail App Password, not your real password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email headers
        $mail->setFrom('sammyjrpingay@gmail.com', 'OTP Verification');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP code is: <b>$otp</b><br><br>
                          It will expire in 5 minutes.<br>
                          <a href='http://localhost/email/verify.php?email=" . urlencode($email) . "'>Click here to verify</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
?>
