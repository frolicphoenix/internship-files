<?php
// Database connection details
$host = 'localhost';
$dbname = 'usersreg';
$username = 'root';
$password = '';

// Check if a file was uploaded
if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
    $file = $_FILES['csvFile']['tmp_name'];
    
    // Open the CSV file
    if (($handle = fopen($file, "r")) !== FALSE) {
        try {
            // Connect to the database using PDO
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Prepare SQL statement excluding 'id'
            $stmt = $pdo->prepare("INSERT INTO mock_data (first_name, last_name, email, salary, position, date_started, gender_id, top_size_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Skip header row
            fgetcsv($handle);
            
            // Read and insert data from CSV
            while (($data = fgetcsv($handle)) !== FALSE) {
                // Exclude 'id' column (index 0)
                array_shift($data);
                $stmt->execute($data);
            }
            
            fclose($handle);
            echo "CSV data has been successfully imported into the database.";
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Unable to open the CSV file.";
    }
} else {
    echo "No file was uploaded or an error occurred during upload.";
}
?>
