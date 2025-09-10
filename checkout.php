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
            $qty = $row['qty'];
            $price = $row['price'];
            $total = $row['total'];

            $insert_item = "INSERT INTO order_items (order_id, user_id, product_id, qty, price, total, order_status) 
                            VALUES ('$order_id', '$user_id', '$product_id', '$qty', '$price', '$total', 'pending')";
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
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }

        .checkout-container {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            gap: 20px;
        }

        /* Left Section */
        .checkout-form {
            flex: 2;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .checkout-form h3 {
            margin-bottom: 10px;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            font-size: 14px;
        }

        .shipping-method,
        .payment-method,
        .billing-address {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fafafa;
        }

        .radio-option {
            margin-bottom: 10px;
        }

        .radio-option input {
            margin-right: 8px;
        }

        /* Right Section */
        .order-summary {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .order-summary h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .product-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-info img {
            height: 50px;
            width: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .total {
            font-weight: bold;
            font-size: 16px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .discount-code {
            display: flex;
            margin: 10px 0;
        }

        .discount-code input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
        }

        .discount-code button {
            padding: 8px 12px;
            border: none;
            background: #0070f3;
            color: #fff;
            cursor: pointer;
            border-radius: 0 4px 4px 0;
        }

        .pay-btn {
            width: 100%;
            padding: 12px;
            border: none;
            background: #0070f3;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 15px;
        }

        .pay-btn:hover {
            background: #005bb5;
        }


        .pay-btn:hover {}

        .footer-links {
            margin-top: 15px;
            font-size: 13px;
            text-align: center;
        }

        .footer-links a {
            margin: 0 8px;
            color: #0070f3;
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .checkout-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <form action="" method="POST">
        <div class="checkout-container">

            <!-- Left Section -->
            <div class="checkout-form">
                <h3>Contact</h3>
                <div class="form-group">
                    <label>Email or mobile phone number</label>
                    <input name="contact" type="text" placeholder="Enter your email or phone">
                </div>

                <h3>Delivery</h3>
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
                    <label>First Name</label>
                    <input name="first_name" type="text">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input name="last_name" type="text">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input name="address" type="text">
                </div>
                <div class="form-group">
                    <label>Postal Code</label>
                    <input name="postal_code" type="text">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input name="phone" type="text">
                </div>

                <div class="shipping-method">
                    <h3>Shipping Method</h3>
                    <p>Home Delivery — Rs.150</p>
                </div>

                <div name="card_method" class="payment-method">
                    <h3>Payment</h3>
                    <div class="radio-option">
                        <input type="radio" name="payment" checked> PayFast (Debit/Credit/Wallet/Bank)
                    </div>
                    <div name="cod" class="radio-option">
                        <input type="radio" name="payment"> Cash on Delivery (COD)
                    </div>
                </div>

                <div name="billing" class="billing-address">
                    <h3>Billing Address</h3>
                    <div name="same_as_shipping" class="radio-option">
                        <input type="radio" name="billing" checked> Same as shipping address
                    </div>
                    <div name="different_billing" class="radio-option">
                        <input type="radio" name="billing"> Use a different billing address
                    </div>
                </div>

                <button type="submit" name="ordernow" class="pay-btn">Order Now</button>

                <div class="footer-links">
                    <a href="#">Refund policy</a> |
                    <a href="#">Shipping</a> |
                    <a href="#">Privacy policy</a> |
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

                $sql = mysqli_query($conn, "SELECT * FROM cart");

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

                <div class="discount-code">
                    <input type="text" placeholder="Discount code">
                    <button>Apply</button>
                </div>

                <div class="summary-item total">
                    <span>Total</span>
                    <span><?php echo "PKR " . number_format($grandTotal, 2); ?></span>
                </div>
            </div>

        </div>
    </form>
</body>

</html>