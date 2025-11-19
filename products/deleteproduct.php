<?php
include '../config.php';

if (!isset($_GET['id'])) {
    header("Location: product.php?error=missing_id");
    exit();
}

$product_id = (int)$_GET['id'];

// Start a transaction to ensure all or nothing is deleted
mysqli_begin_transaction($conn);

try {
    // 1. Get all image paths for the product to delete the files
    $image_paths = [];
    $stmt_get_images = mysqli_prepare($conn, "SELECT image_path FROM product_images WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt_get_images, "i", $product_id);
    mysqli_stmt_execute($stmt_get_images);
    $result = mysqli_stmt_get_result($stmt_get_images);
    while ($row = mysqli_fetch_assoc($result)) {
        $image_paths[] = $row['image_path'];
    }
    mysqli_stmt_close($stmt_get_images);

    // 2. Delete images from the product_images table
    $stmt_delete_images = mysqli_prepare($conn, "DELETE FROM product_images WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt_delete_images, "i", $product_id);
    mysqli_stmt_execute($stmt_delete_images);
    mysqli_stmt_close($stmt_delete_images);

    // 3. Delete the product from the products table
    // This will also delete related images from product_images if you set up the foreign key with ON DELETE CASCADE
    $stmt_delete_product = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt_delete_product, "i", $product_id);
    mysqli_stmt_execute($stmt_delete_product);
    mysqli_stmt_close($stmt_delete_product);

    // 4. Delete the actual image files from the server
    foreach ($image_paths as $path) {
        $full_path = realpath(__DIR__ . '/../' . $path);
        if ($full_path && file_exists($full_path)) {
            unlink($full_path);
        }
    }

    // If everything was successful, commit the transaction
    mysqli_commit($conn);
    echo "<script>alert('Product deleted successfully!'); window.location.href='product.php';</script>";

} catch (mysqli_sql_exception $exception) {
    mysqli_rollback($conn); // Rollback on error
    echo "Error deleting product: " . $exception->getMessage();
}

exit();
?>