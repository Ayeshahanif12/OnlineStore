<?php
session_start();
include '../config.php';
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
<<<<<<< HEAD
      mysqli_stmt_bind_param($stmt, "si", $hashed, $userId);
      mysqli_stmt_execute($stmt);
=======
      mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id=$userId");
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
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
<<<<<<< HEAD
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
=======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['track-order-btn'])) {
  if (!empty($_POST['order_id'])) {
    $orderId = mysqli_real_escape_string($conn, $_POST['order_id']);
    $orderQuery = "SELECT order_status FROM order_items WHERE order_id = '$orderId' AND user_id = $userId";
    $orderResult = mysqli_query($conn, $orderQuery);

    if ($orderResult && mysqli_num_rows($orderResult) > 0) {
      $order = mysqli_fetch_assoc($orderResult);
      $orderStatus = $order['order_status']; // fixed field
    } else {
      $orderStatus = "❌ No order found with this ID.";
    }
  } else {
    $orderStatus = "⚠️ Please enter an Order ID.";
  }
  $activeSection = 'shipping';
}
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
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
<<<<<<< HEAD
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
=======
      overflow: hidden;
    }

    /* Content ka layout (desktop ke liye) */
    .content {
      margin-left: 220px;
      padding: 20px;
      display: block;
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
    }

    /* Sare sections ek hi jagah aligned rahe */
    .section {
<<<<<<< HEAD
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
=======
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 4px 12px rgba(2, 2, 2, 0.1);
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }

    /* Help chat (desktop center) */
    #help {
      position: fixed;
      top: 46%;
      left: 55%;
      transform: translate(-50%, -50%);
      background: #111;
      color: #fff;
      padding: 20px;
      border-radius: 20px;
      width: 500px;
      min-height: 300px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
      font-family: Arial, sans-serif;
      z-index: 2000;
    }

    /* Responsive - Mobile fix */
    @media (max-width: 768px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
      }

      .content {
        margin-left: 0;
      }

      #help {
        position: relative;
        top: auto;
        left: auto;
        transform: none;
        width: 100%;
        max-width: 100%;
        min-height: 200px;
        margin-top: 20px;
      }

      #faq {
        width: 100%;
        margin: 0 auto;
      }
    }

    #logout {
      position: fixed;
      bottom: 0px;
      width: 195px;
      font-size: 19px;
      font-weight: 600;
      color: white;
      text-align: center;
      background-color: #be2e2eff;
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
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

<<<<<<< HEAD
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
    #track-order-btn {
      background: #000;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      font-size: 14px;
      margin-top: 15px;
      transition: 0.3s;
    }
    #track-order-btn:hover {
      background: #333;
=======
    #faq {
      max-height: 400px;
      /* Jitna height chahiye utna set karo */
      overflow-y: auto;
      /* Sirf y-axis scroll */
      padding-right: 10px;
      /* scrollbar ke liye space */
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h2>Settings</h2>
    <div class="menu">
      <a href="#" onclick="showSection('profile', event)"><i class="fa fa-user"></i> Profile</a>
<<<<<<< HEAD
      <a href="<?php echo BASE_URL; ?>/index.php"><i class="fa fa-home"></i> Home</a>
=======
      <a href="http://localhost/clothing%20store/index.php"><i class="fa fa-home"></i> Home</a>
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
      <a href="#" onclick="showSection('password', event)"><i class="fa fa-lock"></i> Password</a>
      <a href="#" onclick="showSection('shipping', event)"><i class="fa fa-truck"></i> Shipping</a>
      <a href="#" onclick="showSection('privacy', event)"><i class="fa fa-shield-alt"></i> Privacy</a>
      <a href="#" onclick="showSection('faq', event)"><i class="fa fa-question-circle"></i> FAQ</a>
      <a href="#" onclick="showSection('help', event)"><i class="fa fa-life-ring"></i> Help</a>
    </div>

<<<<<<< HEAD
    <a id="logout" href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-danger">Logout</a>
=======
    <a id="logout" href="http://localhost/clothing%20store/logout.php" class="btn btn-danger">Logout</a>
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
  </div>

  <div class="content">
    <!-- Profile -->
<<<<<<< HEAD
    <div id="profile" class="section <?php echo $activeSection === 'profile' ? 'active' : ''; ?>">
      <h3>Profile Settings</h3>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="profile-pic-wrapper">
