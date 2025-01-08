<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usersreg";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csvFile"])) {
    $file = $_FILES["csvFile"]["tmp_name"];
    
    if (($handle = fopen($file, "r")) !== FALSE) {
        try {
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }

            // Skip the header row
            fgetcsv($handle);

            $conn->begin_transaction();

            // Read the CSV data and insert into the database
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $id = $data[0]; // Get the id from the first column

                // Check if the record already exists
                $checkSql = "SELECT id FROM mock_data WHERE id = ?";
                $stmt = $conn->prepare($checkSql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) {
                    // Insert new record
                    $sql = "INSERT INTO mock_data (id, first_name, last_name, email, salary, position, date_started, gender_id, top_size_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssssiii", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8]);
                    
                    if ($stmt->execute()) {
                        echo "New record inserted successfully (ID: $id)<br>";
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                } else {
                    echo "Record with ID $id already exists. Skipping.<br>";
                }
            }


            $conn->commit();
            fclose($handle);
            $conn->close();
            echo "CSV data has been successfully imported into the database.";
        } catch (Exception $e) {
            if (isset($conn)) {
                $conn->rollback();
                $conn->close();
            }
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Unable to open the CSV file.";
    }
} else {
    echo "No file was uploaded or an error occurred during upload.";
}
