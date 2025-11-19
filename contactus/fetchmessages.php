<?php
include '../config.php'; // Assuming contactus folder is directly under OnlineStore, so ../config.php

// Optional: Add admin role check here if you want to restrict access
// if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
//   header("Location: " . BASE_URL . "/login.php");
//   exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us Messages</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 280px;
            height: 100vh;
            background-color: #212529;
            color: white;
            flex-shrink: 0;
        }
        .main-section {
            flex-grow: 1;
            padding: 20px;
            overflow-x: hidden;
        }
        .sidebar .nav-link {
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
        }
        .table-responsive-wrapper {
            overflow-x: auto;
            margin-top: 20px;
        }
        .table-responsive-wrapper table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }
        td a {
            text-decoration: none;
            padding: 5px 10px;
        }
        #delete-btn {
            background-color: #dc3545; /* Red for delete */
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }
        #delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column p-3">
        <a href="<?php echo BASE_URL; ?>/adminpanel/adminpage.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
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
                <a href="<?php echo BASE_URL; ?>/adminpanel/category.php" class="nav-link">
                    <i class="bi bi-tags me-2"></i> Categories
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/newsletter/fetchnewsletter.php" class="nav-link">
                    <i class="bi bi-envelope me-2"></i> Newsletter
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/contactus/fetchmessages.php" class="nav-link active">
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
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/login.php">Sign out</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-section">
        <h2>Contact Us Messages</h2>
        
        <?php
        $select = "SELECT * FROM contact_us";
        $result = mysqli_query($conn, $select);
        $dataAll = [];

        if ($result && mysqli_num_rows($result) > 0) {
            while ($rows = mysqli_fetch_assoc($result)) {
                $dataAll[] = $rows;
            }
        }
        ?>

        <div class="table-responsive-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataAll)): ?>
                        <?php foreach ($dataAll as $rows): ?>
                            <tr>
                                <td><?= htmlspecialchars($rows['id']) ?></td>
                                <td><?= htmlspecialchars($rows['name']) ?></td>
                                <td><?= htmlspecialchars($rows['email']) ?></td>
                                <td><?= htmlspecialchars($rows['message']) ?></td>
                                <td><a href="deletecontact.php?id=<?= $rows['id'] ?>" id="delete-btn">Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No messages found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>