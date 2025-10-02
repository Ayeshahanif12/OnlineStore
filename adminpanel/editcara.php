<?php
$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$id = $_GET['id'];
$sql = "SELECT * FROM carousel WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$Carousel_image = $row['img'];
$alt_text = $row['alt_text'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit carousel</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> 

  <style>
     body {
      min-height: 100vh;
      display: flex;
    }
    .sidebar {
      width: 280px;
      height: 100vh;
      background-color: #212529;
      color: white;
    }
    .sidebar .nav-link {
      color: white;
    }
    .sidebar .nav-link.active {
      background-color: #0d6efd;
    }

   
    .main-section {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }

    form {
      display: flex;
      flex-direction: column;
      justify-content: center;
      max-width: 400px;
      width: 100%;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      background: #fff;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 30px;
      font-weight: 800;
    }

    input {
      width: 100%;
      padding: 14px;
      margin-bottom: 25px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }

    button {
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
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
        <a href="http://localhost/store/adminpanel/adminpage.php" class="nav-link active">
          <i class="bi bi-house-door-fill me-2"></i> Home
        </a>
      </li>
      <li>
        <a href="http://localhost/store/adminpanel/dashboard.php" class="nav-link">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="http://localhost/store/adminpanel/order.php" class="nav-link">
          <i class="bi bi-table me-2"></i> Orders
        </a>
      </li>
      <li>
        <a href="http://localhost/store/products/product.php" class="nav-link">
          <i class="bi bi-grid me-2"></i> Products
        </a>
      </li>
      <li>
        <a href="http://localhost/store/adminpanel/user.php" class="nav-link">
          <i class="bi bi-people me-2"></i> Customers
        </a>
      </li>
         <li>
        <a href="http://localhost/store/adminpanel/category.php" class="nav-link">
          <i class="bi bi-tags me-2"></i> Categories
        </a>
      </li>
      <li>
        <a href="http://localhost/store/newsletter/fetchnewsletter.php" class="nav-link">
          <i class="bi bi-envelope me-2"></i> Newsletter
        </a>
      </li>
      <li>
        <a href="http://localhost/store/contactus/fetchmessages.php" class="nav-link">
          <i class="bi bi-telephone me-2"></i> Contact Us
        </a>
      </li>
    </ul>
    <hr>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
        <strong>Admin</strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li><a class="dropdown-item" href="#">New project...</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="http://localhost/store/login.php">Sign out</a></li>
      </ul>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Main Section -->
    <main class="main-section">
      <form action="updatecara.php" method="post" enctype="multipart/form-data">
        <h2>Edit Carousel</h2>
        <input type="hidden" name="id" value="<?= $id ?>">
        <label>Current Image:</label>
        <img src="image/<?= htmlspecialchars($Carousel_image) ?>" alt="<?= htmlspecialchars($alt_text) ?>" width="150" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
        
        <label for="image">Change Image (optional):</label>
        <input type="file" name="image" id="image">
        <input type="text" name="alt_text" value="<?= $alt_text ?>" required>
        <button type="submit">Update</button>
      </form>
    </main>
  </div>
</body>
</html>
