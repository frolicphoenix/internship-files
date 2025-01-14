<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_type_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$id = $_POST['id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$gender_id = $_POST['gender_id'];
$salary = $_POST['salary'];
$position = $_POST['position'];
$top_size_id = $_POST['top_size_id'];
$date_started = $_POST['date_started'];

$stmt = $pdo->prepare("UPDATE MOCK_DATA SET first_name = ?, last_name = ?, email = ?, gender_id = ?, salary = ?, position = ?, top_size_id = ?, date_started = ? WHERE id = ?");
$result = $stmt->execute([$first_name, $last_name, $email, $gender_id, $salary, $position, $top_size_id, $date_started, $id]);

if ($result) {
    echo 'success';
} else {
    echo 'error';
}
?>
