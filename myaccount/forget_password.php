<?php
session_start();
require 'config.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

if (isset($_POST['send_otp'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    $result = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($result) > 0) {
        $otp = rand(100000, 999999); // 6 digit OTP

        // store OTP & expiry in session (simple). Better: store in DB with expiry.
        $_SESSION['otp'] = $otp;
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp_time'] = time(); // for expiry check (e.g., 10 min)

        // PHPMailer + SendGrid
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // Enable verbose debug output
            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
            $mail->Debugoutput = 'html'; // Browser mein behtar view ke liye

            $mail->isSMTP();
            $mail->Host = MAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_USERNAME; // 'apikey'
            $mail->Password = MAIL_PASSWORD; // your SendGrid key
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Password Reset OTP";
            $mail->Body    = "Your OTP code is: <b>$otp</b>. It will expire in 10 minutes.";

            $mail->send();
            echo "<script>alert('OTP sent to your email!'); window.location.href='verify_otp.php';</script>";
            exit;
        } catch (Exception $e) {
            error_log("Mail error: " . $mail->ErrorInfo);
            // User ko wazeh error message dikhayein
            $errorMessage = "Could not send email. Mailer Error: " . $mail->ErrorInfo;
            echo "<script>alert('" . addslashes($errorMessage) . "');</script>";
        }
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6fb;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      width: 350px;
    }
    h2 {
      text-align: center;
      color: #0d47a1;
      margin-bottom: 20px;
    }
    label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    input[type="email"] {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    button {
      margin-top: 18px;
      padding: 12px;
      width: 100%;
      background: #0d47a1;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 15px;
      transition: 0.3s;
    }
    button:hover {
      background: #08306b;
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 15px;
      text-decoration: none;
      font-weight: bold;
      color: #0d47a1;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Forgot Password</h2>
    <form method="POST">
      <label>Email:</label>
      <input type="email" name="email" placeholder="Enter your registered email" required>
      <button type="submit" name="send_otp">Send OTP</button>
    </form>
    <a href="http://localhost/clothing%20store/login.php" class="back-link">Back to Login</a>
  </div>
</body>
</html>
