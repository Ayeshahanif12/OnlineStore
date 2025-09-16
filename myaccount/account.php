<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $updateQuery = "UPDATE users SET fname='$fname', lname='$lname', email='$email' WHERE id='$user_id'";
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Profile Updated!'); window.location.href='account.php';</script>";
    }
}

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $check = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
    $row = mysqli_fetch_assoc($check);

    if (password_verify($current_password, $row['password'])) {
        mysqli_query($conn, "UPDATE users SET password='$new_password' WHERE id='$user_id'");
        echo "<script>alert('Password changed successfully!'); window.location.href='account.php';</script>";
    } else {
        echo "<script>alert('Current password incorrect!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Account</title>
  <!-- Font Awesome for eye icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6fb;
      padding: 30px;
    }
    .account-container {
      max-width: 550px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    h2, h3 {
      text-align: center;
      margin-bottom: 15px;
      color: #0d47a1;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 12px;
    }
    input {
      width: 90%;
      padding: 10px;
      margin-top: 6px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .btn {
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
    .btn:hover {
      background: #08306b;
    }
    .link-btn {
      display: block;
      text-align: center;
      margin-top: 15px;
      color: #0d47a1;
      text-decoration: none;
      font-weight: bold;
    }
    .link-btn:hover {
      text-decoration: underline;
    }
    hr {
      margin: 25px 0;
      border: 0;
      height: 1px;
      background: #ddd;
    }

    /* Password wrapper + eye button */
    .password-wrapper {
      position: relative;
    }
    .password-wrapper input {
      padding-right: 40px; /* space for eye */
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
  </style>
</head>
<body>
  <div class="account-container">
    <h2>My Account</h2>
    
    <!-- Profile Update -->
    <form method="POST">
      <label>First Name</label>
      <input type="text" name="fname" value="<?= $user['fname'] ?>" required>

      <label>Last Name</label>
      <input type="text" name="lname" value="<?= $user['lname'] ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?= $user['email'] ?>" required>

      <button type="submit" name="update" class="btn">Update Profile</button>
    </form>

    <hr>

    <!-- Change Password -->
    <h3>Change Password</h3>
    <form method="POST">
      <label>Current Password</label>
      <div class="password-wrapper">
        <input type="password" id="current_password" name="current_password" required>
        <button type="button" class="password-toggle" onclick="togglePassword('current_password', this)">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>

      <label>New Password</label>
      <div class="password-wrapper">
        <input type="password" id="new_password" name="new_password" required>
        <button type="button" class="password-toggle" onclick="togglePassword('new_password', this)">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>

      <button type="submit" name="change_password" class="btn">Change Password</button>
    </form>

    <a href="forget_password.php" class="link-btn">Forgot Password?</a>
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
  </script>
</body>
</html>
