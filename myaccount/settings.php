<?php
session_start();
            require_once '../db_config.php';  


if (!isset($_SESSION['user_id'])) {
  die("Please login first.");
}

$userId = (int) $_SESSION['user_id'];

// Get old data
$sql = "SELECT * FROM users WHERE id = $userId";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// active section default
$activeSection = isset($_GET['section']) ? $_GET['section'] : 'profile';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fname'])) {
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $email = $_POST['email'];

  // Profile pic handle
  if (!empty($_FILES['profile_pic']['name'])) {
    $target = "uploads/" . time() . "_" . basename($_FILES['profile_pic']['name']);
    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target);
    $profilePic = $target;
  } else {
    $profilePic = $user['profile_pic'] ?? 'uploads/default-user.png';
  }

  $update = "UPDATE users 
             SET fname='$fname', lname='$lname', email='$email', profile_pic='$profilePic' 
             WHERE id=$userId";
  mysqli_query($conn, $update);

  header("Location: settings.php?section=profile");
  exit;
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current_password'])) {
  $current = $_POST['current_password'];
  $new = $_POST['new_password'];
  $confirm = $_POST['confirm_password'];

  $msg = "";
  // Check with password_verify
  if (password_verify($current, $user['password']) || $current === $user['password']) {
    if ($new === $confirm) {
      $hashed = password_hash($new, PASSWORD_DEFAULT);
      mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id=$userId");
      $msg = "✅ Password updated successfully!";
    } else {
      $msg = "❌ New and Confirm password do not match!";
    }
  } else {
    $msg = "❌ Current password is incorrect!";
  }
  $passwordMsg = $msg;
  $activeSection = 'password';
}

// Handle order tracking
// ... [Existing code for Password Update] ...

// Handle order tracking - CORRECTED AND SAFE LOGIC (TOP OF FILE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['track-order-btn'])) {

  // Initialize $orderStatus as an empty array before processing
  $orderStatus = [];

  if (!empty($_POST['order_id'])) {
    $orderId = mysqli_real_escape_string($conn, $_POST['order_id']);

    // QUERY: Select all necessary fields for the display loop.
    $orderQuery = "SELECT product_name, order_status FROM order_items WHERE order_id = '$orderId' AND user_id = $userId";
    $orderResult = mysqli_query($conn, $orderQuery);

    if ($orderResult && mysqli_num_rows($orderResult) > 0) {
      // SUCCESS: Collect all rows into the $orderStatus array
      while ($row = mysqli_fetch_assoc($orderResult)) {
        $orderStatus[] = $row;
      }
    } else {
      // ERROR: Order not found. Define $orderStatus as an array 
      $orderStatus = [
        ['order_status' => 'message', 'text' => '❌ No order found with this ID or it does not belong to your account.']
      ];
    }
  } else {
    // WARNING: Empty Order ID. Define $orderStatus as an array 
    $orderStatus = [
      ['order_status' => 'message', 'text' => '⚠️ Please enter an Order ID.']
    ];
  }

  // Ensure the shipping section is active after submission
  $activeSection = 'shipping';
}

