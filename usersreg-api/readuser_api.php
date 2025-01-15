<?php
require_once 'api.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'read') {
    
    $user_id = $_GET['id'];

    if ($user_id) {

        //fetch a specific user
        $stmt = $pdo->prepare("SELECT m.*, g.gender_type, t.size FROM mock_data m LEFT JOIN gender g ON m.gender_id = g.id LEFT JOIN top_size t ON m.top_size_id = t.id WHERE m.id = ?");

        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user) {
            echo json_encode($user);
        } else {
            echo json_encode(["error"=> "User not found."]);
        }
    } else {
        //fetch all users 
        echo json_encode(["error"=> "No user id found."]);

        // Fetch all users (with pagination)
        // $page = $_GET['page'] ?? 1;
        // $limit = 10;
        // $offset = ($page - 1) * $limit;

        // $stmt = $pdo->prepare("SELECT m.*, g.gender_type, t.size 
        //                        FROM mock_data m 
        //                        LEFT JOIN gender g ON m.gender_id = g.id 
        //                        LEFT JOIN top_size t ON m.top_size_id = t.id 
        //                        LIMIT ? OFFSET ?");
        // $stmt->execute([$limit, $offset]);
        // $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // echo json_encode($users);
    }
}