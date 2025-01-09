
<?php

$host = 'localhost';
$dbname = 'usersreg';
$username = 'root';
$password = '';

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // fetching data from database
    $stmt = $pdo->query("SELECT * FROM mock_data");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //setting headers for file download
    // header('Content-Type: text/csv');
    // header('Content-Disposition: attachment; filename="database_export.csv"');

    date_default_timezone_set('EST');

    // openning output stream and saving to downloads/
    $filelocation = 'downloads/';
    $filename = 'export-' .date('H.i.s').'.csv';
    $file_export = $filelocation . $filename;

    $output = fopen($file_export, 'w');

    // writing csv headers
    fputcsv($output, array_keys($data[0]));

    // write data rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    // close the output stream
    fclose($output);
    echo "Database downloaded.";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
