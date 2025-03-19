<?php
include 'db_connect.php';
include 'functions.php';

// Get total signup count
$tsql = 'SELECT COUNT(DISTINCT Email) FROM dailyweborders';
$stmt = $pdo->prepare($tsql);
$stmt->execute();
$totalSignups = $stmt->fetchColumn();

// Get total orders count per year
$orderYearSql = 'SELECT DISTINCT YEAR(OrderDate) AS order_year, COUNT(DISTINCT Email) AS order_count FROM dailyweborders GROUP BY YEAR(OrderDate)';
$stmt = $pdo->prepare($orderYearSql);
$stmt->execute();
$ordersByYear = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .card table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .card th, .card td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .card th {
            background-color: #f4f4f4;
        }
    </style>
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
                <div class="card">
                    <h3>Orders By Year</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Order Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ordersByYear as $data): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($data['order_year']); ?></td>
                                    <td><?php echo htmlspecialchars($data['order_count']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
