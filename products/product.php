<?php
include '../config.php';

// Category filter
if (isset($_GET['category_id'])) {
    $cat_id = intval($_GET['category_id']);
    $select = "SELECT * FROM products WHERE category_id = $cat_id";
} else {
    $select = "SELECT * FROM products";
}

$result = mysqli_query($conn, $select);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Products</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> 

<style>
body {
    display: flex;
    margin: 0;
    font-family: Arial, sans-serif;
}
.sidebar {
    width: 250px;
    background: #212529;
    color: white;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding: 20px 0;
}
.sidebar .nav-link {
    color: white;
}
.sidebar .nav-link.active {
    background-color: #0d6efd;
}
.main-content {
   margin-left: 250px;
    padding: 20px;
    width: 100%;
}
.P-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.p-card {
    background: #1f1f1f;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255,255,255,0.1);
    width: 207px;
    overflow: hidden;
    transition: transform 0.2s ease;
}
.p-card:hover {
    transform: scale(1.03);
}
.p-card img {
    width: 100%;
    height: auto;
    height: 207px;
    object-fit: contain;
    padding: 20px;
}
.card-body {
    padding: 15px;
}
.card-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 8px;
    text-transform: capitalize;
    color: #fff;
}
.card-text {
    font-size: 14px;
    color: #bbb;
    margin-bottom: 8px;
}
.price {
    color: #ffcc00;
    font-weight: bold;
    font-size: 16px;
}
.btn-edit {
    background: #4CAF50;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
}
.btn-delete {
    background: #E53935;
    color: white;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
}
.no-products {
    font-size: 18px;
    text-align: center;
    width: 100%;
    margin-top: 50px;
    color: #ccc;
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column p-3">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4">Admin Panel</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="<?php echo BASE_URL; ?>/adminpanel/adminpage.php" class="nav-link active"><i class="bi bi-house-door-fill me-2"></i> Home</a></li>
        <li><a href="<?php echo BASE_URL; ?>/adminpanel/dashboard.php" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li><a href="<?php echo BASE_URL; ?>/adminpanel/order.php" class="nav-link"><i class="bi bi-table me-2"></i> Orders</a></li>
        <li><a href="<?php echo BASE_URL; ?>/products/product.php" class="nav-link"><i class="bi bi-grid me-2"></i> Products</a></li>
        <li><a href="<?php echo BASE_URL; ?>/adminpanel/user.php" class="nav-link"><i class="bi bi-people me-2"></i> Customers</a></li>
        <li><a href="<?php echo BASE_URL; ?>/adminpanel/category.php" class="nav-link"><i class="bi bi-tags me-2"></i> Categories</a></li>
        <li><a href="<?php echo BASE_URL; ?>/newsletter/fetchnewsletter.php" class="nav-link"><i class="bi bi-envelope me-2"></i> Newsletter</a></li>
        <li><a href="<?php echo BASE_URL; ?>/contactus/fetchmessages.php" class="nav-link"><i class="bi bi-telephone me-2"></i> Contact Us</a></li>
    </ul>
<hr>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
        <strong>Admin</strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li><a class="dropdown-item" href="#">New project...</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/login.php">Sign out</a></li>
      </ul>
    </div>
  </div>


<!-- Main Content -->
<div class="main-content">
    <!-- Product Form -->
    <form action="" method="post" enctype="multipart/form-data" class="mb-4">
        <h3>Add New Product</h3>
        <input type="text" name="name" placeholder="Enter Name" class="form-control mb-2" required>
        <input type="file" name="image" class="form-control mb-2" required>
        <input type="text" name="description" placeholder="Enter Description" class="form-control mb-2" required>
        <input type="text" name="price" placeholder="Enter Price" class="form-control mb-2" required>
        <select name="category_id" class="form-control mb-2" required>
            <?php
            $cats = mysqli_query($conn, "SELECT * FROM nav_categories");
            while ($cat = mysqli_fetch_assoc($cats)) {
                echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
            }
            ?>
        </select>
        <input type="submit" value="Upload" class="btn btn-primary">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $cat_id = $_POST['category_id'];

        if (isset($_FILES['image'])) {
            $image_name = str_replace(" ", "_", $_FILES['image']['name']); // replace spaces
            $image_tmp = $_FILES['image']['tmp_name'];

            // Products folder se ek level upar jao -> clothing store/image/
            $target_path = "../image/" . basename($image_name);

            if (move_uploaded_file($image_tmp, $target_path)) {
                // Database ke liye relative path save karo
                $db_path = "image/" . basename($image_name);

                $insert = "INSERT INTO products (name, image, description, price, category_id) 
                           VALUES ('$name', '$db_path', '$description', '$price', '$cat_id')";

                if (mysqli_query($conn, $insert)) {
                    echo "<script>alert('Product added successfully!');</script>";
                    echo "<script>window.location.href='product.php';</script>";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "<script>alert('Image upload failed!');</script>";
            }
        }
    }
    ?>

    <!-- Products List -->
    <h1>Products</h1>
    <div class="P-container">
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while ($rows = mysqli_fetch_assoc($result)){ ?>
                <div class="p-card">
                    <img src="../<?php echo $rows['image']; ?>" alt="Product">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $rows['name']; ?></h5>
                        <p class="card-text"><?php echo $rows['description']; ?></p>
                        <p class="price">PKR <?php echo $rows['price']; ?></p>
                        <a href="editpro.php?id=<?php echo $rows['id']; ?>" class="btn-edit">Edit</a>
                        <a href="deletepro.php?id=<?php echo $rows['id']; ?>" class="btn-delete">Delete</a>
                    </div>
                </div>
            <?php }
        } else {
            echo "<p class='no-products'>No products found in this category.</p>";
        }
        ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
