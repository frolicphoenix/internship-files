<?php
include 'db_connect.php';
include 'functions.php';

// Get search term
$searchTerm = getSearchTerm();

// Pagination variables
$limit = 40;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get sort column and sort order
$sortColumn = getSortColumn();
$sortOrder = getSortOrder();

// Fetch all signups (unique)
$sql = "
    SELECT 
        Email, 
        SoldBy,
        CASE 
            WHEN STR_TO_DATE(OrderDate, '%Y%m%d') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%Y%m%d')
            WHEN STR_TO_DATE(OrderDate, '%m/%d/%Y') IS NOT NULL THEN STR_TO_DATE(OrderDate, '%m/%d/%Y')
            ELSE NULL
        END AS OrderDateFM
    FROM dailyweborders
";

if (!empty($searchTerm)) {
    $sql .= " WHERE Email LIKE :search";
}

$sql .= " ORDER BY $sortColumn $sortOrder LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

if (!empty($searchTerm)) {
    $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
}

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

                <!-- <form action="signup_list.php" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search Email" value="<?php //echo htmlspecialchars($searchTerm); ?>">
                        <button type="submit">Search</button>
                </form> -->
                <form action="signup_list.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search Email" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>" placeholder="Start Date">
                <input type="date" name="end_date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>" placeholder="End Date">
                <button type="submit">Search</button>
            </form>
            </header>

            <table border="1">
                <thead>
                    <tr>
                        <th><a href="<?php echo htmlspecialchars(getSignupListURL('Email', $sortColumn, $sortOrder)); ?>">Email</a></th>
                        <th><a href="<?php echo htmlspecialchars(getSignupListURL('SoldBy', $sortColumn, $sortOrder)); ?>">SoldBy</a></th>
                        <th><a href="<?php echo htmlspecialchars(getSignupListURL('OrderDateFM', $sortColumn, $sortOrder)); ?>">OrderDate</a></th>
                        <th>History</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($signups as $signup): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($signup['Email']); ?></td>
                            <td><?php echo htmlspecialchars($signup['SoldBy']); ?></td>
                            <td><?php echo htmlspecialchars($signup['OrderDateFM']); ?></td>
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
                <a href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($searchTerm); ?>">Next</a>
            </div>  
            <a href="index.php<?php echo empty($searchTerm) ? '' : '?search=' . urlencode($searchTerm); ?>">Back to Dashboard</a>
        </main>
    </div>
    
</body>
</html>
