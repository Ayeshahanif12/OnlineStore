<?php
// DB Connection
$conn = mysqli_connect("localhost", "root", "", "clothing_store");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 🔹 Total Orders
$totalOrders = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM checkout");
if ($row = mysqli_fetch_assoc($res)) {
    $totalOrders = $row['total'];
}

// 🔹 Total Revenue
$totalRevenue = 0;
$res = mysqli_query($conn, "SELECT SUM(total) as revenue FROM order_items");
if ($row = mysqli_fetch_assoc($res)) {
    $totalRevenue = $row['revenue'];
}
$totalIncome = $totalRevenue; 

// 🔹 Total Users (Activity)
$totalActivity = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) as users FROM users");
if ($row = mysqli_fetch_assoc($res)) {
    $totalActivity = $row['users'];
}

// 🔹 Monthly Revenue + Sales for Chart
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
    $monthName = date("M", mktime(0, 0, 0, $monthNum, 10));
    $monthlyRevenue[$monthName] = $row['revenue'];
    $monthlySales[$monthName] = $row['sales'];
}
$labels = json_encode(array_keys($monthlyRevenue));
$revenueData = json_encode(array_values($monthlyRevenue));
$salesData = json_encode(array_values($monthlySales));

// 🔹 Top Selling Products from DB
$products = [];
$res = mysqli_query($conn, "
    SELECT 
        p.name, 
        COUNT(oi.id) as sales, 
        SUM(oi.total) as revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY oi.product_id
    ORDER BY sales DESC
    LIMIT 6
");
while ($row = mysqli_fetch_assoc($res)) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; margin:0; }
    .sidebar {
      width: 250px;
      background: #212529;
      color: white;
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      padding: 20px 0;
    }
    .dashboard {
      margin-left: 260px;
      padding: 20px;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
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
              <a href="http://localhost/clothing%20store/adminpanel/adminpage.php" class="nav-link active"> Home </a>
          </li>
          <li><a href="http://localhost/clothing%20store/adminpanel/dashboard.php" class="nav-link"> Dashboard </a></li>
          <li><a href="http://localhost/clothing%20store/adminpanel/order.php" class="nav-link"> Orders </a></li>
          <li><a href="http://localhost/clothing%20store/products/product.php" class="nav-link"> Products </a></li>
          <li><a href="http://localhost/clothing%20store/adminpanel/user.php" class="nav-link"> Customers </a></li>
          <li><a href="http://localhost/clothing%20store/adminpanel/category.php" class="nav-link"> Categories </a></li>
          <li><a href="http://localhost/clothing%20store/newsletter/fetchnewsletter.php" class="nav-link"> Newsletter </a></li>
          <li><a href="http://localhost/clothing%20store/contactus/fetchmessages.php" class="nav-link"> Contact Us </a></li>
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
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="http://localhost/clothing%20store/login.php">Sign out</a></li>
          </ul>
      </div>
  </div>

  <!-- Dashboard -->
  <div class="dashboard">
    <h2>E-commerce Overview</h2>

    <!-- Cards -->
    <div class="row mb-4">
      <div class="col-md-3"><div class="card p-3"><h6>Income</h6><h3>$<?= $totalIncome ?></h3></div></div>
      <div class="col-md-3"><div class="card p-3"><h6>Orders</h6><h3><?= $totalOrders ?></h3></div></div>
      <div class="col-md-3"><div class="card p-3"><h6>Activity</h6><h3><?= $totalActivity ?></h3></div></div>
      <div class="col-md-3"><div class="card p-3"><h6>Revenue</h6><h3>$<?= $totalRevenue ?></h3></div></div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
      <div class="col-md-6"><div class="card p-3"><canvas id="revenueChart"></canvas></div></div>
      <div class="col-md-6"><div class="card p-3"><canvas id="salesChart"></canvas></div></div>
    </div>

    <!-- Top Selling Products Table -->
    <div class="card p-3">
      <h5>Top Selling Products</h5>
      <table class="table table-striped">
        <thead><tr><th>Name</th><th>Sales</th><th>Revenue</th></tr></thead>
        <tbody>
          <?php foreach($products as $p): ?>
          <tr>
            <td><?= $p["name"] ?></td>
            <td><?= $p["sales"] ?></td>
            <td>$<?= $p["revenue"] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Chart.js -->
  <script>
    const labels = <?= $labels ?>;
    const revenueData = <?= $revenueData ?>;
    const salesData = <?= $salesData ?>;

    new Chart(document.getElementById('revenueChart'), {
      type: 'line',
      data: { labels: labels, datasets: [{ label:'Revenue ($)', data: revenueData, borderColor:'#0d47a1', backgroundColor:'rgba(13,71,161,0.2)', fill:true }] },
      options: { responsive:true, plugins:{ legend:{ position:'top' } } }
    });

    new Chart(document.getElementById('salesChart'), {
      type: 'bar',
      data: { labels: labels, datasets: [{ label:'Sales', data: salesData, backgroundColor:'#0d47a1' }, { label:'Revenue ($)', data: revenueData, backgroundColor:'#90caf9' }] },
      options: { responsive:true, plugins:{ legend:{ position:'top' } }, scales:{ y:{ beginAtZero:true } } }
    });
  </script>
</body>
</html>
