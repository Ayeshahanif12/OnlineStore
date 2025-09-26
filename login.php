<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

$category = mysqli_query($conn, "SELECT * FROM nav_categories ");






if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['email'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE email='$username' AND password='$password'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);

    $_SESSION['user_id'] = $user['id']; // ✅ yahan user_id session me save ho gaya
    $_SESSION['username'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];  // role session me save

    if ($user['role'] == "admin") {
      header("Location: http://localhost/clothing%20store/adminpanel/adminpage.php");
    } else {
      header("Location: index.php");
    }
  } else {
    echo "<script>alert('Invalid username or password');</script>";
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="main.css">

</head>

<body>
  <div class="collapse" id="navbarToggleExternalContent">
    <div class="bg-dark p-4">
      <span class="text-muted"></span>
      <ul>
        <li> <a class="links" href="index.php">Home</a></li>
        <li class="nav-item">
          <a class="links" href="#">Categories</a>
          <ul class="type">
            <?php
            while ($row = mysqli_fetch_assoc($category)) {
              echo "<li><a href='#cart{$row['id']}'>{$row['name']}</a></li>";
            }
            ?>
          </ul>
        </li>

        <li> <a class="links" href="#policy">Policy</a> </li>
        <li> <a class="links" href="#contactus">Contact us</a> </li>
    </div>
  </div>
  </ul>
  <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>
  <form action="" method="post">
    <div class="signup">
      <h1 id="account">Log in</h1>

 <div class="mb-2 position-relative">
    <input type="email" class="form-control pe-5" name="email" id="email" placeholder="email"
          required />
          </div>
      <div class="mb-2 position-relative">
        <input type="password" class="form-control pe-5" name="password" id="password" placeholder="Password"
          required />

        <!-- Eye Toggle -->
        <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer;"
          onclick="togglePassword()">
          <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" class="bi bi-eye"
            viewBox="0 0 16 16">
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 
               5.5 8 5.5S16 8 16 8z" />
            <path d="M8 5.5a2.5 2.5 0 1 1 0 
               5 2.5 2.5 0 0 1 0-5z" />
          </svg>
        </span>
      </div>
<button id="create" name="login" style="margin-left: 5px;" class="btn btn-dark">login</button><br>
      <a href="http://localhost/clothing%20store/myaccount/forget_password.php">Forgot Password</a>
      <a style="display: block;
        margin: 0 auto;
        width: fit-content;
        color: black;" href="signup.php">Create Account</a>
    </div>
  </form>



  <div class="foot">
    <div class="footercontainer">
      <h3 style="margin-top: 7px;">About Trendy Wear</h3>
      <a href="">ABOUT US </a>
      <a href="">COMPANY</a>
      <a href="">CAREERS</a>
      <a href="">BLOGS</a>
      <a href="">STORE LOCATORS</a>
    </div>

    <div class="footercontainer">
      <h3>MY ACCOUNT</h3>
      <a href="login.html">LOGIN</a>
      <a href="signup.html">CREATE ACCOUNT</a>
      <a href="signup.html">ACCOUNT INFO</a>
      <a href="#">ORDER HISTORY</a>
      <a href="#">ORDER HISTORY</a>
    </div>

    <div class="footercontainer">
      <h3 style="margin-top: 1px;">FIND US ON</h3>
      <a href="#">INSTAGRAM</a>
      <a href="#">FACEBOOK</a>
      <a href="#">TWITTER</a>
      <a href="#">WHATSAPP</a>
      <a href="#">YOUTUBE</a>
    </div>
    <!-- NEWSLETTER -->
    <div class="footercontainer">
      <h3 style="margin-top: 1px;">SIGN UP FOR UPDATES</h3>
      <P>By entering your email address below, you consent to receiving <br> our newsletter with access to our latest
        collections, events and initiatives. more details on this <br> are provided in our Privacy Policy.</P>
      <form action="" method="post">
        <input type="email" name="email" id="" placeholder="Email Address">
        <input type="tel" name="whatsapp" id="" placeholder="Whatsapp Number">
        <button class="send-btn" name="subscribe">Subscribe</button>
      </form>
    </div>

  </div>

  <?php
  // CONNECTING NEWSLETTER WITH PHP
  
  // Connect to database
  $conn = mysqli_connect("localhost", "root", "", "clothing_store");

  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  if (isset($_POST['subscribe'])) {
    $email = $_POST['email'];
    $whatsapp = $_POST['whatsapp'];

    // Optional: Input Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "<script>alert('Invalid email');</script>";
    } elseif (!preg_match('/^\d{11}$/', $whatsapp)) {
      echo "<script>alert('WhatsApp number must be 11 digits');</script>";
    } else {
      // Fix: use correct column name 'whatsappno'
      $stmt = mysqli_prepare($conn, "INSERT INTO newsletter (email, whatsappno) VALUES (?, ?)");
      mysqli_stmt_bind_param($stmt, "ss", $email, $whatsapp);
      $exe = mysqli_stmt_execute($stmt);

      if ($exe) {
        echo "<script>alert('Subscription successful');</script>";
      } else {
        echo "<script>alert('Insert failed: " . mysqli_error($conn) . "');</script>";
      }

      mysqli_stmt_close($stmt);
    }
  }

  mysqli_close($conn);
  ?>


  <div class="footer">
    <p style="margin-top: 13px; font-size: 26px;">&#169; trendywear copyright 2024</p>
  </div>

</body>
<script>
  function togglePassword() {
    const passwordInput = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      eyeIcon.setAttribute("class", "bi bi-eye-slash");
      eyeIcon.innerHTML = `<path d="M13.359 11.238l2.147 
    2.147-.708.707-2.147-2.147A7.487 
    7.487 0 0 1 8 13.5c-5 0-8-5.5-8-5.5a15.45 
    15.45 0 0 1 3.582-4.243l-2.147-2.147.707-.707 
    12 12-.707.707-2.076-2.076z"/>`;
    } else {
      passwordInput.type = "password";
      eyeIcon.setAttribute("class", "bi bi-eye");
      eyeIcon.innerHTML = `<path d="M16 8s-3-5.5-8-5.5S0 
    8 0 8s3 5.5 8 5.5S16 8 16 
    8z"/><path d="M8 5.5a2.5 2.5 0 1 
    1 0 5 2.5 2.5 0 0 1 0-5z"/>`;
    }
  }
</script>


</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>