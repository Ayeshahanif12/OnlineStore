<?php

$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}




$term = $_GET['term'] ?? '';

if ($term != '') {
    $term = mysqli_real_escape_string($conn, $term);
    $query = "SELECT id, name, image FROM products WHERE name LIKE '$term%' LIMIT 5";
    $result = mysqli_query($conn, $query);

    $suggestions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = $row;
    }

    echo json_encode($suggestions);
}


?>