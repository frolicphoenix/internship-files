<?php
include 'db_connect.php';
include 'functions.php';

// Get the selected year from the URL; default to an empty string if not provided
$selectedYear = $_GET['year'] ?? '';
if (!$selectedYear) {
    echo json_encode([]);
    exit;
}

// Initialize an array
$weeklyCounts = array_fill(1, 53, 0);

// Select all non-empty OrderDate values from the dailyweborders table
$sql = "SELECT OrderDate FROM dailyweborders 
        WHERE OrderDate IS NOT NULL 
          AND OrderDate != ''";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Process each row to count the number of orders 
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dateObj = parseOrderDate($row['OrderDate']);
    // If the date is valid and belongs to the selected year, update the weekly count
    if ($dateObj && $dateObj->format('Y') === $selectedYear) {
        $week = (int) $dateObj->format('W'); // Get the ISO week number (1-53)
        $weeklyCounts[$week]++;
    }
}

// Output the weekly counts as a JSON array
echo json_encode(array_values($weeklyCounts));
?>
