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
                <li><a href="">Dashboard</a></li>
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
                <h1>Customers</h1>
                <form action="signup_list.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search Email" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button type="submit">Search</button>
                </form>
            </header>

        </main>
    </div>
</body>
</html>
