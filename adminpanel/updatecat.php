<?php
require_once '../db_config.php';
$id = $_POST['id'];
$name = $_POST['category_name'];
$new_image = $_FILES['image']['name'];

// Get old image name from database
$old_image_query = mysqli_query($conn, "SELECT img FROM nav_categories WHERE id='$id'");
$old_image_row = mysqli_fetch_assoc($old_image_query);
$old_image = $old_image_row['img'];

// Check if a new image is uploaded
if ($new_image != '') {
    // A new image is uploaded, so we process it
    $update_filename = $new_image;
    $target_dir = "image/";
    $target_file = $target_dir . basename($update_filename);

    // Move the uploaded file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Optional: Delete the old image file from the server
        if ($old_image != '' && $old_image != $update_filename && file_exists($target_dir . $old_image)) {
            // unlink($target_dir . $old_image); // Uncomment to delete old image file
        }
    } else {
        echo "<script>alert('Sorry, there was an error uploading your new image.');</script>";
        header("Location: category.php");
        exit();
    }
} else {
    // No new image uploaded, keep the old one
    $update_filename = $old_image;
}

// Update the database record
$sql = "UPDATE nav_categories SET name = '$name', img='$update_filename' WHERE id = '$id'";
if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Record updated successfully');</script>";
    header("Location: category.php");
} else {
    echo "Error updating record: " . mysqli_error($conn);
}



?>