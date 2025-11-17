<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $cat_name = $_POST['category_name'];

  if (isset($_FILES['image'])) {
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $target_path = "image/" . $image_name;

    $stmt = mysqli_prepare($conn, "INSERT INTO nav_categories (name, img) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $cat_name, $image_name);

    if (mysqli_stmt_execute($stmt)) {
      move_uploaded_file($image_tmp, "../" . $target_path); // Make sure the path is correct
      echo "<script>alert('Category added successfully!'); window.location.href='category.php';</script>";
    } else {
      echo "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
  }
}
?>
<!DOCTYPE html>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    overflow-x: hidden;

  }

  /* Sidebar */
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
    color: #ccc;
    padding: 10px 15px;
    border-radius: 6px;
    transition: all 0.3s;
  }

  .sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
  }

  .sidebar .nav-link.active {
    background-color: #0d6efd;
    color: white;
    font-weight: bold;
  }

  /* Form Container */
  form {
    margin-left: 250px;
    padding: 20px;
    width: 100%;
  }

  form h3 {
    color: black;
    margin-left: 400px;
    font-size: 24px;
    margin-bottom: 20px;
  }

  label {
    font-weight: bold;
    color: #333;
    margin-bottom: 6px;
    display: block;
  }

  input[type="text"],
  input[type="file"] {
    width: 40%;
    padding: 10px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    transition: border 0.3s;
  }

  input[type="text"]:focus,
  input[type="file"]:focus {
    border-color: #0d6efd;
    outline: none;
  }

  input[type="submit"] {
    background: #0d6efd;
    color: white;
    border: none;
    padding: 12px;
    font-size: 16px;
    border-radius: 6px;
    margin-left: 450px;
    cursor: pointer;
    transition: background 0.3s;
  }

  input[type="submit"]:hover {
    background: #0056b3;
  }

  table {
    width: 60%;
    border-collapse: collapse;
    background: #fff;
    margin-left: 300px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    overflow: hidden;
  }

  table th,
  table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
  }

  table th {
    background: #0d6efd;
    color: white;
  }

  table tr:hover {
    background-color: #f1f5ff;
  }

  table img {
    width: 60px;
    height: auto;
    border-radius: 5px;
  }

  table a {
    text-decoration: none;
    color: #0d6efd;
    font-weight: bold;
  }

  table a:hover {
    color: #0056b3;
  }

  #edit-btn {
    background-color: #007bff;
    /* Blue */
    color: white;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 4px;
    display: inline-block;
  }

  #edit-btn:hover {
    background-color: #0056b3;
  }

  #delete-btn {
    background-color: #28a745;
    /* Green */
    color: white;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 4px;
    display: inline-block;
  }

  #delete-btn:hover {
    background-color: #1e7e34;
  }

  .box {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-left: 290px;
    margin-bottom: 20px;
  }
</style>

<body>

  <!-- Sidebar -->
  <div class="sidebar d-flex flex-column p-3">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-4">Admin</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/adminpanel/adminpage.php" class="nav-link">
          <i class="bi bi-house-door-fill me-2"></i> Home
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/adminpanel/dashboard.php" class="nav-link">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/adminpanel/order.php" class="nav-link">
          <i class="bi bi-table me-2"></i> Orders
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/products/product.php" class="nav-link">
          <i class="bi bi-grid me-2"></i> Products
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/adminpanel/user.php" class="nav-link">
          <i class="bi bi-people me-2"></i> Customers
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/adminpanel/category.php" class="nav-link active">
          <i class="bi bi-tags me-2"></i> Categories
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/newsletter/fetchnewsletter.php" class="nav-link">
          <i class="bi bi-envelope me-2"></i> Newsletter
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/contactus/fetchmessages.php" class="nav-link">
          <i class="bi bi-telephone me-2"></i> Contact Us
        </a>
      </li>
    </ul>
    <hr>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
        data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
        <strong>Admin</strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li><a class="dropdown-item" href="#">New project...</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/logout.php">Sign out</a></li>
      </ul>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <form method="POST" enctype="multipart/form-data">
    <h3>Add New Category</h3>
    <div class="box">
      <label for="image">Image</label>
      <input type="file" name="image" id="image" class="form-control mb-2" required>
      <label for="category_name">Category Name</label>
      <input type="text" name="category_name" id="category_name" class="form-control mb-2" required>
    </div>
    <input type="submit" name="add_category" value="Add Category" class="btn-primary">
    </div>
  </form>

  <table border=1>
    <tr>
      <th>Category Name</th>
      <th>Image</th>
      <th>Edit</th>
      <th>Delete</th>
    </tr>
    <?php
    $fcat = mysqli_query($conn, "SELECT * FROM nav_categories");
    while ($cat = mysqli_fetch_assoc($fcat)) { ?>
      <tr>
        <td><?php echo $cat['name']; ?></td>
        <td><img src="image/<?php echo $cat['img']; ?>" alt="Product"></td>

        <td><a href="editcat.php?id=<?php echo $cat['id']; ?>">Edit</a></td>
        <td><a href="deletecat.php?id=<?php echo $cat['id']; ?>">Delete</a></td>
      </tr>
    <?php } ?>
  </table>