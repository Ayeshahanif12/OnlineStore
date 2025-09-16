<?php
session_start();
if (isset($_POST['verify'])) {
    $entered_otp = trim($_POST['otp']);
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_time'])) {
        echo "<script>alert('No OTP requested.'); window.location='forget_password.php';</script>";
        exit;
    }
    // expiry (600 seconds = 10 minutes)
    if (time() - $_SESSION['otp_time'] > 600) {
        unset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['reset_email']);
        echo "<script>alert('OTP expired. Please request again.'); window.location='forget_password.php';</script>";
        exit;
    }

    if ($entered_otp == $_SESSION['otp']) {
        $_SESSION['verified'] = true;
        header("Location: reset_password.php");
        exit;
    } else {
        echo "<script>alert('Invalid OTP');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify OTP</title>
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
      text-align: center;
    }
    h2 {
      color: #0d47a1;
      margin-bottom: 20px;
    }
    input[type="text"] {
      width: 90%;
      padding: 12px;
      margin-top: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
      text-align: center;
      letter-spacing: 4px;
    }
    button {
      margin-top: 18px;
      padding: 12px;
      width: 29%;
      background: #0d47a1;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s;
    }
    button:hover {
      background: #08306b;
    }
    .back-link {
      display: block;
      margin-top: 15px;
      font-weight: bold;
      color: #0d47a1;
      text-decoration: none;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Verify OTP</h2>
    <form method="POST">
      <input type="text" name="otp" placeholder="Enter OTP" required>
      <button type="submit" name="verify">Verify</button>
    </form>
    <a href="forget_password.php" class="back-link">Back</a>
  </div>
</body>
</html>
