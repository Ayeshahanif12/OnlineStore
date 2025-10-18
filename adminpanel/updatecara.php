<?php
require_once '../db_config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $alt_text = $_POST['alt_text'];
    $new_image = $_FILES['image']['name'];
    $update_filename = '';

    // 1. Get old image name from database using prepared statement
    $stmt = mysqli_prepare($conn, "SELECT img FROM carousel WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $old_image_row = mysqli_fetch_assoc($result);
    $old_image = $old_image_row['img'];
    mysqli_stmt_close($stmt);

    // 2. Check if a new image is uploaded
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
            // Redirect with an error message if upload fails
            header("Location: adminpage.php?error=upload_failed");
            exit();
        }
    } else {
        // No new image uploaded, keep the old one
        $update_filename = $old_image;
    }

    // 3. Update the database using prepared statement
    $sql = "UPDATE carousel SET img = ?, alt_text = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $update_filename, $alt_text, $id);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect with a success message
        header("Location: adminpage.php?success=updated");
    } else {
        // Redirect with a generic error message
        header("Location: adminpage.php?error=update_failed");
    }
    mysqli_stmt_close($stmt);
}
?>