// GLOBAL INITIALIZATION: This MUST be placed before the HTML starts.
if (!isset($orderStatus)) {
  $orderStatus = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Settings Page</title>
  <link rel="stylesheet" href="settings.css">
  <style>
    :root {
      --primary-color: #1a1a1a;
      --accent-color: #007bff;
      --danger-color: #dc3545;
      --background-color: #f4f5f7;
      --surface-color: #ffffff;
      --border-color: #e1e4e8;
      --text-color: #24292e;
      --text-muted-color: #586069;
      --sidebar-bg: #111;
      --sidebar-text: #fff;
      --sidebar-active-bg: #007bff;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      margin: 0;
      background-color: var(--background-color);
      color: var(--text-color);
      display: flex;
    }
    html, body { overflow: hidden; }

    /* --- Sidebar --- */
    .sidebar {
      width: 220px;
      height: 100vh;
      background-color: var(--sidebar-bg);
      color: var(--sidebar-text);
      position: fixed;
      top: 0;
      left: 0;
      display: flex;
      flex-direction: column;
      padding: 20px 0;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .sidebar h2 {
      text-align: center;
      font-size: 24px;
      margin-bottom: 25px;
      padding: 0 15px;
    }

    .sidebar .menu {
      flex-grow: 1;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 12px;
      color: var(--sidebar-text);
      padding: 14px 20px;
      text-decoration: none;
      transition: background 0.2s, color 0.2s;
      font-size: 15px;
    }

    .sidebar a:hover {
      background-color: #333;
    }

    .sidebar a.active {
      background-color: var(--sidebar-active-bg);
      font-weight: 600;
    }

    #logout {
      background-color: var(--danger-color);
      margin: 10px;
      border-radius: 6px;
      justify-content: center;
      font-weight: 600;
    }

    #logout:hover {
      background-color: #c82333;
    }

    /* --- Main Content --- */
    .content {
      margin-left: 220px;
      padding: 30px;
      width: calc(100% - 220px);
      overflow-y: auto;
      overflow-x: hidden;
      height: 100vh;
    }

    .section {
      display: none;
      background-color: var(--surface-color);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
      max-width: 600px;
      margin: 40px auto;
    }

    .section.active {
      display: block;
    }

    .section h3 {
      font-size: 22px;
      font-weight: 600;
      margin-top: 0;
      margin-bottom: 25px;
      text-align: center;
      color: var(--sidebar-text);
    }

    /* --- Form Styles --- */
    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      margin-bottom: 6px;
      color: var(--text-muted-color);
    }

    .form-group input {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--border-color);
      border-radius: 6px;
      font-size: 15px;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--accent-color);
      box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
    }

    .password-wrapper {
      position: relative;
    }

    .toggle-eye {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #888;
    }

    .form-button {
      width: 100%;
      padding: 12px;
      border: none;
      background-color: var(--primary-color);
      color: white;
      font-size: 16px;
      font-weight: 600;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.2s;
    }

    .form-button:hover {
      background-color: #333;
    }

    /* --- Profile Section --- */
    .profile-pic-wrapper {
      text-align: center;
      margin-bottom: 25px;
    }

    .profile-pic-wrapper img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--border-color);
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .profile-pic-wrapper img:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    /* --- Shipping/Tracking Section --- */
    #shipping-results {
      margin-top: 20px;
      background: #f9f9f9;
      padding: 15px;
      border-radius: 8px;
      box-shadow: inset 0 1px 4px rgba(0,0,0,0.06);
      max-height: 250px;
      overflow-y: auto;
    }
    #shipping-results h4 {
      color: var(--accent-color);
      border-bottom: 2px solid var(--accent-color);
      padding-bottom: 5px;
      margin-top: 0;
    }
    .shipping-item {
      margin-bottom: 12px;
      padding: 10px;
      border: 1px solid var(--border-color);
      border-radius: 6px;
      background: var(--surface-color);
    }
    .shipping-item p { margin: 0; }

    /* --- Privacy & FAQ --- */
    .policy-box h4, .faq-item {
      margin-top: 15px;
      color: var(--text-color);
      font-size: 18px;
    }
    .policy-box p, .faq-answer {
      font-size: 15px;
      color: var(--text-muted-color);
      line-height: 1.6;
      margin-bottom: 10px;
    }
    #faq {
      max-height: 500px;
      overflow-y: auto;
      padding-right: 10px;
      margin-right: 300px;
      padding-right: 10px; 
    }
    .faq-question {
      width: 100%;
      padding: 12px;
      background: #f9f9f9;
      color: var(--text-color);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      text-align: left;
      cursor: pointer;
      font-size: 16px;
      font-weight: 500;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .faq-answer {
      display: none;
      padding: 15px;
      background: #fdfdfd;
      border: 1px solid var(--border-color);
      border-top: none;
      border-radius: 0 0 8px 8px;
      margin-top: -1px;
    }

    /* --- Help/Chat Section --- */
    #help {
      max-width: 500px;
       margin: 40px ;
      margin-right: 500px;
      /* Margin is already handled by the .section class */
    }
    #chat-box {
      background: #eef0f2;
      padding: 12px;
      border-radius: 10px;
      height: 300px;
      overflow-y: auto;
      margin-bottom: 12px;
      font-size: 14px;
    }
    .user-msg, .ai-msg {
      margin: 8px 0;
      padding: 10px 14px;
      border-radius: 18px;
      max-width: 80%;
      line-height: 1.5;
      position: relative;
      padding-top: 25px; /* Space for the label */
    }
    .msg-label {
      position: absolute; top: 5px; left: 15px; font-size: 12px; font-weight: bold; color: #ccc;
    }
    .user-msg {
      background: var(--accent-color);
      color: #fff;
      margin-left: auto;
      border-bottom-right-radius: 4px;
    }
    .user-msg .msg-label {
      color: #e0e0e0;
    }
    .ai-msg {
      background: var(--surface-color);
      color: var(--text-color);
      border: 1px solid var(--border-color);
      margin-right: auto;
      border-bottom-left-radius: 4px;
    }
    .ai-msg .msg-label {
      color: #888;
    }
    #chat-controls { display: flex; gap: 8px; }
    #user-input { flex: 1; }

    /* --- Responsive --- */
    @media (max-width: 768px) {
      body { display: block; }
      .sidebar { position: relative; width: 100%; height: auto; }
      .content { margin-left: 0; padding: 20px; }
      .section { padding: 20px; }
    }
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h2>Settings</h2>
    <div class="menu">
      <a href="#" onclick="showSection('profile', event)"><i class="fa fa-user"></i> Profile</a>
      <a href="http://localhost/clothing%20store/index.php"><i class="fa fa-home"></i> Home</a>
      <a href="#" onclick="showSection('password', event)"><i class="fa fa-lock"></i> Password</a>
      <a href="#" onclick="showSection('shipping', event)"><i class="fa fa-truck"></i> Shipping</a>
      <a href="#" onclick="showSection('privacy', event)"><i class="fa fa-shield-alt"></i> Privacy</a>
      <a href="#" onclick="showSection('faq', event)"><i class="fa fa-question-circle"></i> FAQ</a>
      <a href="#" onclick="showSection('help', event)"><i class="fa fa-life-ring"></i> Help</a>
    </div>

    <a id="logout" href="http://localhost/clothing%20store/logout.php" class="btn btn-danger">Logout</a>
  </div>

  <div class="content">
    <!-- Profile -->
    <div id="profile" class="section <?php echo $activeSection === 'profile' ? 'active' : ''; ?>">
      <h3>Profile Settings</h3>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="profile-pic-wrapper">
          <label for="profile-pic" style="cursor:pointer;">
            <img id="profilePreview"
              src="<?php echo !empty($user['profile_pic']) ? $user['profile_pic'] : 'uploads/default-user.png'; ?>"
              alt="User" style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:2px solid #ccc;">
          </label>
          <input type="file" id="profile-pic" name="profile_pic" accept="image/*" style="display:none;"
            onchange="previewImage(event)">
        </div>
        <div class="form-group">
          <label for="fname">First Name:</label>
          <input type="text" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" required>
        </div>
        <div class="form-group">
          <label for="lname">Last Name:</label>
          <input type="text" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" required>
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <button type="submit" name="save_changes" class="form-button">Save Changes</button>
      </form>
    </div>

    <!-- Password -->
    <div id="password" class="section <?php echo $activeSection === 'password' ? 'active' : ''; ?>">
      <h3>Password Settings</h3>
      <form method="POST">
        <div class="form-group password-wrapper">
          <label for="current_password">Current Password</label>
          <input type="password" id="current_password" name="current_password" required>
          <i class="fa fa-eye toggle-eye" onclick="togglePassword('current_password', this)"></i>
        </div>
        <div class="form-group password-wrapper">
          <label for="new_password">New Password</label>
          <input type="password" id="new_password" name="new_password" required>
          <i class="fa fa-eye toggle-eye" onclick="togglePassword('new_password', this)"></i>
        </div>
        <div class="form-group password-wrapper">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
          <i class="fa fa-eye toggle-eye" onclick="togglePassword('confirm_password', this)"></i>
        </div>
        <a href="http://localhost/clothing%20store/myaccount/forget_password.php" style="display: block; text-align: center; margin-bottom: 15px;">Forgot Password?</a>
        <button type="submit" class="form-button" name="update_password">Update Password</button>
      </form>
      <?php if (isset($passwordMsg)) { ?>
        <p style="margin-top:15px; text-align: center; color:#333; font-weight:bold;"><?php echo $passwordMsg; ?></p>
      <?php } ?>
    </div>

    <!-- Shipping -->
    <div id="shipping" class="section <?php echo $activeSection === 'shipping' ? 'active' : ''; ?>">
      <h3>Track Your Order</h3>

      <form action="" method="POST">
        <div class="form-group">
          <label for="order_id">Order ID</label>
          <input type="text" id="order_id" name="order_id" required>
        </div>
        <button type="submit" class="form-button" name="track-order-btn">Track Order</button>
      </form>

      <?php if (isset($orderStatus) && !empty($orderStatus)) { ?>
        <div id="shipping-results">
          <h4>Order Status</h4>

          <?php foreach ($orderStatus as $item) { ?>
            <div class="shipping-item">
              <?php if ($item['order_status'] === 'message') { ?>
                <p style="font-weight:bold; color:red; margin:0;">
                  <?= htmlspecialchars($item['text']) ?></p>
              <?php } else { ?>
                <p><strong>Product:</strong>
                  <?= htmlspecialchars($item['product_name']) ?></p>
                <p><strong>Status:</strong>
                  <?php
                  // 1. Colour define karein
                  $status_color = 'blue'; // Default color
                  if ($item['order_status'] == 'completed') {
                    $status_color = 'green';
                  } elseif ($item['order_status'] == 'processing') {
                    $status_color = 'orange';
                  } elseif ($item['order_status'] == 'cancelled') {
                    $status_color = 'red';
                  }
                  ?>
                  <span style="font-weight:bold; color:<?= $status_color ?>;">

                    <?php
                    // 2. Check karein ki ye 'message' hai ya actual status
                    if ($item['order_status'] === 'message') {
                      // Agar message hai, toh kuch display na karein kyunki message upar dikh chuka hai
                      // Ya agar aapko sirf message hi dikhana hai toh isko yahan se hata dein
                    } else {
                      // Actual status ko display karein
                      echo ucfirst($item['order_status']);
                    }
                    ?>
                  </span>
                </p>
              <?php } ?>
              </div>
          <?php } ?>
          </div>
      <?php } ?>
    </div>
    <!-- Privacy -->
    <div id="privacy" class="section <?php echo $activeSection === 'privacy' ? 'active' : ''; ?>">
      <h3>Privacy Policy</h3>
      <div class="policy-box">
        <h4>1. Information We Collect</h4>
        <p> We collect your personal details (name, email, and profile picture) to personalize your account experience.
        </p>
        <h4>2. How We Use Your Information</h4>
        <p> Your information is only used for login, account security, and to improve your shopping experience on our
          clothing store. </p>
        <h4>3. Data Protection</h4>
        <p> We use secure methods to store your data and ensure your privacy is protected at all times. </p>
        <h4>4. Sharing of Information</h4>
        <p> We never sell or share your personal information with third parties without your consent. </p>
        <h4>5. Contact Us</h4>
        <p> If you have any questions regarding our Privacy Policy, please contact us via email. <a
            href="mailto:support@example.com">trendywear@gmail.com</a>. </p>
      </div>
    </div>
  </div>

  
     <!-- FAQ -->
  <div id="faq" class="section <?php echo $activeSection === 'faq' ? 'active' : ''; ?>">
    <h3 style="text-align: center; color: #111;">Frequently Asked Questions</h3>
    <div class="faq-item">
        <button class="faq-question">How can I track my order? <i class="fa fa-chevron-down"></i></button>
        <div class="faq-answer">You can track your order status in the 'Shipping' section of your account settings by entering your Order ID. You can also view all your past orders on the 'Order Status' page, accessible via the truck icon on the main site if you have placed an order.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question">What are your shipping charges and delivery times? <i class="fa fa-chevron-down"></i></button>
        <div class="faq-answer">We charge a flat rate of PKR 150 for shipping. Delivery usually takes 3-5 business days for major cities and up to 7 days for other areas in Pakistan.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question">What payment methods do you accept? <i class="fa fa-chevron-down"></i></button>
        <div class="faq-answer">We accept Cash on Delivery (COD) and online payments through PayFast, which includes debit/credit cards and mobile wallets.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question">What is your return policy? <i class="fa fa-chevron-down"></i></button>
        <div class="faq-answer">You can return items within 7 days of delivery for a refund or exchange, provided they are unworn, unwashed, and have all original tags attached.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question">How do I change my password or account details? <i class="fa fa-chevron-down"></i></button>
        <div class="faq-answer">You can update your name, email, and profile picture in the 'Profile' section. To change your password, please go to the 'Password' section.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question">What should I do if I forget my password? <i class="fa fa-chevron-down"></i></button>
        <div class="faq-answer">Click on the 'Forgot Password?' link on the login page or in the 'Password' section here. You will receive an OTP on your registered email to reset your password.</div>
    </div>
  </div>

  <!-- Help -->


  </div>

  <!-- Help -->
  <div id="help" class="section <?php echo $activeSection === 'help' ? 'active' : ''; ?>">
    <h3>AI Help Chat</h3>
    <div id="chat-box"></div>
    <div id="chat-controls">
      <input type="text" id="user-input" class="form-group input" placeholder="Type your question...">
      <button id="send-btn" onclick="sendMessage()">Send</button>
    </div>
  </div>

  <script>
    function showSection(sectionId, event) {
      document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
      document.querySelectorAll('.sidebar a').forEach(link => link.classList.remove('active'));
      document.getElementById(sectionId).classList.add('active');
      if (event) event.target.classList.add('active');
    }

    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function () {
        document.getElementById('profilePreview').src = reader.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }

    function togglePassword(id, el) {
      const input = document.getElementById(id);
      if (input.type === "password") {
        input.type = "text";
        el.classList.remove("fa-eye");
        el.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        el.classList.remove("fa-eye-slash");
        el.classList.add("fa-eye");
      }
    }

    // Chatbox send
    document.getElementById("user-input").addEventListener("keypress", function (e) {
      if (e.key === "Enter") { sendMessage(); }
    });

    async function sendMessage() {
      let input = document.getElementById("user-input");
      let message = input.value.trim();
      if (!message) return;

      let chatbox = document.getElementById("chat-box");
      chatbox.innerHTML += `<div class="user-msg">
                              <div class="msg-label">You</div>
                              <span>${message}</span>
                            </div>`;
      input.value = "";

      try {
        let res = await fetch("chat_api.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ message })
        });
        const data = await res.json();
        if (data.error) {
          chatbox.innerHTML += `<div class="ai-msg">
                                  <div class="msg-label">AI</div>
                                  <span>Error: ${data.error}</span>
                                </div>`;
        } else {
          chatbox.innerHTML += `<div class="ai-msg">
                                  <div class="msg-label">AI</div>
                                  <span>${data.reply}</span>
                                </div>`;
        }
        chatbox.scrollTop = chatbox.scrollHeight;
      }
      catch (err) {
        chatbox.innerHTML += `<div class="ai-msg"><span>Error: ${err.message}</span></div>`;
      }
    }

    // FAQ toggle
    document.querySelectorAll(".faq-question").forEach(btn => {
      btn.addEventListener("click", function () {
        const ans = this.nextElementSibling;
        ans.style.display = ans.style.display === "block" ? "none" : "block";
        this.querySelector("i").classList.toggle("fa-chevron-down");
        this.querySelector("i").classList.toggle("fa-chevron-up");
      });
    });
  </script>
</body>

</html>