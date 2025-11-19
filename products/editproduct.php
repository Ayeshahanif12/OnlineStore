<?php
include '../config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    die("Invalid Product ID.");
}

// Fetch categories for the dropdown - Moved this down to ensure it's within the logical flow
// and after the main product fetch.
$categories_query = "SELECT * FROM nav_categories";
$categories_result = mysqli_query($conn, $categories_query);
// Fetch product data
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$product_result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($product_result);

if (!$product) {
    die("Product not found.");
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $image_id_to_delete = (int)$_GET['delete_image'];
    $img_delete_stmt = mysqli_prepare($conn, "DELETE FROM product_images WHERE id = ? AND product_id = ?");
    mysqli_stmt_bind_param($img_delete_stmt, "ii", $image_id_to_delete, $id);
    mysqli_stmt_execute($img_delete_stmt);
    // Redirect to avoid re-deletion on refresh
    header("Location: editproduct.php?id=" . $id);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $code = $_POST['code']; // Get code from form
    $price = $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? $_POST['sale_price'] : 0.00;
    $category_id = $_POST['category_id'];
    $stock_status = $_POST['stock_status'];

    // Handle new image uploads
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $target_dir = "../uploads/";
        foreach ($_FILES['images']['name'] as $key => $image_name) {
            $target_file = $target_dir . basename($image_name);
            $db_image_path = "uploads/" . basename($image_name);

            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                // Insert new image into product_images table
                $img_stmt = mysqli_prepare($conn, "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                mysqli_stmt_bind_param($img_stmt, "is", $id, $db_image_path);
                mysqli_stmt_execute($img_stmt);
            }
        }
    }

    // Update featured image if a new one is selected
    if (isset($_POST['featured_image'])) {
        mysqli_query($conn, "UPDATE products SET featured_image = '" . mysqli_real_escape_string($conn, $_POST['featured_image']) . "' WHERE id = $id");
    }

    $update_stmt = mysqli_prepare($conn, "UPDATE products SET name=?, description=?, code=?, price=?, sale_price=?, category_id=?, stock_status=? WHERE id=?");
    mysqli_stmt_bind_param($update_stmt, "sssddisi", $name, $description, $code, $price, $sale_price, $category_id, $stock_status, $id);
    if (mysqli_stmt_execute($update_stmt)) {
        echo "<script>alert('Product updated successfully!'); window.location.href='product.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($update_stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { display: flex; background-color: #f8f9fa; }
        .sidebar { width: 280px; height: 100vh; background-color: #212529; color: white; position: fixed; top: 0; left: 0; }
        .sidebar .nav-link { color: white; }
        .sidebar .nav-link.active { background-color: #0d6efd; }
        .main-content { margin-left: 280px; flex-grow: 1; padding: 20px; }
        .form-section { background: #fff; padding: 20px; border-radius: 8px; }
        .image-gallery { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px; }
        .image-item { position: relative; border: 1px solid #ddd; padding: 5px; border-radius: 5px; }
        .image-item img { width: 100px; height: 100px; object-fit: cover; }
        .image-item .delete-btn { position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; line-height: 20px; text-align: center; cursor: pointer; }
        .image-item .featured-radio { position: absolute; bottom: 5px; left: 5px; }
    </style>
</head>
<body>  
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3">
        <a href="<?php echo BASE_URL; ?>/adminpanel/adminpage.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Admin</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item"><a href="<?php echo BASE_URL; ?>/adminpanel/adminpage.php" class="nav-link text-white"><i class="bi bi-house-door-fill me-2"></i> Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>/adminpanel/dashboard.php" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>/adminpanel/order.php" class="nav-link text-white"><i class="bi bi-table me-2"></i> Orders</a></li>
            <li><a href="<?php echo BASE_URL; ?>/products/product.php" class="nav-link active"><i class="bi bi-grid me-2"></i> Products</a></li>
            <li><a href="<?php echo BASE_URL; ?>/adminpanel/user.php" class="nav-link text-white"><i class="bi bi-people me-2"></i> Customers</a></li>
            <li><a href="<?php echo BASE_URL; ?>/adminpanel/category.php" class="nav-link text-white"><i class="bi bi-tags me-2"></i> Categories</a></li>
            <li><a href="<?php echo BASE_URL; ?>/newsletter/fetchnewsletter.php" class="nav-link text-white"><i class="bi bi-envelope me-2"></i> Newsletter</a></li>
            <li><a href="<?php echo BASE_URL; ?>/contactus/fetchmessages.php" class="nav-link text-white"><i class="bi bi-telephone me-2"></i> Contact Us</a></li>
        </ul>
    </div>
        <!-- Main Content -->
        <div class="main-content">
            <div class="form-section">
                <h3>Edit Product</h3>
                <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($product['code'] ?? '') ?>">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price (PKR)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sale_price" class="form-label">Sale Price (Optional)</label>
                        <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price" value="<?= htmlspecialchars($product['sale_price']) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <?php 
                            if ($categories_result) {
                                while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endwhile; 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock_status" class="form-label">Stock Status</label>
                        <select class="form-select" id="stock_status" name="stock_status">
                            <option value="in_stock" <?= ($product['stock_status'] == 'in_stock') ? 'selected' : '' ?>>In Stock</option>
                            <option value="out_of_stock" <?= ($product['stock_status'] == 'out_of_stock') ? 'selected' : '' ?>>Out of Stock</option>
                        </select>
                    </div>
                </div>
                
                <!-- Image Management -->
                <div class="mb-3">
                    <label class="form-label">Manage Images</label>
                    <div class="image-gallery">
                        <?php
                        $images_result = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id = $id");
                        if ($images_result) {
                            while ($img = mysqli_fetch_assoc($images_result)):
                        ?>
                        <div class="image-item">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($img['image_path']) ?>" alt="Product Image">
                            <a href="editproduct.php?id=<?= $id ?>&delete_image=<?= $img['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this image?')">&times;</a>
                            <input type="radio" name="featured_image" value="<?= htmlspecialchars($img['image_path']) ?>" class="featured-radio" <?= ($product['featured_image'] == $img['image_path']) ? 'checked' : '' ?>>
                        </div>
                        <?php endwhile; } ?>
                    </div>
                    <small class="form-text text-muted">Select a radio button to set the featured image.</small>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">Add More Images (Optional)</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple>
                </div>

                <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                 <a href="product.php" class="btn btn-secondary mt-2">Cancel</a>
            </form>
            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>