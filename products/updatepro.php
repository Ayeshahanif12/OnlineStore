<?php
$conn = mysqli_connect("localhost", "root", "", "clothing_store");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get values from POST
$id = $_POST['id'];  // Make sure you send `id` from the form
$image = mysqli_real_escape_string($conn, $_POST['image']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$price = mysqli_real_escape_string($conn, $_POST['price']);

// Update query
$sql = "UPDATE products 
        SET image = '$image', name = '$name', description = '$description', price = '$price' 
        WHERE id = '$id'";

if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('Record updated successfully');
            window.location.href='product.php';
          </script>";
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
