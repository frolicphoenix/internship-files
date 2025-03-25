<?php
include 'db_connect.php';
include 'functions.php';

// Get the selected year from the URL; default to an empty string 
$selectedYear = $_GET['year'] ?? '';
if (!$selectedYear) {
    echo json_encode([]);
    exit;
}

// Initialize an array with 12 zeros for each month (January to December)
$monthlyCounts = array_fill(1, 12, 0);

// Select all non-empty OrderDate values from the dailyweborders tabl
$sql = "SELECT OrderDate FROM dailyweborders 
        WHERE OrderDate IS NOT NULL AND OrderDate != ''";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Process each row to count the number of orders per month for the selected year
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dateObj = parseOrderDate($row['OrderDate']);
    // If the date is valid and matches the selected year, increment the respective month
    if ($dateObj && $dateObj->format('Y') === $selectedYear) {
        $month = (int)$dateObj->format('n'); // Month as a number (1-12)
        $monthlyCounts[$month]++;
    }
}

// Output the monthly counts as a JSON array
echo json_encode(array_values($monthlyCounts));
?>
