<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>order-status</title>
    <link rel="shortcut icon" href="" type="image/x-icon">
    <style>
        .order-status {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 25px 30px;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #333;
        }

        .order-status h2 {
            font-size: 22px;
            margin-bottom: 15px;
            font-weight: 600;
            color: #0070f3;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .order-status p {
            margin: 6px 0;
            font-size: 15px;
            line-height: 1.5;
        }

        .order-status p span {
            font-weight: 600;
            color: #555;
        }

        /* Order ID style */
        .order-status .order-id {
            font-size: 14px;
            color: #888;
            margin-bottom: 15px;
        }

        /* Info grid for better alignment */
        .order-status .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
        }

        .order-status .info-grid p {
            margin: 0;
            background: #fafafa;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            border: 1px solid #eee;
        }

        /* Address box */
        .order-status .address-box {
            margin-top: 15px;
            background: #fdfdfd;
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-size: 14px;
        }

        /* Status badge */
        .order-status .status-badge {
            display: inline-block;
            background: #ffc107;
            color: #333;
            font-size: 13px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            margin-top: 10px;
        }
    </style>

</head>

<body>
    <div class="container">
        <?php
        session_start();
        $conn = mysqli_connect("localhost", "root", "", "clothing_store");
        if (!$conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }
        $check = mysqli_query($conn, "SELECT * FROM checkout WHERE user_id='$_SESSION[user_id]'");
        while ($rows = mysqli_fetch_assoc($check)) { ?>
            <div class="order-status">
                <h2>Order Status</h2>
                <p class="order-id">Order #<?= $rows['id'] ?></p>

                <div class="info-grid">
                    <p> <?= $rows['first_name'] ?></p>
                    <p> <?= $rows['last_name'] ?></p>
                    <p> <?= $rows['email'] ?></p>
                    <p> <?= $rows['phone'] ?></p>
                    <p> <?= $rows['postal_code'] ?></p>
                    <p> <?= $rows['city'] ?></p>
                    <p> <?= $rows['country'] ?></p>
                </div>

                <div class="address-box">
                    <span>Address:</span><br>
                    <?= $rows['address'] ?>
                </div>

            </div>
        <?php } ?>

        <hr>
        <!-- <div class="order-status">
            <h2>Order Status</h2>
            <p><strong>Order ID:</strong> <?= $rows['id'] ?></p>
            <p><strong>Product Name:</strong> <?= $rows['product_name'] ?></p>
            <p><strong>Quantity:</strong> <?= $rows['quantity'] ?></p>
            <p><strong>Total Price:</strong> $<?= $rows['total_price'] ?></p>
            <p><strong>Status:</strong> <?= $rows['status'] ?></p>
        </div> -->
</body>

</html>