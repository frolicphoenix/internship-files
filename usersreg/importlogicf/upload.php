<?php

session_start();
require_once __DIR__ . '/../db_connect.php';


// defines the uploads directory
$uploadDir = "uploads/";

// creates the upload directory if it doesn't exist ensuring there is somewhere the csv files gets uploaded
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// checks if the form was submitted via POST method and if a file named "csvFile" was uploaded
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csvFile"])) {

    // generates a unique filename to prevent from overwriting the file names (existing)
    $file = $_FILES["csvFile"];
    
    $filename = uniqid() . "_" . $file["name"];
    $uploadPath = $uploadDir . $filename;


    // this moves the uploaded file to the uploads directory
    if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
        echo "File uploaded successfully. ";

        // opening the uploaded file for reading "r"
        if (($handle = fopen($uploadPath, "r")) !== FALSE) {
            try {

                // skips the header row of csv file
                fgetcsv($handle);

                $pdo->beginTransaction();

                // loops through each row of the CSV file
                // reading the CSV data and insert or update the database
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    // if the condition (!empty($data)) is true, it assigns $data to $id
                    // if the condition is false, it assigns null to $id
                    $id = !empty($data[0]) ? $data[0] : null;

                    if ($id !== null) {
                        // update existing record
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
                        // insert new record
                        $sql = "INSERT INTO mock_data (first_name, last_name, email, salary, position, date_started, gender_id, top_size_id) 
                                VALUES (:first_name, :last_name, :email, :salary, :position, :date_started, :gender_id, :top_size_id)";
                        
                        $stmt = $pdo->prepare($sql);
                    }

                    // bind the CSV data to the SQL parameters for both update and insert
                    $stmt->bindParam(':first_name', $data[1], PDO::PARAM_STR);
                    $stmt->bindParam(':last_name', $data[2], PDO::PARAM_STR);
                    $stmt->bindParam(':email', $data[3], PDO::PARAM_STR);
                    $stmt->bindParam(':salary', $data[4], PDO::PARAM_STR);
                    $stmt->bindParam(':position', $data[5], PDO::PARAM_STR);
                    $stmt->bindParam(':date_started', $data[6], PDO::PARAM_STR);
                    $stmt->bindParam(':gender_id', $data[7], PDO::PARAM_INT);
                    $stmt->bindParam(':top_size_id', $data[8], PDO::PARAM_INT);
                    
                    // executing and providing feedback
                    // . for updates and new id for inserts
                    if ($stmt->execute()) {
                        echo $id !== null ? "." : ". " . $pdo->lastInsertId();
                    } else {
                        echo "Error: " . $sql;
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
        echo "Error moving uploaded file.";
    }
} else {
    echo "No file was uploaded or an error occurred during upload.";
}
