<?php
            require_once '../db_config.php';  


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $new_image = $_FILES['image']['name'];

    // 1. Get old image name from database
    $stmt = mysqli_prepare($conn, "SELECT image FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $old_image_row = mysqli_fetch_assoc($result);
    $old_image = $old_image_row['image'];
    mysqli_stmt_close($stmt);

    $update_filename = '';

    // 2. Check if a new image is uploaded
    if ($new_image != '') {
        // A new image is uploaded, process it
        $target_dir = "../image/"; // Go up one level to the 'image' folder
        $target_file = $target_dir . basename($new_image);
        $db_path = "image/" . basename($new_image); // Path to store in DB

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $update_filename = $db_path;
            // Optional: Delete the old image file
            if ($old_image != '' && file_exists("../" . $old_image)) {
                // unlink("../" . $old_image);
            }
        } else {
            echo "<script>alert('Sorry, there was an error uploading your new image.'); window.location.href='product.php';</script>";
            exit();
        }
    } else {
        // No new image uploaded, keep the old one
        $update_filename = $old_image;
    }

    // 3. Update the database using prepared statement
    $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssisi", $name, $description, $price, $category_id, $update_filename, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Product updated successfully'); window.location.href='product.php';</script>";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

?>
