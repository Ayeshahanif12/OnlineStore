<?php
require_once '../db_config.php';
$id = $_GET['id'];

    $delete = "DELETE FROM carousel WHERE id='$id'";
    if (mysqli_query($conn, $delete)) {
        echo "<script>alert('Carousel deleted successfully!');</script>";
    } else {
        echo "Error: " . $delete . "<br>" . mysqli_error($conn);
    }
header("Location: adminpage.php");
?>