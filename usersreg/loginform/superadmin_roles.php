<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Check if user is logged in and is a superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['admin_type_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle form submission for updating admin roles
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo->beginTransaction();
    try {
        foreach ($_POST['admin_roles'] as $user_id => $admin_type_id) {
            $stmt = $pdo->prepare("UPDATE users SET admin_type_id = ? WHERE user_id = ?");
            $stmt->execute([$admin_type_id, $user_id]);
        }
        $pdo->commit();
        $success_message = "Admin roles updated successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error updating admin roles: " . $e->getMessage();
    }
}

// Fetch all users except superadmin
$stmt = $pdo->prepare("SELECT u.user_id, u.username, u.admin_type_id, uat.admin_type_name 
                       FROM users u 
                       JOIN user_admin_type uat ON u.admin_type_id = uat.admin_type_id 
                       WHERE u.admin_type_id != 1");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all admin types except superadmin
$stmt = $pdo->prepare("SELECT * FROM user_admin_type WHERE admin_type_id != 1");
$stmt->execute();
$admin_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Admin Roles</title>
    <link rel="stylesheet" href="pagestyles.css">
</head>
<body>
    <div class="container">
        <h1>Set Admin Roles</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="post">
            <?php foreach($users as $user): ?>
                <div class="user-role">
                    <label for="role_<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['username']); ?></label>
                    <select name="admin_roles[<?php echo $user['user_id']; ?>]" id="role_<?php echo $user['user_id']; ?>">
                        <?php foreach($admin_types as $admin_type): ?>
                            <option value="<?php echo $admin_type['admin_type_id']; ?>" 
                                    <?php echo ($user['admin_type_id'] == $admin_type['admin_type_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($admin_type['admin_type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; ?>
            <input type="submit" value="Update Roles" class="btn">
        </form>
        <a href="superadmin_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
