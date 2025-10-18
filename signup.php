<?php


session_start();
require_once 'db_config.php';
// Load categories for navbar
$category = mysqli_query($conn, "SELECT * FROM nav_categories");

// -- HANDLE SIGNUP SUBMIT --
$signup_errors = [];
$signup_success = false;
if (isset($_POST['create'])) {
  // sanitize basic inputs
  $Fname = trim($_POST['Fname'] ?? '');
  $Lname = trim($_POST['Lname'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  // basic validation
  if ($Fname === '' || $Lname === '' || $email === '' || $password === '') {
    $signup_errors[] = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $signup_errors[] = "Invalid email address.";
  } else {
    // server-side password policy check (same 8 characteristics)
    $pw_errors = [];
    
    if (strlen($password) > 128)
      $pw_errors[] = "Password too long.";
    if (!preg_match('/[a-z]/', $password))
      $pw_errors[] = "Include a lowercase letter.";
    if (!preg_match('/[A-Z]/', $password))
      $pw_errors[] = "Include an uppercase letter.";
    if (!preg_match('/\d/', $password))
      $pw_errors[] = "Include a digit.";
    if (!preg_match('/[!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\\|`~]/', $password))
      $pw_errors[] = "Include a special character.";
    if (preg_match('/\s/', $password))
      $pw_errors[] = "Password must not contain spaces.";
    $common = ['password', '123456', 'qwerty', 'admin', 'letmein', 'welcome', 'abc123', 'password1'];
    foreach ($common as $c) {
      if (stripos($password, $c) !== false) {
        $pw_errors[] = "Password is too common or predictable.";
        break;
      }
    }

    if (!empty($pw_errors)) {
      $signup_errors = array_merge($signup_errors, $pw_errors);
    } else {
      // check duplicate email
      $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
      $check->bind_param("s", $email);
      $check->execute();
      $check->store_result();
      if ($check->num_rows > 0) {
        $signup_errors[] = "Email already registered. Try logging in or use a different email.";
      } else {
        $check->close();
        // hash password and insert
       ;
        $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $Fname, $Lname, $email, $password);
        if ($stmt->execute()) {
          $signup_success = true;
        } else {
          $signup_errors[] = "Insert failed: " . $stmt->error;
        }
        $stmt->close();
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Account - Trendy Wear</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="main.css" />
  <style>
    /* Minimal styles for checklist + generator (adjust with your main.css) */
    .signup {
      max-width: 420px;
      margin: 30px auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
    }

    .pw-help {
      display: none;
      position: relative;
      margin-top: 10px;
      padding: 12px;
      border: 1px solid #e6e6e6;
      border-radius: 6px;
      background: #fafafa;
    }

    .pw-help.visible {
      display: block;
    }

    .pw-checklist {
      list-style: none;
      padding: 0;
      margin: 0 0 10px 0;
    }

    .pw-checklist li {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 6px 0;
      color: #666;
    }

    .pw-checklist li.valid {
      color: #138000;
      font-weight: 600;
    }

    .pw-checklist li .dot {
      width: 18px;
      text-align: center;
      display: inline-block;
    }

    .generator {
      display: flex;
      gap: 8px;
      align-items: center;
      margin-top: 8px;
    }

    .generator button {
      white-space: nowrap;
    }

    .small-note {
      font-size: 13px;
      color: #666;
      margin-top: 6px;
    }

    .alert-box {
      margin-bottom: 12px;
    }
     .type {
      display: none;
      position: absolute;
      top: 11%;
      left: 111px;
      background-color: #333;
      border-radius: 24px;
      /* or white if your theme is white */
      padding: 10px 0;
      z-index: 999;
      min-width: 200px;
    }
  </style>
</head>

<body class="bg-light">

  <!-- Navbar (kept minimal; your original markup preserved) -->
  <div class="collapse" id="navbarToggleExternalContent">
    <div class="bg-dark p-4">
      <ul>
        <li><a class="links text-white" href="index.html">Home</a></li>
        <li class="nav-item">
          <a class="links text-white" href="#">Categories</a>
          <ul class="type">
            <?php
            // reset pointer if needed
            mysqli_data_seek($category, 0);
            while ($row = mysqli_fetch_assoc($category)) {
              echo "<li><a href='#cart{$row['id']}'>{$row['name']}</a></li>";
            }
            ?>
          </ul>
        </li>
        <li><a class="links text-white" href="#policy">Policy</a></li>
        <li><a class="links text-white" href="#contactus">Contact us</a></li>
        <li><a class="links text-white" href="http://localhost/clothing%20store/myaccount/settings.php">Settings</a></li>
      </ul>
    </div>
  </div>

  <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>

  <!-- Signup Form -->
  <div class="container">
    <form action="" method="post" novalidate>
      <div class="signup">
        <h1 id="account">Create Account</h1>

        <!-- show server messages -->
        <?php if (!empty($signup_errors)): ?>
          <div class="alert alert-danger alert-box">
            <?php foreach ($signup_errors as $err)
              echo htmlentities($err) . "<br>"; ?>
          </div>
        <?php endif; ?>
        <?php if ($signup_success): ?>
          <div class="alert alert-success alert-box">Account Created Successfully! <a href="login.php">Login</a></div>
        <?php endif; ?>

        <div class="mb-2">
          <input class="form-control" type="text" placeholder="First Name" name="Fname" required
            value="<?= isset($Fname) ? htmlentities($Fname) : '' ?>" />
        </div>
        <div class="mb-2">
          <input class="form-control" type="text" placeholder="Last Name" name="Lname" required
            value="<?= isset($Lname) ? htmlentities($Lname) : '' ?>" />
        </div>
        <div class="mb-2">
          <input class="form-control" type="email" name="email" placeholder="Email" required
            value="<?= isset($email) ? htmlentities($email) : '' ?>" />
        </div>

        <div class="mb-2 position-relative">
          <input class="form-control" id="password" type="password" name="password" placeholder="Password" required
            autocomplete="new-password" />

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
          <div id="pwHelp" class="pw-help" role="region" aria-live="polite">
            <ul class="pw-checklist">
              <li id="c-length"><span class="dot">✖</span> At least 12 characters</li>
              <li id="c-lower"><span class="dot">✖</span> Lowercase letter (a–z)</li>
              <li id="c-upper"><span class="dot">✖</span> Uppercase letter (A–Z)</li>
              <li id="c-digit"><span class="dot">✖</span> A digit (0–9)</li>
              <li id="c-special"><span class="dot">✖</span> A special character (!@#...)</li>
              <li id="c-nospace"><span class="dot">✖</span> No spaces</li>
              <li id="c-common"><span class="dot">✖</span> Not a common password</li>
            </ul>

            <div class="generator">
              <button type="button" id="generateBtn" class="btn btn-outline-primary btn-sm">Generate strong
                password</button>
              <button type="button" id="copyBtn" class="btn btn-outline-secondary btn-sm">Copy</button>
            </div>
            <div class="small-note">You can use the generated password or type your own. The form requires these rules
              to be met.</div>
          </div>
        </div>

        <div class="d-grid gap-2">
          <button id="create" name="create" style="margin-left: 138px;" class="btn btn-dark">Create</button>
        </div>

        <p style="margin-top: 20px;" class="sign text-center">
          Already have an account? <a style="font-size: 15px;" href="login.php">LOGIN</a>
        </p>
      </div>
    </form>
  </div>


  <!-- Footer -->
  <div class="foot">
    <div class="footercontainer">
      <h3 style="margin-top: 7px;">About Trendy Wear</h3>
      <a href="#">ABOUT US</a>
      <a href="#">COMPANY</a>
      <a href="#">CAREERS</a>
      <a href="#">BLOGS</a>
      <a href="#">STORE LOCATORS</a>
    </div>

    <div class="footercontainer">
      <h3>MY ACCOUNT</h3>
      <a href="login.php">LOGIN</a>
      <a href="signup.php">CREATE ACCOUNT</a>
      <a href="http://localhost/clothing%20store/myaccount/settings.php">ACCOUNT INFO</a>
      <a href="http://localhost/clothing%20store/myaccount/order_status.php">ORDER HISTORY</a>
    </div>

    <div class="footercontainer">
      <h3>FIND US ON</h3>
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

  ?>


  <div class="footer">
    <p style="margin-top: 13px; font-size: 26px;">&#169; Trendy Wear copyright 2024</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Client-side: live validation + generator + copy
    (function () {
      const password = document.getElementById('password');
      const pwHelp = document.getElementById('pwHelp');
      const checks = {
        length: document.getElementById('c-length'),
        lower: document.getElementById('c-lower'),
        upper: document.getElementById('c-upper'),
        digit: document.getElementById('c-digit'),
        special: document.getElementById('c-special'),
        nospace: document.getElementById('c-nospace'),
        common: document.getElementById('c-common')
      };

      const commonList = ['password', '123456', 'qwerty', 'admin', 'letmein', 'welcome', 'abc123', 'password1'];

      function updateChecks(val) {
        // length
        if (val.length >= 12) markValid(checks.length); else markInvalid(checks.length);
        // lower
        if (/[a-z]/.test(val)) markValid(checks.lower); else markInvalid(checks.lower);
        // upper
        if (/[A-Z]/.test(val)) markValid(checks.upper); else markInvalid(checks.upper);
        // digit
        if (/\d/.test(val)) markValid(checks.digit); else markInvalid(checks.digit);
        // special
        if (/[!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\\|`~]/.test(val)) markValid(checks.special); else markInvalid(checks.special);
        // nospace
        if (!/\s/.test(val) && val.length > 0) markValid(checks.nospace); else markInvalid(checks.nospace);
        // common
        let isCommon = false;
        for (let c of commonList) if (val.toLowerCase().includes(c)) { isCommon = true; break; }
        if (!isCommon && val.length > 0) markValid(checks.common); else markInvalid(checks.common);
      }

      function markValid(el) {
        el.classList.add('valid');
        el.querySelector('.dot').textContent = '✔';
      }
      function markInvalid(el) {
        el.classList.remove('valid');
        el.querySelector('.dot').textContent = '✖';
      }

      // show/help when focusing
      password.addEventListener('focus', () => {
        pwHelp.classList.add('visible');
        updateChecks(password.value || '');
      });
      password.addEventListener('input', () => {
        updateChecks(password.value);
      });
      // hide help when clicking outside (optional)
      document.addEventListener('click', (e) => {
        if (!pwHelp.contains(e.target) && e.target !== password) {
          // keep visible if user is typing; hide only if they clicked elsewhere
          pwHelp.classList.remove('visible');
        }
      });

      // generator
      const generateBtn = document.getElementById('generateBtn');
      const copyBtn = document.getElementById('copyBtn');

      function generatePassword(len = 16) {
        const lower = 'abcdefghijklmnopqrstuvwxyz';
        const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const digits = '0123456789';
        const special = '!@#$%^&*()-_+=[]{};:,.<>?';
        const all = lower + upper + digits + special;
        // ensure at least one of each required
        let pw = [];
        pw.push(randomFrom(lower));
        pw.push(randomFrom(upper));
        pw.push(randomFrom(digits));
        pw.push(randomFrom(special));
        for (let i = pw.length; i < len; i++) pw.push(randomFrom(all));
        // shuffle
        for (let i = pw.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [pw[i], pw[j]] = [pw[j], pw[i]];
        }
        return pw.join('');
      }
      function randomFrom(s) { return s[Math.floor(Math.random() * s.length)]; }

      generateBtn.addEventListener('click', () => {
        const gen = generatePassword(16);
        password.value = gen;
        updateChecks(gen);
        pwHelp.classList.add('visible');
        // briefly highlight input
        password.focus();
      });

      copyBtn.addEventListener('click', async () => {
        try {
          await navigator.clipboard.writeText(password.value);
          copyBtn.textContent = 'Copied!';
          setTimeout(() => copyBtn.textContent = 'Copy', 1500);
        } catch (err) {
          copyBtn.textContent = 'Copy (failed)';
          setTimeout(() => copyBtn.textContent = 'Copy', 1500);
        }
      });

      // Prevent submit if client-side checks not all valid
      const form = document.querySelector('form');
      form.addEventListener('submit', (e) => {
        // check all checklist items have valid class
        const allValid = Object.values(checks).every(ch => ch.classList.contains('valid'));
        if (!allValid) {
          e.preventDefault();
          // show help and focus password
          pwHelp.classList.add('visible');
          password.focus();
          alert('Please make sure your password meets all requirements.');
        }
      });

    })();


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
</body>

</html>