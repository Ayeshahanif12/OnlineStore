<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
 <!-- Bootstrap CSS -->
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

        table {
            width: 100%;
            margin-top: 20px;
            margin-left: 20px;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }
        a {
            text-decoration: none;
            color: white;

            padding: 5px 10px;
            
        }
        
        #edit-btn {
            background-color: #007bff; /* Blue */
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
             background-color: #28a745; /* Green */
             color: white;
             text-decoration: none;
             padding: 5px 10px;
             border-radius: 4px;
             display: inline-block;
            }

            #delete-btn:hover {
              background-color: #1e7e34;
            }

    </style>
</head>
<body>
   
  <!-- Sidebar -->
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
        <li><a class="dropdown-item" href="http://localhost/clothing%20store/login.php">Sign out</a></li>
      </ul>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Main Content -->
        <main class="main-section">
            <h2 style="margin-left: 20px;">Contact Us Messages</h2>

            <?php
            $conn = mysqli_connect("localhost", "root", "", "clothing_store");
            if (!$conn) {
                die("Database connection failed: " . mysqli_connect_error());
            }

            $select = "SELECT * FROM contact_us";
            $result = mysqli_query($conn, $select);
            $dataAll = [];

            if (mysqli_num_rows($result) > 0) {
                while ($rows = mysqli_fetch_assoc($result)) {
                    $dataAll[] = $rows;
                }
            }
            ?>

            <table>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th colspan="2">Actions</th>
                </tr>

                <?php foreach ($dataAll as $rows): ?>
                    <tr>
                  <td><?= $rows['id'] ?></td>
                  <td><?= $rows['name'] ?></td>
                  <td><?= $rows['email'] ?></td>
                  <td><?= $rows['message'] ?></td>
                  <td><a href="editmess.php?id=<?= $rows['id'] ?>" id="edit-btn">Edit</a></td>
                  <td><a href="deletemess.php?id=<?= $rows['id'] ?>" id="delete-btn" >Delete</a></td>
                 </tr>

                <?php endforeach; ?>
            </table>
        </main>
    </div>
</body>
</html>
