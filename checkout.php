<?php

session_start();
if (isset($_SESSION['user_id'])) {
    $login_user = $_SESSION['user_id'];
}

$conn = mysqli_connect('localhost', 'root', '', 'clothing_store');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}



if (isset($_POST['ordernow'])) {
    $contact = $_POST['contact'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $postal_code = $_POST['postal_code'];
    $phone = $_POST['phone'];
    $payment = $_POST['payment'] ?? 'COD';
    $billing = $_POST['billing'] ?? 'same';
    $user_id = $_SESSION['user_id'];

    // subtotal calculate from cart
    $subtotal = 0;
    $cart_sql = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id'");
    while ($row = mysqli_fetch_assoc($cart_sql)) {
        $subtotal += $row['total'];
    }

    $shipping = 150;
    $total = $subtotal + $shipping;

    $insert = "INSERT INTO checkout 
    (user_id, email, country, city, first_name, last_name, address, postal_code, phone, payment_method, billing_address, subtotal, shipping, total) 
    VALUES 
    ('$user_id', '$contact', '$country', '$city', '$first_name', '$last_name', '$address', '$postal_code', '$phone', '$payment', '$billing', '$subtotal', '$shipping', '$total')";

    if (mysqli_query($conn, $insert)) {
        // ✅ Get last inserted order id
        $order_id = mysqli_insert_id($conn);

        // ✅ Insert items into order_items
        $cart_sql = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id'");
        while ($row = mysqli_fetch_assoc($cart_sql)) {
            $product_id = $row['product_id'];
            $product_name = $row['name'];   // cart me save h
            $product_image = $row['image']; // cart me save h
            $qty = $row['qty'];
            $price = $row['price'];
            $line_total = $row['total']; // per item ka total (price * qty)

            $insert_item = "INSERT INTO order_items 
(order_id, user_id, product_id, product_name, product_image, qty, price, total, order_status) 
VALUES 
('$order_id', '$user_id', '$product_id', '" . $row['name'] . "', '" . $row['image'] . "', '$qty', '$price', '$total', 'pending')";

            mysqli_query($conn, $insert_item);
        }


        // ✅ Clear cart
        mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id'");

        // ✅ Redirect to refresh form
        echo "<script>
            alert('Order placed successfully!');
            window.location.href='order_status.php';
        </script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Checkout</title>
    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #4a4a4a;
            --accent-color: #007bff;
            --background-color: #f4f5f7;
            --surface-color: #ffffff;
            --border-color: #e1e4e8;
            --text-color: #24292e;
            --text-muted-color: #586069;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .checkout-container {
            display: flex;
            max-width: 1200px;
            margin: 40px auto;
            gap: 30px;
            padding: 0 20px;
        }

        /* Left Section: Form */
        .checkout-form {
            flex: 2;
        }

        .checkout-form h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-section {
            background-color: var(--surface-color);
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

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

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: #fff;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        .radio-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .radio-option:has(input:checked) {
            background-color: rgba(0, 123, 255, 0.05);
            border-color: var(--accent-color);
        }

        .radio-option input {
            margin-right: 12px;
            accent-color: var(--accent-color);
        }

        /* Right Section: Summary */
        .order-summary {
            flex: 1;
            background-color: var(--surface-color);
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .order-summary h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .product-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-info img {
            height: 55px;
            width: 55px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
            color: var(--text-muted-color);
        }

        .summary-item span:last-child {
            color: var(--text-color);
            font-weight: 500;
        }

        .order-summary hr {
            border: 0;
            border-top: 1px solid var(--border-color);
            margin: 20px 0;
        }

        .total {
            font-weight: 600;
            font-size: 18px;
            color: var(--text-color);
        }

        .discount-code {
            display: flex;
            margin-bottom: 20px;
        }

        .discount-code input {
            flex: 1;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-right: none;
            border-radius: 6px 0 0 6px;
        }

        .discount-code button {
            padding: 10px 15px;
            border: none;
            background-color: #eef0f2;
            color: var(--text-muted-color);
            cursor: pointer;
            border-radius: 0 6px 6px 0;
            font-weight: 500;
        }

        .pay-btn {
            width: 100%;
            padding: 15px;
            border: none;
            background-color: var(--primary-color);
            color: white;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
            transition: background-color 0.2s;
        }

        .pay-btn:hover {
            background-color: #333;
        }

        .footer-links {
            margin-top: 25px;
            font-size: 12px;
            text-align: center;
            color: var(--text-muted-color);
        }

        .footer-links a {
            margin: 0 8px;
            color: var(--text-muted-color);
            text-decoration: none;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .checkout-container {
                flex-direction: column-reverse;
            }
        }

        /* Navbar Styles (from other pages) */
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
            text-transform: capitalize;
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
                data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent"
                aria-expanded="false" aria-label="Toggle navigation"
                style="background-color: transparent; border: none;">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <form action="" method="POST">
        <div class="checkout-container">

            <!-- Left Section -->
            <div class="checkout-form">
                <div class="form-section">
                    <h3>Contact</h3>
                    <div class="form-group">
                        <label for="contact">Email or mobile phone number</label>
                        <input id="contact" name="contact" type="text" placeholder="Enter your email or phone" required>
                    </div>
                </div>

                <div class="form-section">
                <h3>Shipping Address</h3>
                <div class="form-group">
                    <label>Country/Region</label>
                    <select name="country">
                        <option value="Pakistan">Pakistan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <select name="city">
                        <option value="karachi">Karachi</option>
                        <option value="lahore">Lahore</option>
                        <option value="islamabad">Islamabad</option>
                        <option value="peshawar">Peshawar</option>
                        <option value="quetta">Quetta</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="hidden" value="<?php echo $login_user; ?>" name="user_id" id="">
                    <label for="first_name">First Name</label>
                    <input id="first_name" name="first_name" type="text" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input id="last_name" name="last_name" type="text" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input id="address" name="address" type="text" placeholder="Street and house number" required>
                </div>
                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input id="postal_code" name="postal_code" type="text" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="text" placeholder="For order updates" required>
                </div>
                </div>

                <div class="form-section">
                    <h3>Payment</h3>
                    <div class="radio-option" onclick="document.getElementById('payfast').checked = true;">
                        <input type="radio" id="payfast" name="payment" value="PayFast" checked>
                        <label for="payfast">PayFast (Debit/Credit/Wallet/Bank)</label>
                    </div>
                    <div class="radio-option" onclick="document.getElementById('cod').checked = true;">
                        <input type="radio" id="cod" name="payment" value="COD">
                        <label for="cod">Cash on Delivery (COD)</label>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Billing Address</h3>
                    <div class="radio-option" onclick="document.getElementById('same_billing').checked = true;">
                        <input type="radio" id="same_billing" name="billing" value="same" checked>
                        <label for="same_billing">Same as shipping address</label>
                    </div>
                    <div class="radio-option" onclick="document.getElementById('diff_billing').checked = true;">
                        <input type="radio" id="diff_billing" name="billing" value="different">
                        <label for="diff_billing">Use a different billing address</label>
                    </div>
                </div>

                <button type="submit" name="ordernow" class="pay-btn">Order Now</button>

                <div class="footer-links">
                    <a href="#">Refund policy</a>
                    <a href="#">Shipping policy</a>
                    <a href="#">Privacy policy</a>
                    <a href="#">Terms of service</a>
                </div>
            </div>

            <!-- Right Section -->
            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "clothing_store");
                if (!$conn) {
                    die("Database connection failed: " . mysqli_connect_error());
                }
                $user_id = $_SESSION['user_id'];
                $sql = mysqli_query($conn, "SELECT * FROM cart where user_id = '$user_id'");

                $subtotal = 0;
                while ($row = mysqli_fetch_assoc($sql)) {
                    $subtotal += $row['total'];
                    ?>
                    <div class="product-row">
                        <div class="product-info">
                            <img src="<?php echo $row['image']; ?>" alt="">
                            <span><?php echo $row['name']; ?></span>
                        </div>
                        <span><?php echo "PKR " . number_format($row['price'], 2); ?></span>
                    </div>
                <?php } ?>

                <div class="summary-item">
                    <span>Subtotal</span>
                    <span><?php echo "PKR " . number_format($subtotal, 2); ?></span>
                </div>

                <?php
                // ✅ Sirf ek dafa shipping
                $shipping = 150;
                $grandTotal = $subtotal + $shipping;
                ?>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span><?php echo "PKR " . number_format($shipping, 2); ?></span>
                </div>

                <hr>

                <div class="discount-code">
                    <input type="text" placeholder="Discount code">
                    <button>Apply</button>
                </div>

                <div class="summary-item total" style="margin-top: 20px;">
                    <span>Total</span>
                    <span><?php echo "PKR " . number_format($grandTotal, 2); ?></span>
                </div>
            </div>

        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>


</html>