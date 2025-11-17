<?php
include '../config.php';

$id = (int)$_GET['id'];

$stmt = mysqli_prepare($conn, "DELETE FROM carousel WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Carousel deleted successfully!');</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
mysqli_stmt_close($stmt);
header("Location: adminpage.php");
?>