=======
    <div id="profile" style="width: 374px; height: auto; border-radius: 44px; margin-top: 61px;"
      class="section <?php echo $activeSection === 'profile' ? 'active' : ''; ?>">
      <h3>Profile Settings</h3>
      <form action="" method="POST" enctype="multipart/form-data">
        <div style="text-align:center; margin-bottom:20px;">
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
          <label for="profile-pic" style="cursor:pointer;">
            <img id="profilePreview"
              src="<?php echo !empty($user['profile_pic']) ? $user['profile_pic'] : 'uploads/default-user.png'; ?>"
              alt="User" style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:2px solid #ccc;">
          </label>
          <input type="file" id="profile-pic" name="profile_pic" accept="image/*" style="display:none;"
            onchange="previewImage(event)">
        </div>
<<<<<<< HEAD
        <div class="form-group">
          <label for="fname">First Name:</label>
=======
        <div style="margin-left: 20px;">
          <label>First Name:</label><br>
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
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
<<<<<<< HEAD
        <button type="submit" name="save_changes" class="form-button">Save Changes</button>
=======
        <button type="submit" name="save_changes">Save Changes</button>
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
      </form>
    </div>

    <!-- Password -->
<<<<<<< HEAD
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
        <a href="<?php echo BASE_URL; ?>/myaccount/forget_password.php" style="display: block; text-align: center; margin-bottom: 15px;">Forgot Password?</a>
        <button type="submit" class="form-button" name="update_password">Update Password</button>
      </form>
      <?php if (isset($passwordMsg)) { ?>
        <p style="margin-top:15px; text-align: center; color:#333; font-weight:bold;"><?php echo $passwordMsg; ?></p>
      <?php } ?>
    </div>

    <!-- Shipping/Order Tracking -->
    <div id="shipping" class="section <?php echo $activeSection === 'shipping' ? 'active' : ''; ?>">
      <h3>Track Your Order</h3>
      <form method="POST">
        <div class="form-group">
          <label for="order_id">Enter Your Order ID:</label>
          <input type="text" name="order_id" id="order_id" required>
        </div>
        <button type="submit" name="track-order-btn" class="form-button">Track Order</button>
      </form>

      <?php if (!empty($orderStatus)): ?>
        <div id="shipping-results">
          <h4>Order Status</h4>
          <?php foreach ($orderStatus as $item): ?>
            <div class="shipping-item">
              <p><?php echo isset($item['text']) ? htmlspecialchars($item['text']) : '<strong>' . htmlspecialchars($item['product_name']) . ':</strong> ' . htmlspecialchars($item['order_status']); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>


    <!-- Privacy Policy -->
    <div id="privacy" class="section">
=======
    <div id="password" style="max-width: 350px; min-height: 450px; margin-top: 90px;"
      class="section <?php echo $activeSection === 'password' ? 'active' : ''; ?>">
      <h3 style="text-align: center; font-size: 20px; font-weight: 700;">Password Settings</h3>
      <form style="margin-left:17px;" method="POST">
        <div class="form-control">
          <input type="password" id="current_password" name="current_password" required>
          <label for="current_password">Current Password</label>
          <i class="fa fa-eye toggle-eye" onclick="togglePassword('current_password', this)"></i>
        </div>
        <div class="form-control">
          <input type="password" id="new_password" name="new_password" required>
          <label for="new_password">New Password</label>
          <i class="fa fa-eye toggle-eye" onclick="togglePassword('new_password', this)"></i>
        </div>
        <div class="form-control">
          <input type="password" id="confirm_password" name="confirm_password" required>
          <label for="confirm_password">Confirm Password</label>
          <i class="fa fa-eye toggle-eye" onclick="togglePassword('confirm_password', this)"></i>
        </div>
        <button style="margin-left:80px;" type="submit" id="update_password" name="update_password">Update
          Password</button>
      </form>
      <?php if (isset($passwordMsg)) { ?>
        <p style="margin-top:15px; margin-left:17px; color:#333; font-weight:bold;"><?php echo $passwordMsg; ?></p>
      <?php } ?>
    </div>

    <!-- Orders -->
    <div id="orders" class="section <?php echo $activeSection === 'orders' ? 'active' : ''; ?>">
      <h3>Your Orders</h3>
      <p>View and track your orders here.</p>
    </div>

    <!-- Shipping -->
