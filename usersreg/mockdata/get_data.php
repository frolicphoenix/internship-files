<?php
require_once __DIR__ . '/../db_connect.php';

// query to get the data to show in the frontend
// gender and top_size is joined to mock_data table to replace the id value for the string
// the table is then ordered in ascending order by mock_data table id 
// the limit to show data is set to 50
$query = "SELECT m.id, m.first_name, m.last_name, m.email, g.gender_type as gender, 
          m.salary, m.position, t.size as top_size, m.date_started 
          FROM MOCK_DATA m
          JOIN gender g ON m.gender_id = g.id
          JOIN top_size t ON m.top_size_id = t.id
          ORDER BY m.id ASC
          LIMIT 50";

// prepares an sql statement to be executed by the PDOStatement::execute() method
$stmt = $pdo->prepare($query);
$stmt->execute();

// method returns an array containing all the remaining rows in the result set
// the fetch method returns each row as an array indexed by column name
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// returns a json string of $data
echo json_encode($data);

