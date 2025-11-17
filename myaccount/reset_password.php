<?php

include '../configs.php';

if (!isset($_SESSION['verified']) || !$_SESSION['verified']) {
    header("Location: forget_password.php");
    exit;
}

if (isset($_POST['reset'])) {
    $newPass = $_POST['password'];
    $confirmPass = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    if ($newPass !== $confirmPass) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
        $email_safe = mysqli_real_escape_string($conn, $email);

        $query = "UPDATE users SET password='$hashedPass' WHERE email='$email_safe'";
        if (mysqli_query($conn, $query)) {
            unset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['verified'], $_SESSION['reset_email']);
            echo "<script>alert('Password Updated!'); window.location.href='" . BASE_URL . "/login.php';</script>";
            exit;
        } else {
            echo "<script>alert('Database error.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <!-- Font Awesome for eye icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6fb;
      padding: 40px;
    }
    .reset-container {
      max-width: 400px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #0d47a1;
    }
    .password-wrapper {
      position: relative;
      margin-bottom: 20px;
    }
    .password-wrapper input {
      width: 90%;
      padding: 12px;
      padding-right: 40px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    .password-toggle {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      font-size: 18px;
      color: #333;
    }
    .btn {
      width: 30%;
      padding: 12px;
      margin-left: 130px;
      background: #0d47a1;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 15px;
      transition: 0.3s;
    }
    .btn:hover {
      background: #08306b;
    }
  </style>
</head>
<body>
  <div class="reset-container">
    <h2>Reset Password</h2>
    <form method="POST" onsubmit="return validatePasswords()">
      <!-- New Password -->
      <div class="password-wrapper">
        <input type="password" id="password" name="password" placeholder="New Password" required>
        <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>

      <!-- Confirm Password -->
      <div class="password-wrapper">
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>

      <button type="submit" name="reset" class="btn">Reset</button>
    </form>
  </div>

  <script>
    function togglePassword(fieldId, btn) {
      const input = document.getElementById(fieldId);
      const icon = btn.querySelector("i");
      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }

    function validatePasswords() {
      const pass = document.getElementById("password").value;
      const confirmPass = document.getElementById("confirm_password").value;
      if (pass !== confirmPass) {
        alert("Passwords do not match!");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
