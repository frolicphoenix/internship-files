<?php
include 'db_connect.php';
include 'functions.php';

// Retrieve search term 
$searchTerm = getSearchTerm();
$selectedStatus = isset($_GET['status']) ? trim($_GET['status']) : '';

// Get sort column and order from the URL 
$sortColumn = getSortColumn();
$sortOrder = getSortOrder();

// Pagination settings: 40 records per page. (CAN BE CHANGED TO DESIRED NUMBER)
$limit = 40;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch all distinct statuses from the 'order_limbo' table for filter options
$statusSql = "SELECT DISTINCT status FROM order_limbo WHERE status IS NOT NULL";
$statusStmt = $pdo->query($statusSql);
$statuses = $statusStmt->fetchAll(PDO::FETCH_COLUMN);

// Build the base SQL query for fetching orders
$sql = "SELECT * FROM order_limbo WHERE 1=1";

// Add search filtering if a search term is provided
if (!empty($searchTerm)) {
    $sql .= " AND (Name LIKE :search OR Email LIKE :search)";
}

// Add status filtering if a specific status is selected
if ($selectedStatus !== '') {
    $sql .= " AND status = :status";
}

// Determine the SQL column to sort by. Default is 'id'
$orderBy = 'id';
if ($sortColumn === 'Email') {
    $orderBy = 'Email';
} elseif ($sortColumn === 'OrderDateFM') {
    $orderBy = 'OrderDate';
}

$sql .= " ORDER BY $orderBy $sortOrder LIMIT :offset, :limit";

$stmt = $pdo->prepare($sql);

if (!empty($searchTerm)) {
    $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
}

if ($selectedStatus !== '') {
    $stmt->bindValue(':status', $selectedStatus, PDO::PARAM_STR);
}

$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order List</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
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
        .search-form input, .search-form select, .search-form button {
            margin-right: 5px;
            padding: 5px;
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
            <li><a href="#">Orders</a></li>
            <li><a href="signup_list.php">Customers</a></li>
        </ul>
    </aside>

    
    <main class="main-content">
        <header class="dashboard-header">
            <h1>Order List</h1>

            <!-- Search and filter form -->
            <form action="order_list.php" method="GET" class="search-form" style="margin-top: 20px;">
                <input type="text" name="search" placeholder="Search Name or Email" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status">
                    <option value="">-- All --</option>
                    <?php foreach ($statuses as $statusOption): ?>
                        <option value="<?php echo htmlspecialchars($statusOption); ?>"
                            <?php echo ($statusOption === $selectedStatus) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($statusOption); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Search</button>
            </form>
        </header>

        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>
                        Order Date
                        <!-- Sorting links for Order Date -->
                        <a href="order_list.php?sort=OrderDateFM&order=ASC&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($selectedStatus); ?>">
                            <img src="up.png" style="width: 15px; height: 15px;">
                        </a>
                        <a href="order_list.php?sort=OrderDateFM&order=DESC&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($selectedStatus); ?>">
                            <img src="down.png" style="width: 15px; height: 15px;">
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php $dateObj = parseOrderDate($order['OrderDate'] ?? ''); ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['Name']); ?></td>
                            <td><?php echo htmlspecialchars($order['Email']); ?></td>
                            <td><?php echo htmlspecialchars($order['Price']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <!-- Display formatted date using helper function -->
                            <td><?php echo htmlspecialchars(formatDate($dateObj)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Message if no orders are found -->
                    <tr><td colspan="5">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($selectedStatus); ?>">Previous</a>
            <?php endif; ?>
            <?php if (count($orders) === $limit): ?>
                <a href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($selectedStatus); ?>">Next</a>
            <?php endif; ?>
        </div>

    </main>
</div>
</body>
</html>
