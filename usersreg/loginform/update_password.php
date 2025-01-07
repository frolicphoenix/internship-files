<?php
require_once __DIR__ . '/../db_connect.php';
?>

<link rel='stylesheet' href="formstyles.css">

<body>
<div class="card">
        <h2 class="card-title">Update Password</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $key = $_POST['key'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password !== $confirm_password) {
                echo "<p class='message error'>Passwords do not match.</p>";
            } else {
                $stmt = $pdo->prepare("SELECT user_id FROM user_key WHERE unique_key = ? AND created_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
                $stmt->execute([$key]);
                $user = $stmt->fetch();

                $stmt = $pdo->prepare("DELETE FROM user_key WHERE unique_key = ?");
                $stmt->execute([$key]);

                if ($user) {
                    $hashed_password = md5($new_password);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                    $stmt->execute([$hashed_password, $user['user_id']]);

                    echo "<p class='message success'>Password successfully reset.</p>";
                    echo "<div class='update-password-links'><a href='login.php'>Click here to login</a></div>";
                } else {
                    echo "<p class='message error'>Invalid or expired reset key.</p>";
                }
            }
        } else {
            echo "<p class='message error'>Invalid request method.</p>";
        }
        ?>
        <!-- <div class="update-password-links">
            <a href="login.php">Back to Login</a>
        </div> -->
    </div>
</body>
