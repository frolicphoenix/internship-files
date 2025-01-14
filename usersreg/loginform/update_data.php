<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_type_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $salary = $_POST['salary'];
    $position = $_POST['position'];
    $top_size = $_POST['top_size'];
    $date_started = $_POST['date_started'];

    try {
        $query = "UPDATE MOCK_DATA SET 
                  first_name = ?, last_name = ?, email = ?, 
                  gender_id = (SELECT id FROM gender WHERE gender_type = ?),
                  salary = ?, position = ?, 
                  top_size_id = (SELECT id FROM top_size WHERE size = ?),
                  date_started = ?
                  WHERE id = ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$first_name, $last_name, $email, $gender, $salary, $position, $top_size, $date_started, $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No changes made or record not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
