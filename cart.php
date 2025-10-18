<?php
session_start();

// 1. Redirect user to login if they are not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_config.php';
// 2. Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// ✅ Update qty with plus/minus
if (isset($_POST['update_qty'])) {
  $id = intval($_POST['product_id']);
  $action = $_POST['update_qty'];

  // 3. Use prepared statements and scope to user_id
  $stmt = mysqli_prepare($conn, "SELECT qty, price FROM cart WHERE product_id = ? AND user_id = ?");
  mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  
  if ($row = mysqli_fetch_assoc($res)) {
    $qty = $row['qty'];
    $price = $row['price'];

    if ($action == "plus") {
      $qty++;
    } elseif ($action == "minus" && $qty > 1) {
      $qty--;
    }

    // 4. Also update the total price for the item
    $new_total = $qty * $price;
    $update_stmt = mysqli_prepare($conn, "UPDATE cart SET qty = ?, total = ? WHERE product_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($update_stmt, "idii", $qty, $new_total, $id, $user_id);
    mysqli_stmt_execute($update_stmt);
  }
}

// Remove
if (isset($_POST['remove_id'])) {
  $id = intval($_POST['remove_id']);
  $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE product_id = ? AND user_id = ?");
  mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
  mysqli_stmt_execute($stmt);
}

// Fetch cart for the specific user
$cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
// ✅ Fetch categories
$category = mysqli_query($conn, "SELECT * FROM nav_categories");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Shopping Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
  body {
  background: #fff;
  color: #111;
  font-family: Arial, sans-serif;
}

h2 {
  font-weight: 600;
  text-transform: uppercase;
  margin-bottom: 30px;
}

.cart-table {
  border: 1px solid #ddd;
  width: 100%;
  margin: auto;
  border-collapse: collapse;
}

.cart-table td,
.cart-table th {
  padding: 20px;
  vertical-align: middle;
  text-align: center;
}

.cart-item-img {
  width: 120px;
  max-width: 100%;
}

.qty-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}

.qty-controls button {
  border: 1px solid #aaa;
  background: none;
  padding: 4px 10px;
  cursor: pointer;
}

.qty-controls input {
  width: 60px;
  text-align: center;
  margin: 0 5px;
}

textarea {
  width: 100%;
  min-height: 100px;
  border: 1px solid #ccc;
  padding: 10px;
  margin-top: 20px;
}

.subtotal-box {
  margin-top: 20px;
  text-align: right;
  font-size: 18px;
  font-weight: 600;
}

.cart-actions {
  margin-top: 30px;
  text-align: center;
}

.cart-actions .btn {
  width: 200px;
  margin: 10px;
  padding: 12px;
}

.foot {
  background-color: #f4f4f4;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  padding: 40px 60px;
  color: #333;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.footercontainer {
  display: flex;
  flex-direction: column;
  flex: 1 1 200px;
  margin: 10px 20px;
}

.footercontainer h3 {
  color: #111;
  font-size: 16px;
  margin-bottom: 15px;
  text-align: left;
}

.footercontainer a {
  color: #333;
  text-decoration: none;
  margin-bottom: 8px;
  font-size: 13px;
  transition: 0.3s;
  text-align: left;
}

.footercontainer a:hover {
  text-decoration: underline;
  color: #0077cc;
}

.footercontainer p {
  font-size: 12px;
  color: #555;
  line-height: 1.6;
}

.footercontainer input {
  width: 90%;
  padding: 8px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 13px;
}

.footer {
  background-color: #f4f4f4;
  color: #111;
  text-align: center;
  padding: 15px 10px;
  font-size: 11px;
  font-weight: 200;
}

/* Responsive */
@media (max-width: 992px) {
  .cart-table td,
  .cart-table th {
    padding: 12px;
  }

  .cart-actions .btn {
    width: 150px;
    font-size: 14px;
    padding: 10px;
  }

  .subtotal-box {
    font-size: 16px;
  }
}

@media (max-width: 768px) {
  /* Make table scrollable */
  .cart-table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }

  .cart-table td,
  .cart-table th {
    font-size: 14px;
    padding: 10px;
  }

  .cart-item-img {
    width: 80px;
  }

  .qty-controls input {
    width: 40px;
  }

  .subtotal-box {
    text-align: center;
  }
}

@media (max-width: 576px) {
  h2 {
    font-size: 22px;
  }

  .cart-actions .btn {
    width: 100%;
    margin: 8px 0;
  }

  textarea {
    min-height: 80px;
    font-size: 14px;
  }

  .foot {
    flex-direction: column;
    align-items: center;
    padding: 20px;
    text-align: center;
  }

  .footercontainer {
    align-items: center;
    text-align: center;
    margin: 15px 0;
  }

  .footercontainer input {
    width: 100%;
  }
}

