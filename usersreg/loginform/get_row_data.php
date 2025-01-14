<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_type_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No ID provided']);
    exit;
}

$id = $_GET['id'];

$query = "SELECT m.id, m.first_name, m.last_name, m.email, g.gender_type as gender, 
          m.salary, m.position, t.size as top_size, m.date_started 
          FROM MOCK_DATA m
          JOIN gender g ON m.gender_id = g.id
          JOIN top_size t ON m.top_size_id = t.id
          WHERE m.id = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($data);
?>