<div id="shipping" style="width:364px; margin-top: 145px; height: auto; padding:20px;"
     class="section <?php echo $activeSection === 'shipping' ? 'active' : ''; ?>">
    <h2 style="text-align: center; margin-top:15px;">TRACK YOUR ORDER</h2>

    <form action="" method="POST">
        <div class="form-control" style="margin-bottom:15px;">
            <input type="text" id="order_id" name="order_id" required style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;">
            <label for="order_id">Order ID</label>
        </div>
        <button type="submit" id="track-order-btn" name="track-order-btn"
                style="padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer;">
            Track Order
        </button>
    </form>

    <?php if (isset($orderStatus) && !empty($orderStatus)) { ?>
        <div style="margin-top:20px; background:#f9f9f9; padding:15px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
            <h3 style="color:#007bff; border-bottom:2px solid #007bff; padding-bottom:5px;">Order Status</h3>

            <?php foreach ($orderStatus as $item) { ?>
                <div style="margin-bottom:12px; padding:10px; border:1px solid #ddd; border-radius:6px; background:#fff;">
                    <p><strong>Product:</strong> <?= htmlspecialchars($item['product_name']) ?></p>
                    <p><strong>Status:</strong> 
                        <span style="font-weight:bold; color:
                            <?= $item['order_status'] == 'completed' ? 'green' :
                               ($item['order_status'] == 'processing' ? 'orange' :
                               ($item['order_status'] == 'cancelled' ? 'red' : 'blue')) ?>;">
                            <?= ucfirst($item['order_status']) ?>
                        </span>
                    </p>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

    <!-- Privacy -->
    <div id="privacy" style="margin-top: 20px; width: 600px; border-radius:20px;"
      class="section <?php echo $activeSection === 'privacy' ? 'active' : ''; ?>">
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
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

<<<<<<< HEAD
  
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
=======
  <!-- FAQ -->
  <div id="faq" style=" position: relative;
        top: auto;
        left: auto;
        transform: none;
        width: 50%;
        margin-right:300px;
        max-width: 100%;
        height:fit-content;
        margin-top: 20px;" class="section <?php echo $activeSection === 'faq' ? 'active' : ''; ?>">
    <h3 style="text-align: center;">Frequently Asked Questions</h3>
    <div class="faq-item">
      <button class="faq-question">What is your return policy? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">You can return items within 7 days of delivery if unworn and with tags.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Do you offer cash on delivery? <i class="fa fa-chevron-down"></i></button>
      <div class="faq-answer">Yes, we provide cash on delivery all across Pakistan.</div>
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
    </div>
  </div>

  <!-- Help -->
<<<<<<< HEAD


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

=======
  <div id="help" class="section <?php echo $activeSection === 'help' ? 'active' : ''; ?>">
    <h3>AI Help Chat</h3>
    <div id="chat-box"></div>
    <div id="chat-controls">
      <input type="text" id="user-input" placeholder="Type your question...">
      <button id="send-btn" onclick="sendMessage()">Send</button>
    </div>
  </div>

>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
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
<<<<<<< HEAD
      chatbox.innerHTML += `<div class="user-msg">
                              <div class="msg-label">You</div>
                              <span>${message}</span>
                            </div>`;
=======
      chatbox.innerHTML += `<div class="user-msg"><span>${message}</span></div>`;
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
      input.value = "";

      try {
        let res = await fetch("chat_api.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ message })
        });
        const data = await res.json();
        if (data.error) {
<<<<<<< HEAD
          chatbox.innerHTML += `<div class="ai-msg">
                                  <div class="msg-label">AI</div>
                                  <span>Error: ${data.error}</span>
                                </div>`;
        } else {
          chatbox.innerHTML += `<div class="ai-msg">
                                  <div class="msg-label">AI</div>
                                  <span>${data.reply}</span>
                                </div>`;
=======
          chatbox.innerHTML += `<div class="ai-msg"><span>Error: ${data.error}</span></div>`;
        } else {
          chatbox.innerHTML += `<div class="ai-msg"><span>${data.reply}</span></div>`;
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
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