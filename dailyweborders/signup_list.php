<?php
include 'db_connect.php';
include 'functions.php';

$searchTerm = getSearchTerm();
$startDate = getStartDate();
$endDate = getEndDate();

// Pagination variables
$limit = 40;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get sort column and sort order
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
if (!empty($searchTerm)) {
    $sql .= " AND (Email LIKE :search OR Name LIKE :search OR SoldBy LIKE :search)";
}
if (!empty($startDate)) {
    $sql .= " AND (
        CASE 
            WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%Y%m%d')
            WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%m/%d/%Y')
            ELSE NULL
        END >= :start_date)";
}
if (!empty($endDate)) {
    $sql .= " AND (
        CASE 
            WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%Y%m%d')
            WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%m/%d/%Y')
            ELSE NULL
        END <= :end_date)";
}
$sql .= " ORDER BY 
    CASE 
        WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%Y%m%d')
        WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%m/%d/%Y')
        ELSE NULL
    END $sortOrder LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

// Bind parameters dynamically
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
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="#">Orders</a></li>
                <li><a href="#">Customers</a></li>
                <li><a href="#">Reports</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <h1>Sign-up List</h1>
                <!-- Search Form -->
                <form action="signup_list.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search Email or Name or Seller" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" placeholder="Start Date">
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" placeholder="End Date">
                    <button type="submit">Search</button>
                </form>
            </header>

            <!-- Table -->
            <table border="1">
                <thead>
                    <tr>
                        <th><a href="<?php echo htmlspecialchars(getSignupListURL('Email', $sortColumn, $sortOrder)); ?>">Email</a></th>
                        <th>Name</th>
                        <th><a href="<?php echo htmlspecialchars(getSignupListURL('SoldBy', $sortColumn, $sortOrder)); ?>">SoldBy</a></th>
                        <th>
                            Order Date
                            <a href="<?php echo htmlspecialchars(getSignupListURL('OrderDateRaw', 'ASC')); ?>"><img src="up.png" style="width: 15px; height: 15px;"></a>
                            <a href="<?php echo htmlspecialchars(getSignupListURL('OrderDateRaw', 'DESC')); ?>"><img src="down.png" style="width: 15px; height: 15px;"></a>
                        </th>
                        <th>History</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($signups as $signup): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($signup['Email']); ?></td>
                            <td><?php echo htmlspecialchars($signup['Name']); ?></td>
                            <td><?php echo htmlspecialchars($signup['SoldBy']); ?></td>
                            <td class="order-date" data-raw-date="<?php echo htmlspecialchars($signup['OrderDateRaw']); ?>">--</td>
                            <td><a href="user_history.php?email=<?php echo urlencode($signup['Email']); ?>">View History</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
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

    <script src="script.js"></script>
</body>
</html>
