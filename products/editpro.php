<?php
$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$image = $row['image'];
$name = $row['name'];
$description = $row['description'];
$price = $row['price'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <title>Edit Product</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> 
  <style>
      
/* General Page Styling */
body {
    display: flex;
    margin: 0;
    font-family: Arial, sans-serif;
}
.sidebar {
    width: 250px;
    background: #212529;
    color: white;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding: 20px 0;
}
.sidebar .nav-link {
    color: white;
}
.sidebar .nav-link.active {
    background-color: #0d6efd;
}

.sidebar hr {
  border-color: #444;
}

.sidebar .dropdown-menu {
  background-color: #2a2a2a;
}

/* Main Form Section */
.main-section {
  margin-left: 300px; /* space for sidebar */
  padding: 25px 30px;
  border-radius: 10px;
  max-width: 450px;
  width: 100%;
  margin-top: 40px;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #212529;
}

/* Form Inputs */
label {
  display: block;
  margin-top: 10px;
  font-weight: bold;
  color:black;
}

input[type="text"],
input[type="file"] {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  background: #ffffff;
  border: 1px solid #444;
  border-radius: 5px;
  color: black;
  outline: none;
}

input[type="text"]:focus,
input[type="file"]:focus {
  border-color: #0d6efd;
}

img {
  display: block;
  margin: 10px auto;
  max-width: 100px;
  border-radius: 5px;
}

/* Button */
button {
  width: 100%;
  padding: 10px;
  margin-top: 15px;
  border: none;
  background: #0d6efd;
  color: white;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s;
}

button:hover {
  background: #0b5ed7;
}

  </style>
</head>
<body>

  <div class="sidebar d-flex flex-column p-3">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-4">Admin</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="http://localhost/clothing%20store/adminpanel/adminpage.php" class="nav-link active">
          <i class="bi bi-house-door-fill me-2"></i> Home
        </a>
      </li>
      <li>
        <a href="http://localhost/clothing%20store/adminpanel/dashboard.php" class="nav-link">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="http://localhost/clothing%20store/adminpanel/order.php" class="nav-link">
          <i class="bi bi-table me-2"></i> Orders
        </a>
      </li>
      <li>
        <a href="http://localhost/clothing%20store/products/product.php" class="nav-link">
          <i class="bi bi-grid me-2"></i> Products
        </a>
      </li>
      <li>
        <a href="http://localhost/clothing%20store/adminpanel/user.php" class="nav-link">
          <i class="bi bi-people me-2"></i> Customers
        </a>
      </li>
         <li>
        <a href="http://localhost/clothing%20store/adminpanel/category.php" class="nav-link">
          <i class="bi bi-tags me-2"></i> Categories
        </a>
      </li>
      <li>
        <a href="http://localhost/clothing%20store/newsletter/fetchnewsletter.php" class="nav-link">
          <i class="bi bi-envelope me-2"></i> Newsletter
        </a>
      </li>
      <li>
        <a href="http://localhost/clothing%20store/contactus/fetchmessages.php" class="nav-link">
          <i class="bi bi-telephone me-2"></i> Contact Us
         </a>
      </li>
    </ul>
    <hr>
    <div class="dropdown" style="display:flex;">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
        <strong>Admin</strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li><a class="dropdown-item" href="#">New project...</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="http://localhost/clothing%20store/login.php">Sign out</a></li>
      </ul>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


    <main class="main-section">
      <form action="updatepro.php" method="post" enctype="multipart/form-data">
        <h2>Edit Product</h2>
        <input type="hidden" name="id" value="<?= $id ?>">

        <label>Current Image</label>
        <img src="image/<?= $image ?>" alt="Product Image">
        <label for="image">Change Image</label>
        <input type="file" name="image">

        <label for="name">Name</label>
        <input type="text" name="name" id="name" value="<?= $name ?>" required>

        <label for="description">Description</label>
        <input type="text" name="description" id="description" value="<?= $description ?>" required>

        <label for="price">Price</label>
        <input type="text" name="price" id="price" value="<?= $price ?>" required>

        <button type="submit">Update</button>
      </form>
    </main>
</body>
</html>
