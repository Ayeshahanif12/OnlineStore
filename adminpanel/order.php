<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Pehle user ke sare orders fetch karo
$checkouts = mysqli_query($conn, "SELECT * FROM checkout");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <link rel="shortcut icon" href="" type="image/x-icon">
    <style>
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

        form {
            margin-top: 12px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        form select {
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            color: #333;
            background: #fff;
            cursor: pointer;
            transition: 0.2s ease;
        }

        form select:hover {
            border-color: #888;
        }

        form button {
            padding: 8px 18px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 3px 8px rgba(0, 123, 255, 0.2);
        }

        form button:hover {
            background: #0056b3;
            box-shadow: 0 4px 10px rgba(0, 86, 179, 0.3);
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="incoming-orders"></div>

        <?php while ($checkout = mysqli_fetch_assoc($checkouts)) { ?>
            <div class="order-status">
                <h2>Customer Information</h2>
                <p class="order-id">Order #<?= $checkout['id'] ?></p>

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

                $order_items = mysqli_query($conn, "SELECT * FROM order_items");
                $rows = mysqli_fetch_assoc($order_items);

                ?>
                <div class="product-box">
                    <p><strong>Product:</strong> <?= $rows['product_name'] ?></p>
                    <p><strong>Image:</strong>
                        <img src="<?= $rows['product_image'] ?>" alt="<?= $rows['product_name'] ?>" width="60"
                            style="border-radius:8px; border:1px solid #ddd; padding:3px;">
                    </p>
                    <p><strong>Quantity:</strong> <?= $rows['qty'] ?></p>
                    <p><strong>Total Price:</strong> PKR <?= $rows['total'] ?></p>
                    <!-- FORM for updating order status -->
                    <form method="POST">
                        <input type="hidden" name="order_item_id" value="<?= $rows['id'] ?>">
                        <select name="order_status">
                            <option value="accepted" <?= $rows['order_status'] == 'accepted' ? 'selected' : '' ?>>Accepted
                            </option>
                            <option value="processing" <?= $rows['order_status'] == 'processing' ? 'selected' : '' ?>>
                                Processing</option>
                            <option value="completed" <?= $rows['order_status'] == 'completed' ? 'selected' : '' ?>>Completed
                            </option>
                            <option value="cancelled" <?= $rows['order_status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled
                            </option>
                        </select>
                        <button type="submit" name="update_order">Update</button>
                    </form>
                </div>
            <?php } ?>
        </div>

        <?php
        // Handle order update
        if (isset($_POST['update_order'])) {
            $order_id = $_POST['order_item_id'];
            $status = $_POST['order_status'];

            $update_status = mysqli_query($conn, "UPDATE order_items SET order_status='$status' WHERE id='$order_id'");

            if ($update_status) {
                echo "<script>alert('Order status updated to: $status'); window.location.href=window.location.href;</script>";
            }
        }
        ?>
    </div>

    </div>
</body>

</html>