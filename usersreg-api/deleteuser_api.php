<?php
require_once 'api.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $_GET['action'] === 'delete') {
    // $userId = $_GET['id'];
    $uniqueKey = $_GET['key'];

    if (!$uniqueKey) {
        echo json_encode(["error" => "Unique key is required"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM regusers_key WHERE unique_key = ?");
    $stmt->execute([$uniqueKey]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(["error" => "Invalid  unique key"]);
        exit;
    }

    $userId = $result['id'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM regusers_key WHERE id = ?");
        $stmt->execute([$userId]);

        $stmt = $pdo->prepare("DELETE FROM mock_data WHERE id = ?");
        $stmt->execute([$userId]);

        $pdo->commit();
        echo json_encode(["message" => "User deleted successfully"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["error" => "Failed to delete user: " . $e->getMessage()]);
    }
}