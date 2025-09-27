<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "clothing_store");

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Settings Page</title>
  <link rel="stylesheet" href="settings.css">
  <style>
    body {
      overflow: hidden;
    }

    /* Content ka layout (desktop ke liye) */
    .content {
      margin-left: 220px;
      padding: 20px;
      display: block;
    }

    /* Sare sections ek hi jagah aligned rahe */
    .section {
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
    }

    #logout:hover {
      background-color: #c74747;
    }

    #faq {
      max-height: 400px;
      /* Jitna height chahiye utna set karo */
      overflow-y: auto;
      /* Sirf y-axis scroll */
      padding-right: 10px;
      /* scrollbar ke liye space */
    }

    #shipping {
      overflow: auto;

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
    <div id="profile" style="width: 374px; height: auto; border-radius: 44px; margin-top: 61px;"
      class="section <?php echo $activeSection === 'profile' ? 'active' : ''; ?>">
      <h3>Profile Settings</h3>
      <form action="" method="POST" enctype="multipart/form-data">
        <div style="text-align:center; margin-bottom:20px;">
          <label for="profile-pic" style="cursor:pointer;">
            <img id="profilePreview"
              src="<?php echo !empty($user['profile_pic']) ? $user['profile_pic'] : 'uploads/default-user.png'; ?>"
              alt="User" style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:2px solid #ccc;">
          </label>
          <input type="file" id="profile-pic" name="profile_pic" accept="image/*" style="display:none;"
            onchange="previewImage(event)">
        </div>
        <div style="margin-left: 20px;">
          <label>First Name:</label><br>
          <input type="text" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" required>
        </div>
        <div style="margin-left: 20px;">
          <label>Last Name:</label><br>
          <input type="text" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" required>
        </div>
        <div style="margin-left: 20px;">
          <label>Email:</label><br>
          <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <button type="submit" name="save_changes">Save Changes</button>
      </form>
    </div>

    <!-- Password -->
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
      <a href="http://localhost/clothing%20store/myaccount/forget_password.php">Forgot Password</a>

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
    <div id="shipping" style="width:364px; margin-top: 15px; height: auto; padding:20px;"    
      class="section <?php echo $activeSection === 'shipping' ? 'active' : ''; ?>">
      <h2 style="text-align: center; margin-top:15px;">TRACK YOUR ORDER</h2>

      <form action="" method="POST">
        <div class="form-control" style="margin-bottom:15px;">
          <input type="text" id="order_id" name="order_id" required
            style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;">
          <label for="order_id">Order ID</label>
          </div>
        <button type="submit" id="track-order-btn" name="track-order-btn"                
          style="padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer;">
          Track Order
          </button>
        </form>

      <?php if (isset($orderStatus) && !empty($orderStatus)) { ?>
        <div
          style="margin-top:20px; background:#f9f9f9; padding:15px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); max-height: 250px; overflow-y: auto;">
          <h3 style="color:#007bff; border-bottom:2px solid #007bff; padding-bottom:5px;">Order Status</h3>

          <?php foreach ($orderStatus as $item) { ?>
            <div
              style="margin-bottom:12px; padding:10px; border:1px solid #ddd; border-radius:6px; background:#fff;">
          
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
    <div id="privacy" style="margin-top: 20px; width: 600px; border-radius:20px;"
      class="section <?php echo $activeSection === 'privacy' ? 'active' : ''; ?>">
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
    </div>
  </div>

  <!-- Help -->
  <div id="help" class="section <?php echo $activeSection === 'help' ? 'active' : ''; ?>">
    <h3>AI Help Chat</h3>
    <div id="chat-box"></div>
    <div id="chat-controls">
      <input type="text" id="user-input" placeholder="Type your question...">
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
      chatbox.innerHTML += `<div class="user-msg"><span>${message}</span></div>`;
      input.value = "";

      try {
        let res = await fetch("chat_api.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ message })
        });
        const data = await res.json();
        if (data.error) {
          chatbox.innerHTML += `<div class="ai-msg"><span>Error: ${data.error}</span></div>`;
        } else {
          chatbox.innerHTML += `<div class="ai-msg"><span>${data.reply}</span></div>`;
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