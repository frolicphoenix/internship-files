<?php
// Include the database connection and helper functions
require_once 'db_connect.php'; 
include 'functions.php';

// Get the email parameter from the URL query string
$email = $_GET['email'] ?? ''; 

// Get sort column and order
$sortColumn = getSortColumn();
$sortOrder = getSortOrder();

// Set up pagination for the signup history
// Display 6 records per page
$signupPage = isset($_GET['signupPage']) ? (int)$_GET['signupPage'] : 1;
$signupPerPage = 6;  //CAN BE CHANGED TO DESIRED NUMBER
$signupOffset = ($signupPage - 1) * $signupPerPage;

// Build the query for signup history for the given email
$signupQuery = "
    SELECT 
        Name, 
        Email, 
        OrderDate AS OrderDateRaw, 
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

// Set up pagination for the completed orders section
// Again, 6 records per page
$completedPage = isset($_GET['completedPage']) ? (int)$_GET['completedPage'] : 1;
$completedPerPage = 6; //CAN BE CHANGED TO DESIRED NUMBER
$completedOffset = ($completedPage - 1) * $completedPerPage;

// Build the query for completed orders for the given email
// Only orders with complete details are considered "completed"
$completedOrdersQuery = "
    SELECT 
        Name, 
        Email,
        Price AS order_amount, 
        SoldBy, 
        OrderDate AS OrderDateRaw,
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
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="order_list.php">Orders</a></li>
            <li><a href="signup_list.php">Customers</a></li>
        </ul>
    </aside>

    <!-- SIGNUP HISTORY Section -->
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
                            <?php 
                                // Parse and format the order date.
                                $dateObj = parseOrderDate($signup['OrderDateRaw']); 
                            ?>
                            <td><?php echo htmlspecialchars(formatDate($dateObj)); ?></td>
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

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($signupPage > 1): ?>
                <a href="?email=<?php echo urlencode($email); ?>&signupPage=<?php echo ($signupPage - 1); ?>">Previous</a>
            <?php endif; ?>
            <?php if (count($signups) === $signupPerPage): ?>
                <a href="?email=<?php echo urlencode($email); ?>&signupPage=<?php echo ($signupPage + 1); ?>">Next</a>
            <?php endif; ?>
        </div>

        <!-- Completed Orders Section -->
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
                            <?php 
                                // Parse and format the order date
                                $orderDateObj = parseOrderDate($order['OrderDateRaw']); 
                            ?>
                            <td><?php echo htmlspecialchars(formatDate($orderDateObj)); ?></td>
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
                    <!-- Display message if no completed orders are found -->
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
