<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category</title>
</head>
<body>
    
</body>
</html>
<?php 
include '../config.php';
$id = (int)$_GET['id'];
$stmt = mysqli_prepare($conn, "DELETE FROM nav_categories WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Record deleted successfully');</script>";
    header("Location: category.php");
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
mysqli_stmt_close($stmt);
?>