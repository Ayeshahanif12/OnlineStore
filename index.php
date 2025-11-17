<?php

include 'config.php';

if (!isset($_SESSION["role"]) == 'user') {
  // User is not logged in, redirect to login page
  echo "<script>alert('Please login to continue.');</script>";
  header("location: login.php");
  exit();
}


if ($user_id > 0) {
  $order_check_query = mysqli_query($conn, "SELECT id FROM checkout WHERE user_id = '$user_id' LIMIT 1");
  if ($order_check_query && mysqli_num_rows($order_check_query) > 0) {
    $has_orders = true;
  }
}


// ====== REMOVE ITEM ======
if (isset($_POST['remove_id'])) {
  $remove_id = $_POST['remove_id'];
  mysqli_query($conn, "DELETE FROM cart WHERE product_id='$remove_id' AND user_id='$user_id'");
}





$searchResults = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
  $search = mysqli_real_escape_string($conn, $_GET['search']);

  // Agar number hai to ID se bhi search kare
  if (is_numeric($search)) {
    $query = "SELECT * FROM products WHERE id = '$search' OR name LIKE '%$search%'";
  } else {
    $query = "SELECT * FROM products WHERE name LIKE '%$search%'";
  }

  $result = mysqli_query($conn, $query);
  while ($row = mysqli_fetch_assoc($result)) {
    $searchResults[] = $row;
  }
}