.send-btn {
  margin-top: 10px;
  padding: 10px 20px;
  background-color: #0077cc;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.send-btn:hover {
  background-color: #005fa3;
}

.links {
  font-size: 20px;
  color: white;
  text-transform: capitalize;
  text-decoration: none;
  display: inline-block;
  padding: 10px;
  position: relative;
}

.nav-item {
  position: relative;
  list-style: none;
}

.type {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  background-color: black;
  padding: 10px 0;
  z-index: 999;
  min-width: 200px;
}

.nav-item:hover .type {
  display: block;
}

.type li {
  list-style: none;
}

.type li a {
  display: block;
  color: white;
  padding: 10px 20px;
  text-decoration: none;
  font-weight: 300;
  font-size: 17px;
}

.type li a:hover {
  background-color: grey;
  color: white;
}

.privacy-policy {
  background-color: #f4f4f4;
  padding: 60px 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #2b2b2b;
}

.policy-wrapper {
  max-width: 900px;
  margin: auto;
  background-color: #fff;
  padding: 40px 30px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.policy-title {
  font-size: 36px;
  font-weight: bold;
  text-align: center;
  color: #111;
  margin-bottom: 20px;
}

.policy-intro {
  font-size: 18px;
  line-height: 1.8;
  margin-bottom: 30px;
  text-align: center;
  color: #555;
}

.policy-section {
  margin-bottom: 25px;
}

.policy-section h2 {
  font-size: 22px;
  margin-bottom: 10px;
  color: #222;
}

.policy-section p {
  font-size: 16px;
  line-height: 1.6;
  color: #444;
}

.policy-footer {
  text-align: center;
  margin-top: 40px;
  font-size: 16px;
}

.policy-footer a {
  color: #0077cc;
  text-decoration: none;
}

.policy-footer a:hover {
  text-decoration: underline;
}

.policy-container p {
  line-height: 1.7;
  margin: 10px 0;
}
</style>
</head>

<body >
  <!-- NAVBAR -->
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
        aria-label="Toggle navigation" style="background-color: transparent; border: none;">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>
 <div class="container py-5">
  <h2 class="text-center">SHOPPING CART</h2>

  <?php
  $total = 0;
  if (mysqli_num_rows($cart) > 0) {
    echo "<table class='cart-table'>";
    echo "<tr><th></th><th>Product</th><th>Quantity</th><th>Total</th><th></th></tr>";
    while ($row = mysqli_fetch_assoc($cart)) {
      $subtotal = $row['total']; // Use the 'total' column from the database
      $total += $subtotal;
      echo "
          <tr>
            <td><img src='{$row['image']}' class='cart-item-img'></td>
            <td>
              <strong>{$row['name']}</strong><br>
              <small>PKR {$row['price']}</small>
            </td>
            <td>
             <form method='post' class='qty-controls'>
                <input type='hidden' name='product_id' value='{$row['product_id']}'>
                <button type='submit' name='update_qty' value='minus'>-</button>
                <input type='text' value='{$row['qty']}' readonly>
                <button type='submit' name='update_qty' value='plus'>+</button>
              </form>
            </td>
            <td>PKR {$subtotal}</td>
            <td>
              <form method='post'>
                <input type='hidden' name='remove_id' value='{$row['product_id']}'>
                <button type='submit' class='btn btn-sm btn-outline-danger'>&#128465;</button>
              </form>
            </td>
          </tr>";
    }
    echo "</table>";

    // Notes + subtotal + buttons
    echo "
      <div>
        <label for='notes'><strong>Order Notes</strong></label>
        <textarea id='notes' name='notes' placeholder='Please leave special instructions above'></textarea>
      </div>
      <div class='subtotal-box'>
        Subtotal: PKR $total
        <br><small>Taxes, discounts and shipping calculated at checkout</small>
      </div>
      <div class='cart-actions'>
        <a href='checkout.php' class='btn btn-dark'>Checkout</a>
        <a href='index.php' class='btn btn-outline-dark'>Continue Shopping</a>
      </div>
      ";
  } else {
    echo "<p class='text-center'>Your cart is empty.</p>";
    echo "<a href='index.php' style='margin: 10px 470px 0px ; margin-bottom: 20px;' class='btn btn-dark'>Continue Shopping</a>";

  }
  ?>
  </div>

  <!-- FOOTER -->
  <div class="foot" style="background-color: #f8f9fa; padding: 20px 0;">
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
      <a href="login.php">LOGIN</a>
      <a href="signup.php">CREATE ACCOUNT</a>
      <a href="signup.php">ACCOUNT INFO</a>
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
    <p style="margin-top: 13px; font-size: 26px;">&#169; trendywear copyright 2024</p>
  </div>

</body>

</html>