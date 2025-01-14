<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_type_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$admin_type_id = $_SESSION['admin_type_id'];

// Fetch admin privileges
$stmt = $pdo->prepare("SELECT privilege_type, value_id FROM admin_privileges WHERE admin_type_id = ?");
$stmt->execute([$admin_type_id]);
$privileges = $stmt->fetchAll(PDO::FETCH_ASSOC);

$gender_ids = [];
$top_size_ids = [];

foreach ($privileges as $privilege) {
    if ($privilege['privilege_type'] == 'gender') {
        $gender_ids[] = $privilege['value_id'];
    } elseif ($privilege['privilege_type'] == 'top_size') {
        $top_size_ids[] = $privilege['value_id'];
    }
}

// Construct the query based on privileges
$query = "SELECT m.id, m.first_name, m.last_name, m.email, g.gender_type as gender, 
          m.salary, m.position, t.size as top_size, m.date_started 
          FROM MOCK_DATA m
          JOIN gender g ON m.gender_id = g.id
          JOIN top_size t ON m.top_size_id = t.id
          WHERE 1=1";

if (!empty($gender_ids)) {
    $query .= " AND m.gender_id IN (" . implode(',', $gender_ids) . ")";
}

if (!empty($top_size_ids)) {
    $query .= " AND m.top_size_id IN (" . implode(',', $top_size_ids) . ")";
}

$query .= " ORDER BY m.id ASC LIMIT 50";

$stmt = $pdo->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add the update button to each row of data
foreach ($data as &$row) {
    $row['actions'] = '<button class="update-btn" data-id="' . $row['id'] . '">Update</button>';
}

echo json_encode($data);
?>
