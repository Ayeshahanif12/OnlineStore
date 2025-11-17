<?php
include '../config.php';
session_start();
$id = $_POST['id'];
$name = $_POST['name'];       
$email = $_POST['email'];
$messages = $_POST['message'];

$sql = "UPDATE contact_us SET name = '$name', email ='$email', message='$messages' WHERE id = '$id'";
if(mysqli_query($conn,$sql)){
    echo"<script>alert('Record updated successfully');</script>";
    header("Location: fetchmessages.php");
}
else{
    echo "Error updating record: " . mysqli_error($conn);
}



?>