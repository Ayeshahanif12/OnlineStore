<?php
include '../config.php';

// Fetch categories for the dropdown
$categories_result = mysqli_query($conn, "SELECT * FROM nav_categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $code = $_POST['code']; // Get the code from the form
    $price = $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? $_POST['sale_price'] : 0.00;
    $category_id = $_POST['category_id'];
    $stock_status = $_POST['stock_status'];

    $featured_image_path = $_POST['images'];

    // Insert main product details first
    $stmt = mysqli_prepare($conn, "INSERT INTO products (name, description, code, price, sale_price, category_id, stock_status,featured_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssddis", $name, $description, $code, $price, $sale_price, $category_id, $stock_status,$featured_image_path);

    if (mysqli_stmt_execute($stmt)) {
        $product_id = mysqli_insert_id($conn);

        // Handle multiple image uploads
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $target_dir = "../uploads/";
            foreach ($_FILES['images']['name'] as $key => $image_name) {
                $target_file = $target_dir . basename($image_name);
                $db_image_path = "uploads/" . basename($image_name);

                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                    // Set the first uploaded image as the featured image
                    if ($featured_image_path === null) {
                        $featured_image_path = $db_image_path;
                        mysqli_query($conn, "UPDATE products SET featured_image = '$featured_image_path' WHERE id = $product_id");
                    }
                    // Insert into product_images table
                    $img_stmt = mysqli_prepare($conn, "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                    mysqli_stmt_bind_param($img_stmt, "is", $product_id, $db_image_path);
                    mysqli_stmt_execute($img_stmt);
                }
            }
        }
        echo "<script>alert('Product added successfully!'); window.location.href='product.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< HEAD
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { 
            width: 280px; 
            height: 100vh; 
            background-color: #212529; 
            color: white; 
            position: fixed; /* Make the sidebar fixed */
            top: 0;
            left: 0;
        }
        .sidebar .nav-link { color: white; }
        .sidebar .nav-link.active { background-color: #0d6efd; }
        .main-content { margin-left: 280px; padding: 20px; } /* Add margin to avoid overlap */
        .form-section, .table-section { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .table-responsive-wrapper { overflow-x: auto; }
        table img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
        .price-col .sale { color: red; }
        .price-col .original { text-decoration: line-through; color: grey; }
    </style>
=======
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
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3">
        <a href="<?php echo BASE_URL; ?>/adminpanel/adminpage.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Admin</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item"><a href="<?php echo BASE_URL; ?>/adminpanel/adminpage.php" class="nav-link"><i class="bi bi-house-door-fill me-2"></i> Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>/adminpanel/dashboard.php" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>/adminpanel/order.php" class="nav-link"><i class="bi bi-table me-2"></i> Orders</a></li>
            <li><a href="<?php echo BASE_URL; ?>/products/product.php" class="nav-link active"><i class="bi bi-grid me-2"></i> Products</a></li>
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
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/logout.php">Sign out</a></li>
            </ul>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Main Content -->
    <div class="main-content">
        <!-- Add Product Form -->
        <div class="form-section">
            <h3>Add New Product</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <div class="row">
                 <div class="mb-3">
                    <label for="code" class="form-label">Code</label>
                    <textarea class="form-control" id="code" name="code" rows="3"></textarea>
                </div>
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price (PKR)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sale_price" class="form-label">Sale Price (Optional)</label>
                        <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock_status" class="form-label">Stock Status</label>
                        <select class="form-select" id="stock_status" name="stock_status">
                            <option value="in_stock">In Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="images" class="form-label">Product Images (can select multiple)</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple required>
                </div>
                <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
            </form>
        </div>
=======

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
>>>>>>> 5ce6da0 (Add comprehensive styles for account settings, chat interface, and profile management)

        <!-- Products Table -->
        <div class="table-section">
            <h3>All Products</h3>
            <div class="table-responsive-wrapper">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $products_result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                        if (mysqli_num_rows($products_result) > 0):
                            while ($prod = mysqli_fetch_assoc($products_result)):
                        ?>
                        <tr>
                            <td><?= $prod['id'] ?></td>
                            <td><img src="<?= BASE_URL . '/' . htmlspecialchars($prod['featured_image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>"></td>
                            <td><?= htmlspecialchars($prod['name']) ?></td>
                            <td class="price-col">
                                <?php if ($prod['sale_price'] > 0): ?>
                                    <span class="sale">PKR <?= htmlspecialchars($prod['sale_price']) ?></span><br>
                                    <span class="original">PKR <?= htmlspecialchars($prod['price']) ?></span>
                                <?php else: ?>
                                    <span>PKR <?= htmlspecialchars($prod['price']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($prod['stock_status'] == 'in_stock'): ?>
                                    <span class="badge bg-success">In Stock</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="editproduct.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="deleteproduct.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center">No products found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
