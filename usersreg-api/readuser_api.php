<?php
require_once 'api.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'read') {
    
    if (isset($_GET['id'])) {
        // Fetch a specific user
        $user_id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT m.*, g.gender_type, t.size FROM mock_data m LEFT JOIN gender g ON m.gender_id = g.id LEFT JOIN top_size t ON m.top_size_id = t.id WHERE m.id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user) {
            echo json_encode($user);
        } else {
            echo json_encode(["error" => "User not found."]);
        }
    } else {
        // Fetch all users 
        $stmt = $pdo->prepare("SELECT m.*, g.gender_type, t.size 
                   FROM mock_data m 
                   LEFT JOIN gender g ON m.gender_id = g.id 
                   LEFT JOIN top_size t ON m.top_size_id = t.id");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($users)) {
            echo json_encode(["message" => "No users found."]);
        } else {
            echo json_encode($users);
        }
    }
}
