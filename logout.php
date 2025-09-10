<?php
session_start(); // session shuru karo

// sari sessions destroy kardo
session_unset();
session_destroy();

// ab redirect karo (direct location)
header("Location: login.php"); 
exit();
?>