// ------------------- ADD TO CART -------------------
if (isset($_POST['add_to_cart'])) {

  if ($_SESSION['user_id'] != "") {



    $id = $_POST['id']; // product_id
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];


    // Check if already in cart
    $check = mysqli_query($conn, "SELECT * FROM cart WHERE product_id = '$id'");
    if (mysqli_num_rows($check) > 0) {
      // Update quantity
      $row = mysqli_fetch_assoc($check);
      $newQty = $row['qty'] + 1;
      $newTotal = $newQty * $price;

      mysqli_query($conn, "UPDATE cart 
                             SET qty = '$newQty', total = '$newTotal' 
                             WHERE product_id = '$id'");

    } else {
      $total = $price * 1;
      mysqli_query($conn, "INSERT INTO cart (user_id,product_id, name, price, image, qty, total) 
                             VALUES ('$_SESSION[user_id]', '$id', '$name', '$price', '$image', 1, '$total')");
    }

    header("Location: index.php#openCart");
    exit;
  }
} else if ($_SESSION['role'] == '') {
  echo "<script>alert('Please login to continue.');</script>";
  header("location: login.php");
  exit();
}
// ------------------- REMOVE FROM CART -------------------
if (isset($_POST['remove_id'])) {
  $remove_id = $_POST['remove_id'];
  mysqli_query($conn, "DELETE FROM cart WHERE product_id = '$remove_id'");
  header("Location: index.php#openCart");
  exit;
}
?>


<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="	https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- Font Awesome for filter icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="function.js"></script>
  <title>Trendy Wear</title>
  <style>
    /* card css */
    .P-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: flex-start;
      margin-bottom: 40px;
    }

    .p-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      padding: 20px;
      width: 23%;
      min-width: 250px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .p-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .p-card img {
      width: 80%;
      height: 200px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 15px;
    }

    .p-card .card-body {
      padding: 10px 0;
    }

    .p-card .card-title {
      font-size: 18px;
      font-weight: 600;
      color: #222;
      margin-bottom: 8px;
      text-transform: capitalize;
    }

    .p-card .card-text {
      font-size: 14px;
      color: #666;
      margin-bottom: 8px;
    }

    .p-card .card-text:last-of-type {
      font-size: 16px;
      font-weight: bold;
      color: #121212;
    }

    .addToCart {
      background: #000;
      color: #fff;
      border: none;
      font-size: 14px;
      font-weight: 600;
      padding: 9px 20px;
      border-radius: 8px;
      cursor: pointer;
      width: 101%;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .addToCart:hover {
      background: #333;
      transform: scale(1.05);
    }



    .btn-custom {
      display: block;
      width: 200px;
      margin: 10px auto;
      padding: 12px;
      background-color: #000;
      color: #fff;
      text-align: center;
      text-decoration: none;
      font-weight: 500;
      border-radius: 6px;
      transition: 0.3s;
    }

    .btn-custom:hover {
      background-color: #333;
    }



    .search-icon {
      font-size: 20px;
      color: white;
      cursor: pointer;
    }

    .search-container {
      position: relative;
      display: flex;
      align-items: center;
      z-index: 9999;
      /* upar rakhne ke liye */
    }

    .search-bar {
      width: 0;
      opacity: 0;
      padding: 6px;
      margin-left: 8px;
      border: none;
      border-radius: 4px;
      transition: all 0.3s ease;
      background: white;
      box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.2);
    }

    .search-container.active .search-bar {
      width: 200px;
      /* expand hone ke baad width */
      opacity: 1;
    }



    /* Hide clear (X) button in search inputs for WebKit browsers */
    input[type="search"]::-webkit-search-cancel-button {
      -webkit-appearance: none;
      appearance: none;
    }

    .categories {
      display: flex;
      justify-content: center;
      gap: 40px;
      /* categories ke beech gap */
      margin-top: 100px;
      margin-bottom: 60px;
      flex-wrap: wrap;
    }

    .category-item a {
      display: flex;
      flex-direction: column;
      /* image upar, text neeche */
      align-items: center;
      text-decoration: none;
      color: #000;
      margin-left: 45px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .category-item:hover {
      transform: translateY(-6px);
    }

    .category-item img {
      width: 90px;
      height: 90px;
      background: #fff;
      border-radius: 50%;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      object-fit: cover;
      margin-bottom: 8px;
      transition: all 0.3s ease;
    }

    .category-item:hover img {
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
    }

    .category-item span {
      font-size: 14px;
      text-transform: uppercase;
      font-weight: 500;
      color: #313131ff;
      text-align: center;
      line-height: 1.2;
    }

    @media (max-width: 576px) {


      /* NAVBAR TITLE */
      .nav h1{
        text-align: center;
        font-size: 46px;
        /* Bigger and bolder */
        font-weight: 800;
        letter-spacing: 3px;
        color: white;
        text-transform: uppercase;
        padding: 15px 0;
        position: relative;
        text-shadow: 0 0 8px rgba(255, 255, 255, 0.6);
        /* Soft glow */
        margin: 0 auto;
        margin-right: 216px;
      }

      /* Navbar icons ko align karna */
      .icons {
        display: flex;
        align-items: center;
        gap: 12px;
        float: right;
        margin-right: 20px;
      }

      .P-container {
        display: flex;
        justify-content: center;
        align-items: center;
      }
    }

    /* SEARCH BOX FIX */
    .search-box {
      display: none;
      position: absolute;
      top: 70px;
      /* Navbar ke neeche show hoga */
      right: 20px;
      /* Right side me (icons ke neeche) */
      background: #fff;
      padding: 10px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      width: 300px;
      /* Width fix */
      z-index: 2000;
      /* Sabse upar */
    }

    .search-box.active {
      display: block;
    }


    .nav h1 {
      margin: 0;
      text-align: center;
    }

    .badge {
      font-size: 12px;
      padding: 4px 7px;
      border: 2px solid #121212;
      /* black border so badge looks clean */
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

    /* --- MEDIA QUERIES FOR MOBILE --- */
    @media (max-width: 768px) {
      .navbar-dark .icons {
        display: flex !important;
      }

      .p-card {
        width: 48%;
        /* 2 cards per row */
        min-width: 150px;
      }

      .categories {
        gap: 15px;
        /* categories ke beech gap kam karein */
        margin-top: 80px;
      }

      .category-item img {
        width: 70px;
        height: 70px;
      }

      /* Second Navbar mobile styles */
      .navbar h1 {
        font-size: 1rem;
        /* Title chota karein */
        letter-spacing: -1px;
        /* Letters ke beech space kam karein */
        margin-left: -8px;
      }


      .navbar .icons {
        gap: 0px;
        margin-right: -32px;
        margin-top: 3px;
      }



      .navbar .icons i {
        font-size: 1.1rem !important;

        /* Icons ka size chota karein */
      }
    }

    @media (max-width: 576px) {
      .p-card {
        width: 90%;
        /* 1 card per row */
      }
    }
      .filter-box {
      display: flex;
      align-items: center;
      gap: 10px;
      background: white;
      padding: 12px 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      max-width: 280px;
    }

    .filter-box i {
      color: #333;
      font-size: 18px;
    }

    .filter-box select {
      flex: 1;
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 6px;
      outline: none;
      font-size: 14px;
      cursor: pointer;
    }

    .filter-box select:focus {
      border-color: #555;
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
        <li> <a class="links" href="<?php echo BASE_URL; ?>/myaccount/settings.php">Settings</a> </li>
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

  <nav class="navbar navbar-dark"
    style="background-color:#121212; height:70px; padding:0 20px; position: relative; z-index: 1050;">
    <h1 style="color:white;">Trendy Wear</h1>

    <div class="icons d-flex align-items-center ms-auto">
      <!-- Search -->
      <div class="search-container">
        <i class="bi bi-search text-white fs-5" id="openSearch" style="cursor:pointer;"></i>
        <div class="search-box" id="searchBox" style="height: 51px;">
          <form method="GET" action="index.php" class="d-flex">
            <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search..."
              value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
              style="height: 35px;">
            <button type="button" style="height: 35px;" class="btn btn-sm btn-dark ms-2" id="closeSearch"><i
                class="bi bi-x-lg"></i></button>
          </form>
          <div id="suggestionsBox"></div>
        </div>
      </div>
      <script>
        document.getElementById("searchInput").addEventListener("keyup", function () {
          let query = this.value.trim();

          if (query.length > 0) {
            fetch("search_suggestions.php?term=" + encodeURIComponent(query))
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

                    div.innerHTML = `
              <img src="${item.image}" width="30" height="30" style="border-radius:5px; margin-right:10px;">
              <span>${item.name}</span>
            `;

                    // Jab click karo to search box fill ho jaye
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
      </script>

      <!-- Shipping Icon -->
      <?php if ($has_orders): ?>
        <a href="order_status.php" class="ms-3 text-white">
          <i class="fa fa-truck fs-5"></i>
        </a>
      <?php else: ?>
        <a href="#" onclick="alert('First order please'); return false;" class="ms-3 text-white">
          <i class="fa fa-truck fs-5"></i>
        </a>
      <?php endif; ?>

      <!-- Cart -->
      <button class="btn ms-3 position-relative" style="background:transparent; border:none;" type="button"
        data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">

        <!-- Cart Icon -->
        <i class="fa fa-shopping-cart text-white fs-5"></i>

        <!-- Badge -->
        <?php
        $cartCount = 0;
        $result = mysqli_query($conn, "SELECT COUNT(*) AS totalItems FROM cart WHERE user_id = '$user_id'");
        if ($result) {
          $row = mysqli_fetch_assoc($result);
          $cartCount = $row['totalItems'] ?? 0;
        }
        ?>

        <?php if ($cartCount > 0): ?>
          <span class="position-absolute top-2 start-101 translate-middle badge rounded-pill bg-danger">
            <?php echo $cartCount; ?>
          </span>
        <?php endif; ?>
      </button>

      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasRightLabel">My Cart</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">

          </button>
        </div>
        <div class="offcanvas-body" id="cartItems">



          <?php
          $total = 0;

          $result = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              $subtotal = $row['total'];
              $total += $subtotal;
              echo "
<div class='cart-item d-flex align-items-center mb-3'>
  <img src='{$row['image']}' width='50' class='me-2'>
  <div class='flex-grow-1'>
    <h6>{$row['name']}</h6>
    <p>PKR {$row['price']} x {$row['qty']} = PKR {$subtotal}</p>
  </div>
  <form method='post' action='index.php#openCart'>
    <input type='hidden' name='remove_id' value='{$row['product_id']}'>
    <button type='submit' class='btn btn-sm btn-danger'>
      <i class='fa fa-trash'></i>
    </button>
  </form>
</div>

    ";
            }
            echo "<hr><h5>Total: PKR $total</h5>";
          } else {
            echo "<p>Your cart is empty.</p>";
          }
          ?>

        </div>

        <div style="text-align: center; margin: 20px;">
          <a href="cart.php" class="btn btn-dark w-75 mb-2">View Cart</a>
          <a href="checkout.php" class="btn btn-primary w-75">Checkout</a>
        </div>



      </div>
  </nav>
  <script>
    const openSearch = document.getElementById("openSearch");
    const closeSearch = document.getElementById("closeSearch");
    const searchBox = document.getElementById("searchBox");
    const searchForm = searchBox.querySelector("form");
    const searchInput = searchBox.querySelector("input");

    // Open search
    openSearch.addEventListener("click", () => {
      searchBox.classList.add("active");
      searchInput.focus();
    });

    // Close search
    closeSearch.addEventListener("click", () => {
      searchBox.classList.remove("active");
    });

    // Clear input after form submit
    searchForm.addEventListener("submit", () => {
      setTimeout(() => {
        searchInput.value = "";       // input empty ho jayega
        // searchBox.classList.remove("active"); // agar chaho to search bar bhi close ho jaye
      }, 100);
    });
  </script>

  <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
    <?php
    $select = "SELECT * FROM carousel";
    $result = mysqli_query($conn, $select);
    $dataAll = [];

    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $dataAll[] = $row;
      }
    } else {
      echo "<p>No carousel found.</p>";
    }
    ?>

    <!-- Indicators -->
    <div class="carousel-indicators">
      <?php foreach ($dataAll as $index => $row): ?>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?php echo $index; ?>"
          class="<?php echo $index === 0 ? 'active' : ''; ?>"
          aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $index + 1; ?>">
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Slides -->
    <div class="carousel-inner">
      <?php foreach ($dataAll as $index => $row): ?>
        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
          <img src="<?php echo htmlspecialchars('image/' . $row['img']); ?>" class="d-block w-100"
            alt="<?php echo htmlspecialchars($row['alt_text']); ?>">
          <div class="carousel-caption d-none d-md-block">
            <p style="display: none;"><?php echo htmlspecialchars($row['alt_text']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>




  <div style="margin: 0px;" id="cat">
    <div class="categories" style="margin-top: 150px;">
      <?php
      $cat = mysqli_query($conn, "SELECT * FROM nav_categories");
      while ($fcat = mysqli_fetch_assoc($cat)) {
        echo "
        <div class='category-item'>
          <a href='#cart{$fcat['id']}'>
            <img src='image/{$fcat['img']}' alt='{$fcat['name']}'>
            <span>{$fcat['name']}</span>
          </a>
        </div>
      ";
      }
      ?>
    </div>
  </div>

  <?php
  $searchResults = [];
  if (isset($_GET['search']) && $_GET['search'] !== "") {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT * FROM products WHERE name LIKE '%$search%' OR id LIKE '%$search%'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
      while ($row = mysqli_fetch_assoc($result)) {
        $searchResults[] = $row;
      }
    }
  }
  ?>
  <!-- SEARCH BOX CARD -->
  <?php if (isset($_GET['search'])): ?>
    <div style="margin:20px;">
      <h3 style="color:#121212;">Search Results:</h3>
      <?php if (count($searchResults) > 0): ?>
        <div class="P-container">
          <?php foreach ($searchResults as $rows): ?>
            <div class="p-card">
              <img src="<?php echo $rows['image']; ?>" alt=""
                style="width: 60%; margin-left:30px; border-radius: 10px; height: auto;">
              <div class="card-body">
                <h5 class="card-title"><span
                    style="font-weight: bold; color:#121212; font-size: 18px; font-weight: 500; text-transform:capitalize;"><?php echo $rows['name']; ?></span>
                </h5>
                <p style="color: gray; " class="card-text"><?php echo $rows['description']; ?></p>
                <p style="color: black;" class="card-text"> PKR <?php echo $rows['price']; ?></p>
                <form method="post" action="index.php#openCart">
                  <input type="hidden" name="id" value="<?php echo $rows['id']; ?>">
                  <input type="hidden" name="name" value="<?php echo $rows['name']; ?>">
                  <input type="hidden" name="price" value="<?php echo $rows['price']; ?>">
                  <input type="hidden" name="image" value="<?php echo $rows['image']; ?>">
                  <button type="submit" name="add_to_cart" class="addToCart">Add to Cart</button>
                </form>

              </div>
            </div>


          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p style="color:red;">No products found for "<?php echo htmlspecialchars($_GET['search']); ?>"</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

<!-- filter pricing -->

<form method="GET">
  <div class="filter-box">
    <i class="fa-solid fa-filter"></i>
    <?php if (isset($_GET['search'])): ?>
          <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
      <?php endif; ?>

    <select name="price_filter" onchange="this.form.submit()">
      <option value="">Filter by price</option>
      <option value="low_to_high" name="low_to_high">Low to High</option>
      <option value="high_to_low" name="high_to_low">High to Low</option>
    </select>
  </div>
      </form>

<?php
if (isset($_GET['price_filter'])) {
    $filter = $_GET['price_filter'];
    if ($filter === 'low_to_high') {
        $order_clause = "ORDER BY price ASC";
    } elseif ($filter === 'high_to_low') {
        $order_clause = "ORDER BY price DESC";
    } else {
        $order_clause = "";
    }

} else {
    $order_clause = "";  // Default order clause if no filter is applied
  }
?>


  <!-- PRODUCT CARD CONTAINER -->
  <?php

  $cat = mysqli_query($conn, "SELECT * FROM nav_categories");

  while ($fcat = mysqli_fetch_assoc($cat)) {
    echo "<div class='P-container' id='cart{$fcat['id']}'>";
    $sql = "SELECT * FROM products WHERE category_id = {$fcat['id']}"; // Base query
      if(isset($order_clause) && $order_clause != ""){
         $sql .= " ".$order_clause; // Append order clause if a filter is selected
      }
    $pro = mysqli_query($conn, $sql);
    while ($rows = mysqli_fetch_assoc($pro)) { ?>
      <div class="p-card">
        <img src="<?php echo $rows['image']; ?>" alt=""
          style="width: 60%; margin-left:30px; border-radius: 10px; height: auto;">
        <div class="card-body">
          <h5 class="card-title"><span
              style="font-weight: bold; color:#121212; font-size: 18px; font-weight: 500; text-transform:capitalize;"><?php echo $rows['name']; ?></span>
          </h5>
          <p style="color: gray; " class="card-text"><?php echo $rows['description']; ?></p>
          <p style="color: black;" class="card-text"> PKR <?php echo $rows['price']; ?></p>
          <form method="post" action="index.php#openCart">
            <input type="hidden" name="id" value="<?php echo $rows['id']; ?>">
            <input type="hidden" name="name" value="<?php echo $rows['name']; ?>">
            <input type="hidden" name="price" value="<?php echo $rows['price']; ?>">
            <input type="hidden" name="image" value="<?php echo $rows['image']; ?>">
            <button type="submit" name="add_to_cart" class="addToCart">Add to Cart</button>
          </form>

        </div>
      </div>


    <?php }
    echo "</div>";

  } ?>










  <!-- POLICY DIV -->
  <div id="policy">
    <section class="privacy-policy">
      <div class="policy-wrapper">
        <h1 class="policy-title">Privacy Policy</h1>
        <p class="policy-intro">
          At <strong>Trendy Wear</strong>, we are committed to protecting your privacy and ensuring that your personal
          information is handled in a safe and responsible manner.
        </p>

        <div class="policy-section">
          <h2>1. Information We Collect</h2>
          <p>We collect personal information including your name, email, shipping address, and payment details when
            you
            place an order or create an account with us.</p>
        </div>

        <div class="policy-section">
          <h2>2. Use of Information</h2>
          <p>Your information is used to process your orders, improve our services, and communicate updates or
            promotional offers — only if you opt-in.</p>
        </div>

        <div class="policy-section">
          <h2>3. Data Security</h2>
          <p>All data is encrypted and stored securely. We implement strict measures to prevent unauthorized access,
            misuse, or disclosure.</p>
        </div>

        <div class="policy-section">
          <h2>4. Cookies</h2>
          <p>We use cookies to personalize your experience, analyze site traffic, and enhance website functionality.
            You
            can manage cookies in your browser settings.</p>
        </div>

        <div class="policy-section">
          <h2>5. Third-Party Disclosure</h2>
          <p>We do not sell or trade your personal information. It may be shared only with trusted partners who assist
            us in delivering our services.</p>
        </div>

        <div class="policy-section">
          <h2>6. Your Rights</h2>
          <p>You may request access to, correction of, or deletion of your personal data at any time by contacting us.
          </p>
        </div>

        <p class="policy-footer">
          For any queries, please contact us at <a href="mailto:support@trendywear.com">support@trendywear.com</a>
        </p>
      </div>
    </section>

  </div>

  <!-- CONTACT US  -->
  <div id="contactus">
    <section class="contact-section">
      <div class="contact-wrapper">
        <h2 class="contact-title">Contact Us</h2>
        <form class="contact-form" method="post">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required />
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required />
          </div>

          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" rows="5" name="message" placeholder="Your Message" required></textarea>
          </div>

          <button type="submit" class="contact-btn" name="contact">Send Message</button>
        </form>
      </div>
    </section>
  </div>


  <!-- CONNECTING CONTACT US WITH PHP -->
  <?php if (isset($_POST['contact'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "<script>alert('Invalid Email Address');</script>";
    } else {
      $stmt = mysqli_prepare($conn, "INSERT INTO contact_us (name, email, message) VALUES (?, ?, ?)");
      mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);
      $exe = mysqli_stmt_execute($stmt);

      if ($exe) {
        echo "<script>alert('Message sent successfully!');</script>";
      } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
      }

      mysqli_stmt_close($stmt);
    }
  }

  ?>


  <!-- FOOTER -->
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
      <a href="login.php">LOGIN</a>
      <a href="signup.php">CREATE ACCOUNT</a>
      <a href="<?php echo BASE_URL; ?>/myaccount/settings.php">SETTINGS</a>
      <a href="<?php echo BASE_URL; ?>/order_status.php">ORDER HISTORY</a>
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
  // CONNECTING NEWSLETTER WITH PHP
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (window.location.hash === "#openCart") {
      var myOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasRight'));
      myOffcanvas.show();
    }
  });

  // Close the connection at the very end of the script
  <?php
    if (isset($conn)) {
        mysqli_close($conn);
    }
  ?>
</script>