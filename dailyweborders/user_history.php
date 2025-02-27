<?php
include 'db_connect.php';
include 'functions.php';

// Get the email from the query string
$email = isset($_GET['email']) ? $_GET['email'] : null;

if (!$email) {
    echo "No email provided.";
    die();
}

// Get sort column and sort order
$sortColumn = getSortColumn();
$sortOrder = getSortOrder();

// Fetch signup history from chargedbackcards_log
$signupQuery = "
    SELECT 
        name,
        soldby AS order_by,
        DATE_FORMAT(date_added, '%Y-%m-%d %H:%i:%s') AS OrderDateFM
    FROM dailyweborders.chargedbackcards_log
    WHERE email = ?
    ORDER BY $sortColumn $sortOrder;
";
$signupStmt = $pdo->prepare($signupQuery);
$signupStmt->execute([$email]);
$signups = $signupStmt->fetchAll();

// Fetch completed orders from multiple tables (dailyweborders, orderscaptured, order_limbo)
$completedOrdersQuery = "
    SELECT 
        'dailyweborders' AS source_table,
        Name,
        Price AS order_amount,
        SoldBy AS order_by,
        CASE 
            WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN DATE_FORMAT(STR_TO_DATE(OrderDate, '%Y%m%d'), '%Y-%m-%d')
            WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN DATE_FORMAT(STR_TO_DATE(OrderDate, '%m/%d/%Y'), '%Y-%m-%d')
            ELSE NULL
        END AS OrderDateFM
    FROM dailyweborders
    WHERE Email = ?

    UNION ALL

    SELECT 
        'orderscaptured' AS source_table,
        Name,
        Price AS order_amount,
        SoldBy AS order_by,
        CASE 
            WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN DATE_FORMAT(STR_TO_DATE(OrderDate, '%Y%m%d'), '%Y-%m-%d')
            WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN DATE_FORMAT(STR_TO_DATE(OrderDate, '%m/%d/%Y'), '%Y-%m-%d')
            ELSE NULL
        END AS OrderDateFM
    FROM orderscaptured
    WHERE Email = ?

    UNION ALL

    SELECT 
        'order_limbo' AS source_table,
        Name,
        Price AS order_amount,
        SoldBy AS order_by,
        CASE 
            WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN DATE_FORMAT(STR_TO_DATE(OrderDate, '%Y%m%d'), '%Y-%m-%d')
            WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN DATE_FORMAT(STR_TO_DATE(OrderDate, '%m/%d/%Y'), '%Y-%m-%d')
            ELSE NULL
        END AS OrderDateFM
    FROM order_limbo
    WHERE Email = ?
";
$completedOrdersStmt = $pdo->prepare($completedOrdersQuery);
$completedOrdersStmt->execute([$email, $email, $email]); // Pass the email three times for UNION query
$completedOrders = $completedOrdersStmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User History</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>History of <?php echo htmlspecialchars($email); ?></h1>

    <!-- Signup History Table -->
    <h2>Signup History</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Order By</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($signups) > 0): ?>
                <?php foreach ($signups as $signup): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($signup['name']); ?></td>
                        <td><?php echo htmlspecialchars($signup['order_by']); ?></td>
                        <td><?php echo htmlspecialchars($signup['OrderDateFM']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No signups found for this email.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Completed Orders Table -->
    <h2>Completed Orders</h2>
    <table>
        <thead>
            <tr>
                <th>Source Table</th>
                <th>Name</th>
                <th>Order Amount</th>
                <th>Order By</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($completedOrders) > 0): ?>
                <?php foreach ($completedOrders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['source_table']); ?></td>
                        <td><?php echo htmlspecialchars($order['Name']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_amount']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_by']); ?></td>
                        <td><?php echo htmlspecialchars($order['OrderDateFM']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No completed orders found for this email.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Back Link -->
    <a href="signup_list.php">Back to Sign-up List</a>

</body>
</html>
