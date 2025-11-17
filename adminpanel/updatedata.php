<?php
include '../config.php';
$id = $_POST['id'];
$Fname = $_POST['Fname'];       
$Lname = $_POST['Lname'];
$email = $_POST['email'];
$password = $_POST['password'];

$sql = "UPDATE users SET fname = '$Fname', lname ='$Lname', email='$email', password='$password' WHERE id = '$id'";
if(mysqli_query($conn,$sql)){
    echo"<script>alert('Record updated successfully');</script>";
    header("Location: user.php");
}
else{
    echo "Error updating record: " . mysqli_error($conn);
}



?>