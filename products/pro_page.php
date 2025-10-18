<?php
session_start();
require_once '../db_config.php';

// 1. Check if ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("Product not found.");
}

$product_id = (int) $_GET['id'];

// 2. Fetch product details from the database using a prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
  die("Product not found.");
}

$rows = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// 3. Handle 'Add to Cart' from this page
if (isset($_POST['add_to_cart'])) {
  // Check if user is logged in
  if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != "") {
    $user_id = $_SESSION['user_id'];
    $id = (int) $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $code = $_POST['code'];
    $size = $_POST['size'];
    $qty = (int) ($_POST['qty'] ?? 1); // Get quantity from form

    // Make sure a size is selected
    if (isset($size) && !empty($size)) {
      // Check if already in cart for this user
      $check_stmt = mysqli_prepare($conn, "SELECT * FROM cart WHERE product_id = ? AND user_id = ?");
      mysqli_stmt_bind_param($check_stmt, "ii", $id, $user_id);
      mysqli_stmt_execute($check_stmt);
      $check_result = mysqli_stmt_get_result($check_stmt);

      if (mysqli_num_rows($check_result) > 0) {
        // Update quantity
        $cart_row = mysqli_fetch_assoc($check_result);
        $newQty = $cart_row['qty'] + $qty;
        $newTotal = $newQty * $price;
        $update_stmt = mysqli_prepare($conn, "UPDATE cart SET qty = ?, total = ? WHERE product_id = ? AND user_id = ?");
        mysqli_stmt_bind_param($update_stmt, "idii", $newQty, $newTotal, $id, $user_id);
        mysqli_stmt_execute($update_stmt);
      } else {
        // Insert new item
        $total = $price * $qty;
        $insert_stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, name, price, image, qty, total, size, code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($insert_stmt, "iisdsidss", $user_id, $id, $name, $price, $image, $qty, $total, $size, $code);
        mysqli_stmt_execute($insert_stmt);
      }
      // Set a success message in the session and refresh the current page
      $_SESSION['cart_message'] = htmlspecialchars($name) . " has been added to your cart!";
      // Refresh the current page and add #openCart to the URL to trigger the offcanvas
      header("Location: pro_page.php?id=" . $product_id . "#openCart");
      exit;
    } else {
      // If size is not selected, show an error message
      $_SESSION['cart_message'] = '<span style="color: red;">Please select a size before adding to cart!</span>';
      header("Location: pro_page.php?id=" . $product_id);
      exit;
    }
  } else {
    // If user is not logged in, redirect to login
    header("Location: ../login.php");
    exit();
  }
}

// ====== REMOVE ITEM ======
if (isset($_POST['remove_id'])) {
  $remove_id = $_POST['remove_id'];
  mysqli_query($conn, "DELETE FROM cart WHERE product_id='$remove_id' AND user_id='$user_id'");
}


// Fetch categories for navbar
$category = mysqli_query($conn, "SELECT * FROM nav_categories ");

// Get user ID for cart logic
$user_id = $_SESSION['user_id'] ?? 0;

