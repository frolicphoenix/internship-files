<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usersreg";

// checks if the form was submitted via POST method and if a file names "csvFile" was uploaded.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csvFile"])) {

    // gets the temporary name of the uploaded file on the server
    $file = $_FILES["csvFile"]["tmp_name"];
    
    if (($handle = fopen($file, "r")) !== FALSE) {
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Skip the header row
            fgetcsv($handle);

            $pdo->beginTransaction();

            // Read the CSV data and insert or update the database
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $id = !empty($data[0]) ? $data[0] : null; // Get the id from the first column if it exists

                if ($id !== null) {
                    // Update existing record
                    $sql = "UPDATE mock_data SET 
                            first_name = :first_name, 
                            last_name = :last_name, 
                            email = :email, 
                            salary = :salary, 
                            position = :position, 
                            date_started = :date_started, 
                            gender_id = :gender_id, 
                            top_size_id = :top_size_id 
                            WHERE id = :id";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                } else {
                    // Insert new record
                    $sql = "INSERT INTO mock_data (first_name, last_name, email, salary, position, date_started, gender_id, top_size_id) 
                            VALUES (:first_name, :last_name, :email, :salary, :position, :date_started, :gender_id, :top_size_id)";
                    
                    $stmt = $pdo->prepare($sql);
                }

                // Bind parameters for both update and insert
                $stmt->bindParam(':first_name', $data[1], PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $data[2], PDO::PARAM_STR);
                $stmt->bindParam(':email', $data[3], PDO::PARAM_STR);
                $stmt->bindParam(':salary', $data[4], PDO::PARAM_STR);
                $stmt->bindParam(':position', $data[5], PDO::PARAM_STR);
                $stmt->bindParam(':date_started', $data[6], PDO::PARAM_STR);
                $stmt->bindParam(':gender_id', $data[7], PDO::PARAM_INT);
                $stmt->bindParam(':top_size_id', $data[8], PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    echo $id !== null ? "." : ". " . $pdo->lastInsertId();
                } else {
                    echo "Error: " . $sql . "<br>";
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
