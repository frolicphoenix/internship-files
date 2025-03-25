<?php
include 'db_connect.php';
include 'functions.php';

$searchTerm = getSearchTerm();
$startDate = getStartDate();
$endDate = getEndDate();

// Set up pagination: 40 records per page (can be changed to desired number)
$limit = 40;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get the sort column and order using helper functions
$sortColumn = getSortColumn();
$sortOrder = getSortOrder();

$sql = "
    SELECT 
        Email, 
        SoldBy,
        Name,
        OrderDate AS OrderDateRaw
    FROM dailyweborders
    WHERE 1=1
";

// Add a search filter if a search term is provided
if (!empty($searchTerm)) {
    $sql .= " AND (Email LIKE :search OR Name LIKE :search OR SoldBy LIKE :search)";
}

// Add a start date filter 
// Uses a CASE statement to handle multiple date formats
if (!empty($startDate)) {
    $sql .= " AND (
        CASE 
            WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%Y%m%d')
            WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%m/%d/%Y')
            ELSE NULL
        END >= :start_date)";
}

// Add an end date filter, similar to the start date filter
if (!empty($endDate)) {
    $sql .= " AND (
        CASE 
            WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%Y%m%d')
            WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%m/%d/%Y')
            ELSE NULL
        END <= :end_date)";
}

// Append ORDER BY clause to sort the results based on the OrderDate in various formats
// The query also includes LIMIT and OFFSET for pagination
$sql .= " ORDER BY 
    CASE 
        WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%Y%m%d')
        WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%m/%d/%Y')
        ELSE NULL
    END $sortOrder LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

if (!empty($searchTerm)) {
    $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
}

if (!empty($startDate)) {
    $stmt->bindValue(':start_date', $startDate, PDO::PARAM_STR);
}
if (!empty($endDate)) {
    $stmt->bindValue(':end_date', $endDate, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$signups = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-up List</title>
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
    <link rel="stylesheet" href="dashboard.css">
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
                <li><a href="#">Customers</a></li>
            </ul>
        </aside>

        <main class="main-content">
            
            <!-- Dashboard header and search form -->
            <header class="dashboard-header">
                <h1>Sign-up List</h1>
                <form action="signup_list.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search Email or Name or Seller" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" placeholder="Start Date">
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" placeholder="End Date">
                    <button type="submit">Search</button>
                </form>
            </header>

            <!-- Table displaying signup records -->
            <table border="1">
                <thead>
                    <tr>
                        <!-- The column headers include sorting links via the getSignupListURL function -->
                        <th><a href="<?php echo htmlspecialchars(getSignupListURL('Email', $sortColumn, $sortOrder)); ?>">Email</a></th>
                        <th>Name</th>
                        <th><a href="<?php echo htmlspecialchars(getSignupListURL('SoldBy', $sortColumn, $sortOrder)); ?>">SoldBy</a></th>
                        <th>
                            Order Date
                            <!-- Sorting links for ascending and descending order -->
                            <a href="<?php echo htmlspecialchars(getSignupListURL('OrderDateRaw', 'ASC')); ?>"><img src="up.png" style="width: 15px; height: 15px;"></a>
                            <a href="<?php echo htmlspecialchars(getSignupListURL('OrderDateRaw', 'DESC')); ?>"><img src="down.png" style="width: 15px; height: 15px;"></a>
                        </th>
                        <th>History</th>
                    </tr>
                </thead>
                <tbody>
                <!-- Loop through each signup record and display its details -->
                <?php foreach ($signups as $signup): ?>
                    <?php $dateObj = parseOrderDate($signup['OrderDateRaw']); ?>
                    <tr>
                        <td><?php echo htmlspecialchars($signup['Email']); ?></td>
                        <td><?php echo htmlspecialchars($signup['Name']); ?></td>
                        <td><?php echo htmlspecialchars($signup['SoldBy']); ?></td>
                        <!-- Format the order date using a helper function -->
                        <td><?php echo htmlspecialchars(formatDate($dateObj)); ?></td>
                        <!-- Link to the user's history page -->
                        <td><a href="user_history.php?email=<?php echo urlencode($signup['Email']); ?>">View History</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <!-- pagination links -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($searchTerm); ?>">Previous</a>
                <?php endif; ?>
                <?php if (count($signups) === $limit): ?>
                    <a href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($searchTerm); ?>">Next</a>
                <?php endif; ?>
            </div>  
        </main>
    </div>
</body>
</html>
