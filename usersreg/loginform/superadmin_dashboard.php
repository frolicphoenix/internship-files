<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Check if user is logged in and is a superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['admin_type_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle form submission for updating privileges
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo->beginTransaction();
    try {
        foreach ($_POST['privileges'] as $admin_type_id => $privileges) {
            // Delete existing privileges for this admin type
            $stmt = $pdo->prepare("DELETE FROM admin_privileges WHERE admin_type_id = ?");
            $stmt->execute([$admin_type_id]);

            // Insert new privileges
            $stmt = $pdo->prepare("INSERT INTO admin_privileges (admin_type_id, privilege_type, value_id) VALUES (?, ?, ?)");

            if (isset($privileges['gender'])) {
                foreach ($privileges['gender'] as $gender_id) {
                    $stmt->execute([$admin_type_id, 'gender', $gender_id]);
                }
            }
            if (isset($privileges['top_size'])) {
                foreach ($privileges['top_size'] as $size_id) {
                    $stmt->execute([$admin_type_id, 'top_size', $size_id]);
                }
            }
        }
        $pdo->commit();
        $success_message = "Privileges updated successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error updating privileges: " . $e->getMessage();
    }
}

// Fetch all admin types except superadmin
$stmt = $pdo->prepare("SELECT * FROM user_admin_type WHERE admin_type_id != 1 AND admin_type_id != 7");
$stmt->execute();
$admin_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all genders and top sizes
$stmt = $pdo->prepare("SELECT * FROM gender");
$stmt->execute();
$genders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM top_size");
$stmt->execute();
$top_sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch current privileges for each admin type
$current_privileges = [];
$stmt = $pdo->prepare("SELECT admin_type_id, privilege_type, value_id FROM admin_privileges");
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $current_privileges[$row['admin_type_id']][$row['privilege_type']][] = $row['value_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Dashboard</title>
    <link rel="stylesheet" href="pagestyles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, Superadmin!</h1>
            <nav>
                <a href="logout.php" class="btn">Logout</a>
                <a href="superadmin_table.html" class="btn">Table</a>
                <a href="superadmin_roles.php" class="btn">Set Roles</a>
            </nav>
        </header>

        <?php if (isset($success_message)): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <main>
            <h2>Set Admin Privileges</h2>
            <form method="post">
                <?php foreach ($admin_types as $admin_type): ?>
                    <section class="admin-type">
                        <h3><?php echo htmlspecialchars($admin_type['admin_type_name']); ?></h3>
                        
                        <div class="privilege-group">
                            <h4>Genders:</h4>
                            <?php foreach ($genders as $gender): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" 
                                           name="privileges[<?php echo $admin_type['admin_type_id']; ?>][gender][]" 
                                           value="<?php echo $gender['id']; ?>"
                                           <?php echo (isset($current_privileges[$admin_type['admin_type_id']]['gender']) && 
                                                       in_array($gender['id'], $current_privileges[$admin_type['admin_type_id']]['gender'])) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($gender['gender_type']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="privilege-group">
                            <h4>Top Sizes:</h4>
                            <?php foreach ($top_sizes as $size): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" 
                                           name="privileges[<?php echo $admin_type['admin_type_id']; ?>][top_size][]" 
                                           value="<?php echo $size['id']; ?>"
                                           <?php echo (isset($current_privileges[$admin_type['admin_type_id']]['top_size']) && 
                                                       in_array($size['id'], $current_privileges[$admin_type['admin_type_id']]['top_size'])) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($size['size']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
                
                <input type="submit" value="Update Privileges" class="btn submit-btn">
            </form>
        </main>
    </div>
</body>
</html>
