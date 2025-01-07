<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (isset($_SESSION['admin_type_id'])) {
    $stmt = $pdo->prepare("SELECT admin_type_name FROM user_admin_type WHERE admin_type_id = ?");
    $stmt->execute([$_SESSION['admin_type_id']]);
    $admin_type = $stmt->fetchColumn();
    echo $admin_type;
} else {
    echo 'Guest';
}
?>