// Check if the user has placed any orders (copied from index.php)
$has_orders = false;
if ($user_id > 0) {
  $order_check_query = mysqli_query($conn, "SELECT id FROM checkout WHERE user_id = '$user_id' LIMIT 1");
  if ($order_check_query && mysqli_num_rows($order_check_query) > 0) {
    $has_orders = true;
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($rows['name']); ?> - Trendy Wear</title>
  <!-- Links from index.php (Corrected path for main.css) -->
  <link rel="stylesheet" href="../main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* Navbar dropdown style from index.php */
    .type {
      display: none;
      position: absolute;
      top: 11%;
      left: 111px;
      background-color: #333;
      border-radius: 24px;
      padding: 10px 0;
      z-index: 999;
      min-width: 200px;
    }

    /* Search Box styles from index.php */
    .search-box {
      display: none;
      position: absolute;
      top: 70px;
      right: 20px;
      background: #fff;
      padding: 10px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      width: 300px;
      z-index: 2000;
    }

    .search-box.active {
      display: block;
    }

    #suggestionsBox {
      position: absolute;
      top: 55px;
      /* Below the input */
      left: 10px;
      right: 10px;
      width: calc(100% - 20px);
      background: #fff;
      border: 1px solid #ddd;
      border-top: none;
      z-index: 2001;
      max-height: 300px;
      overflow-y: auto;
      border-radius: 0 0 8px 8px;
    }

    .badge {
      font-size: 12px;
      padding: 4px 7px;
      border: 2px solid #121212;
    }

    body {
      background-color: #f8f9fa;
      font-family: 'Poppins', sans-serif;
    }

    .product-page {
      max-width: 1200px;
      margin: 60px auto;
      display: flex;
      flex-wrap: wrap;
      gap: 50px;
      background: #fff;
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    /* LEFT SECTION */
    .product-gallery {
      flex: 1 1 45%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .main-image {
      width: 100%;
      border-radius: 12px;
      overflow: hidden;
      cursor: zoom-in;
    }

    .main-image img {
      width: 100%;
      transition: transform 0.4s ease;
    }

    .main-image:hover img {
      transform: scale(1.05);
    }

    .thumbs {
      display: flex;
      gap: 10px;
      margin-top: 15px;
    }

    .thumbs img {
      width: 80px;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid transparent;
      cursor: pointer;
      transition: 0.3s;
    }

    .thumbs img:hover,
    .thumbs img.active {
      border-color: #000;
    }

    /* RIGHT SECTION */
    .product-info {
      flex: 1 1 50%;
    }

    .product-info h1 {
      font-size: 2.2rem;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .product-price {
      font-size: 1.8rem;
      color: black;
      font-weight: 500;
      margin: 10px 0 25px 0;
    }

    /* SIZE */
    .size-options button {
      border: 1px solid #ccc;
      border-radius: 50%;
      width: 45px;
      height: 45px;
      margin-right: 10px;
      background: #fff;
      font-weight: 500;
      transition: all 0.3s;
    }

    .size-options button:hover,
    .size-options button.active {
      background: #000;
      color: #fff;
    }

    /* QUANTITY */
    .quantity-control {
      display: flex;
      align-items: center;
      gap: 15px;
      margin: 25px 0;
    }

    .quantity-control button {
      width: 35px;
      height: 35px;
      border: 1px solid #ccc;
      background: #fff;
      border-radius: 5px;
      font-weight: bold;
    }

    /* ADD TO CART */
    .addToCart {
      background: #000;
      color: #fff;
      border: none;
      padding: 14px 40px;
      border-radius: 8px;
      font-size: 1.1rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: 0.3s;
    }

    .addToCart:hover {
      background: #333;
    }

    /* TABS */
    .tab-section {
      margin-top: 50px;
    }

    .nav-tabs .nav-link {
      border: none;
      color: #333;
      font-weight: 500;
    }

    .nav-tabs .nav-link.active {
      border-bottom: 2px solid #000;
      color: #000;
    }

    .tab-content {
      margin-top: 20px;
      font-size: 0.95rem;
      color: #555;
      line-height: 1.7;
    }

    @media (max-width: 900px) {
      .product-page {
        flex-direction: column;
        padding: 20px;
      }
    }
    .cart-item img {
    width: 50px;
    height: 75px;
    margin-right: 10px;
}
  </style>
</head>

<body>
  <!-- NAVBAR 1 (TOGGLER) from index.php -->
  <div class="collapse" id="navbarToggleExternalContent">
    <div class="bg-dark p-4">
      <span class="text-muted"></span>
      <ul>
        <li> <a class="links" href="../index.php">Home</a></li>
        <li class="nav-item">
          <a class="links" href="#">Categories</a>
          <ul class="type">
            <?php
            // Reset pointer and loop through categories
            mysqli_data_seek($category, 0);
            while ($cat_row = mysqli_fetch_assoc($category)) {
              echo "<li><a href='../index.php#cart{$cat_row['id']}'>{$cat_row['name']}</a></li>";
            }
            ?>
          </ul>
        </li>
        <li> <a class="links" href="../index.php#policy">Policy</a> </li>
        <li> <a class="links" href="../index.php#contactus">Contact us</a> </li>
        <li> <a class="links" href="http://localhost/store/myaccount/settings.php">Settings</a> </li>
      </ul>
    </div>
  </div>
  <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false"
        aria-label="Toggle navigation" style="background-color: transparent; border: none;">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>

  <!-- NAVBAR 2 (MAIN) from index.php -->
  <nav class="navbar navbar-dark" style="background-color:#121212; height:70px; padding:0 20px; position:relative;">
    <h1 style="color:white; margin:0; text-align: center;">Trendy Wear</h1>
    <div class="icons d-flex align-items-center ms-auto">
      <!-- Search Icon & Box (Copied from index.php) -->
      <div class="search-container">
        <i class="bi bi-search text-white fs-5" id="openSearch" style="cursor:pointer;"></i>
        <div class="search-box" id="searchBox" style="height: 51px;">
          <form method="GET" action="../index.php" class="d-flex">
            <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search..."
              value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
              style="height: 35px;">
            <button type="button" style="height: 35px;" class="btn btn-sm btn-dark ms-2" id="closeSearch"><i
                class="bi bi-x-lg"></i></button>
          </form>
          <div id="suggestionsBox"></div>
        </div>
      </div>

      <!-- Shipping Icon (Copied from index.php) -->
      <?php if ($has_orders): ?>
        <a href="../order_status.php" class="ms-3 text-white">
          <i class="fa fa-truck fs-5"></i>
        </a>
      <?php else: ?>
        <a href="#" onclick="alert('First order please'); return false;" class="ms-3 text-white">
          <i class="fa fa-truck fs-5"></i>
        </a>
      <?php endif; ?>

      <!-- Cart Icon with Offcanvas Toggle -->
      <button class="btn ms-3 position-relative" style="background:transparent; border:none;" type="button"
        data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">

        <!-- Cart Icon -->
        <i class="fa fa-shopping-cart text-white fs-5"></i>

        <!-- Badge -->
        <?php
        $cartCount = 0;
        if ($user_id > 0) {
          $result_count = mysqli_query($conn, "SELECT COUNT(*) AS totalItems FROM cart WHERE user_id = '$user_id'");
          if ($result_count) {
            $row_count = mysqli_fetch_assoc($result_count);
            $cartCount = $row_count['totalItems'] ?? 0;
          }
        }
        ?>

        <?php if ($cartCount > 0): ?>
          <span class="position-absolute top-2 start-101 translate-middle badge rounded-pill bg-danger">
            <?php echo $cartCount; ?>
          </span>
        <?php endif; ?>
      </button>
    </div>

    <!-- Offcanvas Cart HTML (Copied from index.php) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">My Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body" id="cartItems">
        <?php
        $total_offcanvas = 0;
        if ($user_id > 0) {
          $result_offcanvas = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
          if (mysqli_num_rows($result_offcanvas) > 0) {
            while ($row_offcanvas = mysqli_fetch_assoc($result_offcanvas)) {
              $subtotal_offcanvas = $row_offcanvas['total'];
              $total_offcanvas += $subtotal_offcanvas;
              echo "<div class='cart-item d-flex align-items-center mb-3'>
                                <img src='../{$row_offcanvas['image']}' width='50' class='me-2'>
                                <div class='flex-grow-1'>
                                    <h6 style='text-transform: capitalize;'>{$row_offcanvas['name']}</h6>
                                    " . (!empty($row_offcanvas['code']) ? "<small>Code: {$row_offcanvas['code']}</small><br>" : "") . "
                                    " . (!empty($row_offcanvas['size']) ? "<small>Size: {$row_offcanvas['size']}</small>" : "") . "
                                    <p>PKR {$row_offcanvas['price']} x {$row_offcanvas['qty']} = PKR {$subtotal_offcanvas}</p>
                                </div>
                              </div>
                               <input type='hidden' name='remove_id' value='{$row['product_id']}'>
    <button type='submit' class='btn btn-sm btn-danger'>
      <i class='fa fa-trash'></i>
    </button>
                              
                              ";
            }
            echo "<hr><h5>Total: PKR $total_offcanvas</h5>";
          } else {
            echo "<p>Your cart is empty.</p>";
          }
        } else {
          echo "<p>Your cart is empty.</p>";
        } ?>
      </div>
      <div style="text-align: center; margin: 20px;">
        <a href="../cart.php" class="btn btn-dark w-75 mb-2">View Cart</a>
        <a href="../checkout.php" class="btn btn-primary w-75">Checkout</a>
      </div>
    </div>
  </nav>

  <div class="product-page" style="margin-top: 20px;">
    <!-- Left: Product Gallery -->
    <div class="product-gallery">
      <div class="main-image">
        <img id="mainImg" src="../<?php echo htmlspecialchars($rows['image']); ?>"
          alt="<?php echo htmlspecialchars($rows['name']); ?>">
      </div>
      <div class="thumbs">
        <!-- These are placeholders, you can add more images later -->
        <img src="../<?php echo htmlspecialchars($rows['image']); ?>" class="active thumb" alt="Thumbnail 1">
      </div>
    </div>

    <!-- Right: Product Info -->
    <div class="product-info">
      <h1><?php echo htmlspecialchars($rows['name']); ?></h1>
      <?php
      // Display the cart message if it exists
      if (isset($_SESSION['cart_message'])) {
        echo '<div class="alert alert-success" role="alert">' . $_SESSION['cart_message'] . '</div>';
        // Unset the message so it doesn't show again on refresh
        unset($_SESSION['cart_message']);
      }
      ?>
      <p>Code: <?php echo htmlspecialchars($rows['pro_code']); ?></p>
      <p class="product-price">PKR <?php echo htmlspecialchars($rows['price']); ?></p>

      <!-- Size Options -->
      <div class="size-options mb-3">
        <?php
        $available_sizes = explode(',', $rows['size']);
        foreach ($available_sizes as $size) {
          if (!empty(trim($size))) {
            echo "<button type='button' class='size-btn'>" . htmlspecialchars(trim($size)) . "</button>";
          }
        }
        ?>
      </div>

      <!-- Quantity Control -->
      <div class="quantity-control">
        <button type="button" id="minusBtn">−</button>
        <span id="qtyValue">1</span>
        <button type="button" id="plusBtn">+</button>
      </div>

      <!-- Add to Cart Form -->
      <form method="post" action="">
        <input type="hidden" name="size" id="selectedSize" value=""> <!-- This will be set by JS -->
        <input type="hidden" name="qty" id="selectedQty" value="1"> <!-- This will be set by JS -->
        <input type="hidden" name="id" value="<?php echo (int) $rows['id']; ?>">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($rows['name']); ?>">
        <input type="hidden" name="price" value="<?php echo htmlspecialchars($rows['price']); ?>">
        <input type="hidden" name="image" value="<?php echo htmlspecialchars($rows['image']); ?>">
        <input type="hidden" name="code" value="<?php echo htmlspecialchars($rows['pro_code']); ?>">
        <button type="submit" name="add_to_cart" class="addToCart">ADD TO CART</button>
      </form>
      <!-- Tabs -->
      <div class="tab-section">
        <ul class="nav nav-tabs" id="productTabs" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button"
              role="tab">Description</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" id="care-tab" data-bs-toggle="tab" data-bs-target="#care" type="button"
              role="tab">Care Instructions</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" id="disc-tab" data-bs-toggle="tab" data-bs-target="#disc" type="button"
              role="tab">Disclaimer</button>
          </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane fade show active" id="desc" role="tabpanel">
            <?php echo htmlspecialchars($rows['description']); ?>
          </div>
          <div class="tab-pane fade" id="care" role="tabpanel">
            <p>Hand wash or machine wash cold with like colors. Do not bleach. Line dry in shade. Iron on medium.</p>
          </div>
          <div class="tab-pane fade" id="disc" role="tabpanel">
            <p>Product color may vary slightly due to photographic lighting or your device display settings.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER from index.php -->
  <div class="foot">
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
      <a href="../login.php">LOGIN</a>
      <a href="../signup.php">CREATE ACCOUNT</a>
      <a href="http://localhost/store/myaccount/settings.php">SETTINGS</a>
      <a href="http://localhost/store/myaccount/order_status.php">ORDER HISTORY</a>
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

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "<script>alert('Invalid email');</script>";
    } elseif (!preg_match('/^\d{11}$/', $whatsapp)) {
      echo "<script>alert('WhatsApp number must be 11 digits');</script>";
    } else {
      $stmt = mysqli_prepare($conn, "INSERT INTO newsletter (email, whatsappno) VALUES (?, ?)");
      mysqli_stmt_bind_param($stmt, "ss", $email, $whatsapp);
      if (mysqli_stmt_execute($stmt)) {
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Quantity Buttons
    // JS to show offcanvas (Copied from index.php)
    document.addEventListener("DOMContentLoaded", function () {
      if (window.location.hash === "#openCart") {
        var myOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasRight'));
        myOffcanvas.show();
      }
    });

    // Search Box JS (Copied from index.php)
    const openSearch = document.getElementById("openSearch");
    const closeSearch = document.getElementById("closeSearch");
    const searchBox = document.getElementById("searchBox");
    const searchInput = searchBox.querySelector("input");

    openSearch.addEventListener("click", () => {
      searchBox.classList.add("active");
      searchInput.focus();
    });

    closeSearch.addEventListener("click", () => {
      searchBox.classList.remove("active");
    });

    // Search Suggestions JS (Copied from index.php, path adjusted)
    document.getElementById("searchInput").addEventListener("keyup", function () {
      let query = this.value.trim();

      if (query.length > 0) {
        fetch("../search_suggestions.php?term=" + encodeURIComponent(query))
          .then(res => res.json())
          .then(data => {
            let box = document.getElementById("suggestionsBox");
            box.innerHTML = "";

            if (data.length > 0) {
              data.forEach(item => {
                let div = document.createElement("div");
                div.style.display = "flex";
                div.style.alignItems = "center";
                div.style.padding = "5px";
                div.style.cursor = "pointer";

                // Path to image is adjusted with ../
                div.innerHTML = `
                                <img src="../${item.image}" width="30" height="30" style="border-radius:5px; margin-right:10px;">
                                <span>${item.name}</span>
                            `;

                div.addEventListener("click", function () {
                  document.getElementById("searchInput").value = item.name;
                  box.style.display = "none";
                  // Submit form automatically
                  document.querySelector(".search-box form").submit();
                });

                box.appendChild(div);
              });
              box.style.display = "block";
            } else {
              box.style.display = "none";
            }
          });
      } else {
        document.getElementById("suggestionsBox").style.display = "none";
      }
    });


    const plusBtn = document.getElementById('plusBtn');
    const minusBtn = document.getElementById('minusBtn');
    const qtyValue = document.getElementById('qtyValue');
    const selectedQtyInput = document.getElementById('selectedQty');
    let qty = 1;

    plusBtn.addEventListener('click', () => {
      qty++;
      qtyValue.textContent = qty;
      selectedQtyInput.value = qty;
    });
    minusBtn.addEventListener('click', () => {
      if (qty > 1) qty--;
      qtyValue.textContent = qty;
      selectedQtyInput.value = qty;
    });

    // Size Selection
    const sizeBtns = document.querySelectorAll('.size-btn');
    const selectedSizeInput = document.getElementById('selectedSize');
    sizeBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        sizeBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedSizeInput.value = btn.textContent;
      });
    });

    // Thumbnail Switch
    const mainImg = document.getElementById('mainImg');
    const thumbs = document.querySelectorAll('.thumb');
    thumbs.forEach(thumb => {
      thumb.addEventListener('click', () => {
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
        mainImg.src = thumb.src;
      });
    });

    // Add to cart validation
    document.querySelector('.addToCart').closest('form').addEventListener('submit', function (e) {
      if (!selectedSizeInput.value) {
        e.preventDefault();
        alert('Please select a size!');
      }
    });

  </script>
</body>

</html>