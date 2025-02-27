<?php

include 'db_connect.php';
include 'functions.php';

//search term from query string
$searchTerm = getSearchTerm();

//get total signup count based 
$sql = 'SELECT COUNT(DISTINCT Email) FROM dailyweborders';

if(!empty($searchTerm)) {
    $sql .= " WHERE Email LIKE ?";
}
$stmt = $pdo->prepare($sql);

if(!empty($searchTerm)) {
    $stmt->execute(["%" . $searchTerm . "%"]);
} else {
    $stmt->execute();
}
$totalSignups = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        .signup-summary {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Dashboard</h1>

    <div class="signup-summary">
        <h2>Total Signups: <?php echo $totalSignups; ?></h2>

        <form action="signup_list.php" method="GET">
            <input type="text" name="search" placeholder="Search Email" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Search</button>
        </form>

        <a href="signup_list.php<?php echo empty($searchTerm) ? '' : '?search=' . urlencode($searchTerm); ?>">View Signups</a>
    </div>
</body>
</html>