<?php
// DB Connection
$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Monthly Revenue + Sales Query
$monthlyRevenue = [];
$monthlySales = [];

$query = "
  SELECT 
    MONTH(c.created_at) as month, 
    SUM(oi.total) as revenue,
    COUNT(oi.id) as sales
  FROM order_items oi
  JOIN checkout c ON oi.order_id = c.id
  GROUP BY MONTH(c.created_at)
  ORDER BY MONTH(c.created_at)
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $monthNum = $row['month'];
    $monthName = date("M", mktime(0, 0, 0, $monthNum, 10)); // Jan, Feb, etc.
    $monthlyRevenue[$monthName] = $row['revenue'];
    $monthlySales[$monthName] = $row['sales'];
}

// Convert arrays to JSON for Chart.js
$labels = json_encode(array_keys($monthlyRevenue));
$revenueData = json_encode(array_values($monthlyRevenue));
$salesData = json_encode(array_values($monthlySales));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
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

        .dashboard {
            max-width: 1200px;
            margin: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #0d47a1;
        }

        .charts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        canvas {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    !-- Sidebar -->
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
                <li><a class="dropdown-item" href="http://localhost/clothing%20store/login.php">Sign out</a></li>
            </ul>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <form method="POST" enctype="multipart/form-data">
        <div class="dashboard">
            <h2>E-commerce Overview</h2>

            <div class="charts">
                <canvas id="revenueChart"></canvas>
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <script>
            const labels = <?= $labels ?>;
            const revenueData = <?= $revenueData ?>;
            const salesData = <?= $salesData ?>;

            // Line Chart: Revenue
            const ctx1 = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue ($)',
                        data: revenueData,
                        backgroundColor: 'rgba(13,71,161,0.2)',
                        borderColor: '#0d47a1',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });

            // Bar Chart: Sales vs Revenue
            const ctx2 = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Sales',
                            data: salesData,
                            backgroundColor: '#0d47a1'
                        },
                        {
                            label: 'Revenue ($)',
                            data: revenueData,
                            backgroundColor: '#90caf9'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        </script>
</body>

</html>