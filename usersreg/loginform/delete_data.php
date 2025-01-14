<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_type_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $query = "DELETE FROM MOCK_DATA WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);

        // checking if any row is affected
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Record not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
