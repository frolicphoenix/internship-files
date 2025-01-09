
<?php
// Database connection details
$host = 'localhost';
$dbname = 'usersreg';
$username = 'root';
$password = '';

try {
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data from the database
    $stmt = $pdo->query("SELECT * FROM mock_data");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers for file download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="database_export.csv"');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Write CSV header
    fputcsv($output, array_keys($data[0]));

    // Write data rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    // Close the output stream
    fclose($output);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
