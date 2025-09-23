<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "clothing_store");

if (!isset($_SESSION['user_id'])) {
  die("Please login first.");
}

$userId = $_SESSION['user_id'];

// Get old data
$sql = "SELECT * FROM users WHERE id = $userId";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

  header("Location: settings.php"); // refresh to show updated info
  exit;
}
?>





<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style></style>
  <title>Settings Page</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      background: #f4f4f4;
      overflow: hidden
    }

    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      height: 100%;
      width: 220px;
      /* Sidebar width fix */
      background: #111;
      color: #fff;
      padding-top: 20px;
      box-shadow: 2px 0 8px rgba(0, 0, 0, 0.3);
      z-index: 1000;
      /* Taake sidebar upar rahe */
    }

    .content {
      margin-left: 220px;
      /* Same as sidebar width */
      padding: 20px;
      background: #f9f9f9;
      min-height: 100vh;
      /* Full height */
    }


    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar a {
      display: block;
      color: #fff;
      padding: 12px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: #575757;
    }

    .content {
      flex: 1;
      padding: 20px;
    }

    .section {
      display: none;
    }

    .section.active {
      display: block;
    }



    .form-control {
      position: relative;
      margin-top: 20px;
      width: 300px;
    }

    .form-control input {
      width: 100%;
      padding: 12px 10px;
      font-size: 16px;
      border: 2px solid #ccc;
      border-radius: 6px;
      outline: none;
      background: transparent;
    }

    .form-control label {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #80868b;
      font-size: 16px;
      pointer-events: none;
      transition: all 0.3s ease;
      background: white;
      padding: 0 4px;
    }

    /* Focused or Filled Input */
    .form-control input:focus {
      border: 2px solid black;
    }

    .form-control input:focus+label,
    .form-control input:valid+label {
      top: -8px;
      left: 8px;
      font-size: 13px;
      color: blue;
    }


    /* Chat container */
    #help {
      background: #111;
      color: #fff;
      padding: 20px;
      border-radius: 12px;
      max-width: 400px;
      /* box chhota aur centered */
      margin: 0 auto;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
      font-family: Arial, sans-serif;
    }

    /* Chat box */
    #chat-box {
      background: #000;
      padding: 12px;
      border-radius: 10px;
      height: 300px;
      /* chhoti height */
      overflow-y: auto;
      margin-bottom: 12px;
      font-size: 14px;
    }

    /* Chat bubbles */
    .user-msg,
    .ai-msg {
      margin: 6px 0;
      padding: 10px 14px;
      border-radius: 18px;
      max-width: 75%;
      line-height: 1.4;
    }

    .user-msg {
      background: #444;
      color: #fff;
      margin-left: auto;
      text-align: right;
    }

    .ai-msg {
      background: #1a1a1a;
      color: #fff;
      margin-right: auto;
      text-align: left;
    }

    /* Controls */
    #chat-controls {
      display: flex;
      gap: 8px;
    }

    #user-input {
      flex: 1;
      padding: 10px 14px;
      border: none;
      border-radius: 20px;
      outline: none;
      background: #222;
      color: #fff;
      font-size: 14px;
    }

    #send-btn {
      padding: 10px 16px;
      border: none;
      border-radius: 20px;
      background: #444;
      color: #fff;
      cursor: pointer;
      transition: 0.3s;
    }

    #send-btn:hover {
      background: #666;
    }

    /* Profile Settings Card */
    #profile {
      max-width: 450px;
      margin: 0 auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    /* Headings */
    #profile h3 {
      text-align: center;
      margin-bottom: 20px;
      color: #222;
    }

    /* Form Inputs */
    #profile input[type="text"],
    #profile input[type="email"] {
      width: 80%;
      padding: 10px 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: 0.3s;
    }

    #profile input:focus {
      border-color: #000;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    }

    /* Button */
    #profile button {
      background: #000;
      color: #fff;
      padding: 8px 20px;
      border: none;
      border-radius: 50px;
      /* oval shape */
      cursor: pointer;
      font-size: 14px;
      margin-top: 15px;
      display: block;
      margin-left: auto;
      margin-right: auto;
      transition: 0.3s;
    }

    #profile button:hover {
      background: #333;
    }

    /* Profile Pic */
    #profile img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #000;
      transition: 0.3s;
    }

    #profile img:hover {
      transform: scale(1.05);
    }

    #logout {
      margin-top: 217px;
      text-align: center;
      background-color: #be2e2eff;
    }

    #logout:hover {
      background-color: #c74747;
    }



    .policy-box h4 {
      margin-top: 15px;
      color: #333;
      font-size: 18px;
    }

    .policy-box p {
      font-size: 15px;
      color: #555;
      line-height: 1.6;
      margin-bottom: 10px;
    }

    .section {
      display: none;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 5px 10px rgba(17, 17, 17, 0.1);
    }

    .section.active {
      display: block;
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
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h2>Settings</h2>
    <a href="#" onclick="showSection('profile', event)"><i class="fa fa-user"></i> Profile</a>
    <a href="#" onclick="showSection('password', event)"><i class="fa fa-lock"></i> Password</a>
    <a href="#" onclick="showSection('orders', event)"><i class="fa fa-box"></i> Orders</a>
    <a href="#" onclick="showSection('shipping', event)"><i class="fa fa-truck"></i> Shipping</a>
    <a href="#" onclick="showSection('privacy', event)"><i class="fa fa-shield-alt"></i> Privacy</a>
    <a href="#" onclick="showSection('faq', event)"><i class="fa fa-question-circle"></i> FAQ</a>
    <a href="#" onclick="showSection('help', event)"><i class="fa fa-life-ring"></i> Help</a>
    <a id="logout" href="http://localhost/clothing%20store/logout.php">logout</a>
  </div>

  <div class="content">
    <!-- Profile -->
    <div id="profile" class="section active">
      <h3>Profile Settings</h3>

      <form action="" method="POST" enctype="multipart/form-data">
        <div style="text-align:center; margin-bottom:20px;">
          <!-- Profile image circle -->
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

        <button type="submit">Save Changes</button>
      </form>
    </div>

    <script>
      // Profile image preview
      function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
          document.getElementById('profilePreview').src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
      }
    </script>




    <!-- Password -->
    <div id="password" class="section">
      <h3>Password Settings</h3>
      <p>Change your password here.</p>
    </div>

    <!-- Orders -->
    <div id="orders" class="section">
      <h3>Your Orders</h3>
      <p>View and track your orders here.</p>
    </div>

    <!-- Shipping -->
    <div id="shipping"  style=" overflow: hidden; max-width: 400px;
    margin: 0 auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    text-align: center;
    transform: translate(2%, 63%);

    " class="section">
      <h2>TRACK YOUR ORDER</h2>
      <div class="form-control">
        <input style="margin-left: 39px;" type="text" id="order_id" required>
        <label style="margin-left: 39px;" for="order_id">Order ID</label>
      </div>

      <button id="track-order-btn">Track Order</button>
    </div>


    <!-- Privacy Policy -->
    <div id="privacy" class="section">
      <h3>Privacy Policy</h3>
      <div class="policy-box">
        <h4>1. Information We Collect</h4>
        <p>
          We collect your personal details (name, email, and profile picture) to personalize your account experience.
        </p>

        <h4>2. How We Use Your Information</h4>
        <p>
          Your information is only used for login, account security, and to improve your shopping experience on our
          clothing store.
        </p>

        <h4>3. Data Protection</h4>
        <p>
          We use secure methods to store your data and ensure your privacy is protected at all times.
        </p>

        <h4>4. Sharing of Information</h4>
        <p>
          We never sell or share your personal information with third parties without your consent.
        </p>

        <h4>5. Contact Us</h4>
        <p>
          If you have any questions regarding our Privacy Policy, please contact us via email.
          <a href="mailto:support@example.com">trendywear@gmail.com</a>.
        </p>
      </div>
    </div>



    <!-- FAQ -->
    <div id="faq" class="section">
      <h3>FAQ</h3>
      <p>Find answers to common questions here.</p>
    </div>

    <!-- Help (Chatbox untouched) -->
    <div id="help" class="section">
      <h3>AI Help Chat</h3>
      <div id="chat-box"></div>
      <div id="chat-controls">
        <input type="text" id="user-input" placeholder="Type your question...">
        <button id="send-btn" onclick="sendMessage()">Send</button>
      </div>
    </div>

    <!-- Scripts -->
    <script>
      function showSection(sectionId, event) {
        // hide all
        document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
        document.querySelectorAll('.sidebar a').forEach(link => link.classList.remove('active'));

        // show clicked
        document.getElementById(sectionId).classList.add('active');
        event.target.classList.add('active');
      }

      // Enter key support
      document.getElementById("user-input").addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
          sendMessage();
        }
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
        } catch (err) {
          chatbox.innerHTML += `<div class="ai-msg"><span>Error: ${err.message}</span></div>`;
        }
      }

    </script>
</body>

</html>