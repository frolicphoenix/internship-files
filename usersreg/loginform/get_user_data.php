<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_type_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM mock_data WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($user);
?>
