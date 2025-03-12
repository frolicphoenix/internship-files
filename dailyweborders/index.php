<?php
include 'db_connect.php';
include 'functions.php';

// Search term from query string
$searchTerm = getSearchTerm();

// Get total signup count
$sql = 'SELECT COUNT(DISTINCT Email) FROM dailyweborders';
if (!empty($searchTerm)) {
    $sql .= " WHERE Email LIKE ?";
}
$stmt = $pdo->prepare($sql);
if (!empty($searchTerm)) {
    $stmt->execute(["%" . $searchTerm . "%"]);
} else {
    $stmt->execute();
}

$totalSignups = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Orders</a></li>
                <li><a href="signup_list.php">Customers</a></li>
                <li><a href="#">Reports</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <h1>Welcome to the Dashboard</h1>
            </header>

            <!-- Summary Cards -->
            <section class="summary-cards">
                <div class="card">
                    <h3>Total Signups</h3>
                    <p><?php echo $totalSignups; ?></p>
                </div>
                <!-- <div class="card">
                    <h3>Total Revenue</h3>
                    <p></p> 
                </div>
                <div class="card">
                    <h3>Total Orders</h3>
                    <p>279</p> 
                </div>
                <div class="card">
                    <h3>Total Customers</h3>
                    <p>65</p> 
                </div> -->
            </section>
<!-- 
            <section class="orders-summary">
                <h2>Orders Summary</h2>
            </section>

            <section class="revenue-section">
                <h2>Revenue Overview</h2>
            </section> -->
        </main>
    </div>
</body>
</html>
