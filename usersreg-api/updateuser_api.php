<?php
require_once 'api.php';

if($_SERVER['REQUEST_METHOD'] === 'PUT' && $_GET['action'] === 'update') {
    $uniqueKey = $_GET['key'];

    if (!$uniqueKey) {
        echo json_encode(["error" => "Unique key is required"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM regusers_key WHERE unique_key = ?");
    $stmt->execute([$uniqueKey]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(["error" => "Invalid unique key"]);
        exit;
    }

    $userId = $result['id'];

    // read-only stream that allows to read the request body sent to it
    $data = json_decode(file_get_contents('php://input'), true);


    $updateFields = [];
    $params = [];
    foreach ($data as $key => $value) {
        if (in_array($key, ['first_name', 'last_name', 'email', 'salary', 'position', 'date_started', 'gender_id', 'top_size_id'])) {
            $updateFields[] = "$key = ?";
            $params[] = $value;
        }
    }
    $params[] = $userId;

    if (empty($updateFields)) {
        echo json_encode(["error" => "No valid fields to update"]);
        exit;
    }

    // $updateFields[] instead of bindParam=> name=:name, description=:description, email=:email
    $updateQuery = "UPDATE mock_data SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $pdo->prepare($updateQuery);
    
    if ($stmt->execute($params)) {
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update user"]);
    }
}