<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usersreg";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csvFile"])) {
    $file = $_FILES["csvFile"]["tmp_name"];
    
    if (($handle = fopen($file, "r")) !== FALSE) {
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Skip the header row
            fgetcsv($handle);

            $pdo->beginTransaction();

            // Read the CSV data and insert into the database
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $id = $data[0]; // Get the id from the first column

                // Check if the record already exists
                $checkSql = "SELECT id FROM mock_data WHERE id = :id";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
                $checkStmt->execute();

                if ($checkStmt->rowCount() == 0) {
                    // Insert new record
                    $sql = "INSERT INTO mock_data (id, first_name, last_name, email, salary, position, date_started, gender_id, top_size_id) 
                            VALUES (:id, :first_name, :last_name, :email, :salary, :position, :date_started, :gender_id, :top_size_id)";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $data[0], PDO::PARAM_INT);
                    $stmt->bindParam(':first_name', $data[1], PDO::PARAM_STR);
                    $stmt->bindParam(':last_name', $data[2], PDO::PARAM_STR);
                    $stmt->bindParam(':email', $data[3], PDO::PARAM_STR);
                    $stmt->bindParam(':salary', $data[4], PDO::PARAM_STR);
                    $stmt->bindParam(':position', $data[5], PDO::PARAM_STR);
                    $stmt->bindParam(':date_started', $data[6], PDO::PARAM_STR);
                    $stmt->bindParam(':gender_id', $data[7], PDO::PARAM_INT);
                    $stmt->bindParam(':top_size_id', $data[8], PDO::PARAM_INT);
                    
                    if ($stmt->execute()) {
                        echo "New record inserted successfully (ID: $id)";
                    } else {
                        echo "Error: " . $sql;
                    }
                } else {
                    echo ".";
                }
            }

            $pdo->commit();
            fclose($handle);
            echo "CSV data has been successfully imported into the database.";
        } catch (PDOException $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Unable to open the CSV file.";
    }
} else {
    echo "No file was uploaded or an error occurred during upload.";
}
