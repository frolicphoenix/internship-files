<?php
require_once 'db_connect.php'; 
include 'functions.php';

$email = $_GET['email'] ?? ''; 

$sortColumn = getSortColumn();
$sortOrder = getSortOrder();

// -----------------
// Pagination Setup for Signup History
// -----------------
$signupPage = isset($_GET['signupPage']) ? (int)$_GET['signupPage'] : 1;
$signupPerPage = 6;  // number of records per page
$signupOffset = ($signupPage - 1) * $signupPerPage;

// Signup query with pagination
$signupQuery = "SELECT Name, Email, 
    CASE 
        WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%Y%m%d')
        WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%m/%d/%Y')
        ELSE NULL
    END AS OrderDateFM, 
SoldBy 
FROM dailyweborders 
WHERE Email = :email 
LIMIT :offset, :limit";
$signupStmt = $pdo->prepare($signupQuery);
$signupStmt->bindValue(':email', $email);
$signupStmt->bindValue(':offset', $signupOffset, PDO::PARAM_INT);
$signupStmt->bindValue(':limit', $signupPerPage, PDO::PARAM_INT);
$signupStmt->execute();
$signups = $signupStmt->fetchAll();

// -----------------
// Pagination Setup for Completed Orders
// -----------------
$completedPage = isset($_GET['completedPage']) ? (int)$_GET['completedPage'] : 1;
$completedPerPage = 6; // adjust as needed
$completedOffset = ($completedPage - 1) * $completedPerPage;

// Completed orders query with pagination
$completedOrdersQuery = "
    SELECT 
        Name, 
        Email,
        Price AS order_amount, 
        SoldBy, 
        CASE 
            WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%Y%m%d')
            WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%m/%d/%Y')
            ELSE NULL
        END AS OrderDateFM,
        Country,
        Address,
        City,
        ProvinceState,
        PostalZipCode,
        Phone,
        Tracking_Number
    FROM dailyweborders
    WHERE Email = :email
        AND Name != '' AND Name IS NOT NULL
        AND Country != '' AND Country IS NOT NULL
        AND Address != '' AND Address IS NOT NULL
        AND City != '' AND City IS NOT NULL
        AND ProvinceState != '' AND ProvinceState IS NOT NULL
        AND PostalZipCode != '' AND PostalZipCode IS NOT NULL
        AND Price != '' AND Price IS NOT NULL
        AND OrderDate != '' AND OrderDate IS NOT NULL
    LIMIT :offset, :limit";
$completedOrdersStmt = $pdo->prepare($completedOrdersQuery);
$completedOrdersStmt->bindValue(':email', $email);
$completedOrdersStmt->bindValue(':offset', $completedOffset, PDO::PARAM_INT);
$completedOrdersStmt->bindValue(':limit', $completedPerPage, PDO::PARAM_INT);
$completedOrdersStmt->execute();
$completedOrders = $completedOrdersStmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User History</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .pagination a {
            padding: 5px 10px;
            margin: 0 2px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
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
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="#">Orders</a></li>
            <li><a href="signup_list.php">Customers</a></li>
            <li><a href="#">Reports</a></li>
            <li><a href="#">Settings</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <h2>Signup History</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Order Date</th>
                    <th>Sold By</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($signups) > 0): ?>
                    <?php foreach ($signups as $signup): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($signup['Name']); ?></td>
                            <td><?php echo htmlspecialchars($signup['Email']); ?></td>
                            <td><?php echo htmlspecialchars($signup['OrderDateFM']); ?></td>
                            <td><?php echo htmlspecialchars($signup['SoldBy']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No signups found for this email.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- Pagination for Signup History -->
        <div class="pagination">
            <?php if ($signupPage > 1): ?>
                <a href="?email=<?php echo urlencode($email); ?>&signupPage=<?php echo ($signupPage - 1); ?>">Previous</a>
            <?php endif; ?>
            <?php if (count($signups) === $signupPerPage): ?>
                <a href="?email=<?php echo urlencode($email); ?>&signupPage=<?php echo ($signupPage + 1); ?>">Next</a>
            <?php endif; ?>
        </div>

        <h2>Completed Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Order Amount</th>
                    <th>SoldBy</th>
                    <th>Order Date</th>
                    <th>Country</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Province/State</th>
                    <th>Postal Code</th>
                    <th>Phone</th>
                    <th>Tracking Number</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($completedOrders) > 0): ?>
                    <?php foreach ($completedOrders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['Name']); ?></td>
                            <td><?php echo htmlspecialchars($order['Email']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_amount']); ?></td>
                            <td><?php echo htmlspecialchars($order['SoldBy']); ?></td>
                            <td><?php echo htmlspecialchars($order['OrderDateFM']); ?></td>
                            <td><?php echo htmlspecialchars($order['Country']); ?></td>
                            <td><?php echo htmlspecialchars($order['Address']); ?></td>
                            <td><?php echo htmlspecialchars($order['City']); ?></td>
                            <td><?php echo htmlspecialchars($order['ProvinceState']); ?></td>
                            <td><?php echo htmlspecialchars($order['PostalZipCode']); ?></td>
                            <td><?php echo htmlspecialchars($order['Phone']); ?></td>
                            <td><?php echo htmlspecialchars($order['Tracking_Number']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12">No completed orders found for this email.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- Pagination for Completed Orders -->
        <div class="pagination">
            <?php if ($completedPage > 1): ?>
                <a href="?email=<?php echo urlencode($email); ?>&completedPage=<?php echo ($completedPage - 1); ?>">Previous</a>
            <?php endif; ?>
            <?php if (count($completedOrders) === $completedPerPage): ?>
                <a href="?email=<?php echo urlencode($email); ?>&completedPage=<?php echo ($completedPage + 1); ?>">Next</a>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
