<?php
require_once 'api.php';



if($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'register') {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];


    $salary = $_POST['salary'] ?? null;
    $position = $_POST['position'] ?? null;
    $date_started = $_POST['date_started'] ?? null;
    // $gender_id = $_POST['gender_id'];
    // $top_size_id = $_POST['top_size_id'];

    // validate required fields
    if (!$first_name || !$last_name || !$email) {
        echo json_encode(["error" => "First name, Last name and Email are required."]);
        exit;
    }

    try { 

        $pdo->beginTransaction();

        // inserting into mock_data table
        $stmt = $pdo->prepare("INSERT INTO mock_data (first_name, last_name, email, salary, position, date_started) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $salary, $position, $date_started]);
        $userId = $pdo->lastInsertId();

        // generate unique key
        $uniqueKey = bin2hex(random_bytes(16));

        // insert into regusers_key
        $stmt = $pdo->prepare("INSERT INTO regusers_key (id, unique_key) VALUES (?, ?);");
        $stmt->execute([$userId, $uniqueKey]);

        $pdo->commit();

        echo json_encode(["message"=> "User registered successfully", "id" => $userId, "unique_key" => $uniqueKey]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["error"=> $e->getMessage()]);
    }
    
}
