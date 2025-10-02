<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

// Pehle user ke sare orders fetch karo
$checkouts = mysqli_query($conn, "SELECT * FROM checkout WHERE user_id='{$_SESSION['user_id']}' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Status</title>
  <link rel="shortcut icon" href="" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    a {
      text-decoration: none;
      color: white;
      font-size: 20px;
    }

    li {
      list-style: none;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f4f6f9;
      color: #333;
    }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .thank-you {
      text-align: center;
      margin-bottom: 25px;
    }

    .thank-you h1 {
      font-size: 26px;
      font-weight: 700;
      color: #28a745;
      margin-bottom: 8px;
    }

    .thank-you p {
      font-size: 15px;
      color: #555;
    }

    .order-status {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
      padding: 25px 30px;
      margin-bottom: 25px;
      transition: 0.3s ease;
    }

    .order-status:hover {
      transform: translateY(-3px);
    }

    .order-status h2 {
      font-size: 20px;
      margin-bottom: 15px;
      font-weight: 600;
      color: #222;
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 8px;
    }

    .order-status .order-id {
      font-size: 14px;
      color: #777;
      margin-bottom: 15px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 14px 20px;
      margin-bottom: 18px;
    }

    .info-grid p {
      margin: 0;
      background: #f8f9fa;
      padding: 10px 14px;
      border-radius: 6px;
      font-size: 14px;
      border: 1px solid #eee;
      color: #444;
    }

    .address-box {
      background: #fdfdfd;
      padding: 14px 16px;
      border: 1px solid #eaeaea;
      border-radius: 8px;
      font-size: 14px;
      line-height: 1.5;
      margin-top: 10px;
    }

    .product-box {
      margin-top: 15px;
      padding: 15px;
      border: 1px solid #eee;
      border-radius: 10px;
      background: #fafafa;
    }

    .product-box p {
      margin: 6px 0;
      font-size: 14px;
    }

    .status-badge {
      display: inline-block;
      font-size: 13px;
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 20px;
      margin-top: 10px;
    }

    .status-pending {
      background: #17a2b8;
      color: #fff;
    }

    .status-processing {
      background: #ffc107;
      color: #222;
    }

    .status-completed {
      background: #28a745;
      color: #fff;
    }

    .status-cancelled {
      background: #dc3545;
      color: #fff;
    }

    @media (max-width: 600px) {
      .order-status {
        padding: 20px;
      }

      .thank-you h1 {
        font-size: 22px;
      }
    }

    .status-pending {
      background: #6f42c1;
      /* purple */
      color: #fff;
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
            top: 11%;
            left: 111px;
            background-color: #333;
            border-radius: 24px;
            /* or white if your theme is white */
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
  </style>
</head>

<body>
  <div class="collapse" id="navbarToggleExternalContent">
    <div class="bg-dark p-4">
      <span class="text-muted"></span>
      <ul>
        <li> <a class="links" href="index.php">Home</a></li>
        <li class="nav-item">
          <a class="links" href="index.php">Categories</a>
          <ul class="type">


            <?php
            $category = mysqli_query($conn, "SELECT * FROM nav_categories ");
            while ($row = mysqli_fetch_assoc($category)) {
              echo "<li><a href='#cart{$row['id']}'>{$row['name']}</a></li>";
            }
            ?>
          </ul>
        </li>
        <li> <a class="links" href="index.php">Policy</a> </li>
        <li> <a class="links" href="index.php">Contact us</a> </li>
        <li> <a class="links" href="http://localhost/store/myaccount/settings.php">Settings</a> </li>
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
  <div class="container">

    <div class="thank-you">
      <h1>✅ Thank You for Your Order!</h1>
      <p>Your order has been placed successfully. Below are your order details:</p>
    </div>

    <?php while ($checkout = mysqli_fetch_assoc($checkouts)) { ?>
      <div class="order-status">
        <h2>Customer Information</h2>
        <p class="order-id">Order #<?= $checkout['id'] ?></p>
        <p style="color:black; font-size: 17px;" class="order-id">Placed On : <?= $checkout['created_at'] ?></p>

        <div class="info-grid">
          <p><strong>First Name:</strong> <?= $checkout['first_name'] ?></p>
          <p><strong>Last Name:</strong> <?= $checkout['last_name'] ?></p>
          <p><strong>Email:</strong> <?= $checkout['email'] ?></p>
          <p><strong>Phone:</strong> <?= $checkout['phone'] ?></p>
          <p><strong>Postal Code:</strong> <?= $checkout['postal_code'] ?></p>
          <p><strong>City:</strong> <?= $checkout['city'] ?></p>
          <p><strong>Country:</strong> <?= $checkout['country'] ?></p>
        </div>

        <div class="address-box">
          <span><strong>Address:</strong></span><br>
          <?= $checkout['address'] ?>
        </div>

        <h2>Order Details</h2>
        <?php
        $order_items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id='{$checkout['id']}'");
        $grand_total = 0; // total store karne ke liye
        while ($rows = mysqli_fetch_assoc($order_items)) {
          $status = strtolower($rows['order_status']);
          $statusClass = "status-pending";
          if ($status === "processing")
            $statusClass = "status-processing";
          if ($status === "completed")
            $statusClass = "status-completed";
          if ($status === "cancelled")
            $statusClass = "status-cancelled";

          // grand total add karo
          $grand_total = $rows['total'];
          ?>
          <div class="product-box">
            <p><strong>Product:</strong> <?= $rows['product_name'] ?></p>
            <p><strong>Image:</strong>
              <img src="<?= $rows['product_image'] ?>" alt="<?= $rows['product_name'] ?>" width="60"
                style="border-radius:8px; border:1px solid #ddd; padding:3px;">
            </p>
            <p><strong>Quantity:</strong> <?= $rows['qty'] ?></p>
            <p><strong>Price:</strong> PKR <?= $rows['price'] ?></p>
            <span class="status-badge <?= $statusClass ?>"><?= ucfirst($status) ?></span>
          </div>
        <?php } ?> <!-- order_items loop close -->

        <!-- grand total neeche ek hi baar show hoga -->
        <h4 style="margin-top:20px; color:#000;">Total Price: PKR <?= $grand_total ?></h4>
      </div> <!-- order-status close -->
    <?php } ?> <!-- checkouts loop close -->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"></script>
</body>

</html>