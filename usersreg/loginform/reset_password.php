<?php
require_once __DIR__ . '/../db_connect.php';
?>

<link rel='stylesheet' href="formstyles.css">

<body>
<div class="card">
        <h2 class="card-title">Reset Password</h2>
        <?php
        if (isset($_GET['key'])) {
            $key = $_GET['key'];

            $stmt = $pdo->prepare("SELECT user_id FROM user_key WHERE unique_key = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
            $stmt->execute([$key]);
            $user = $stmt->fetch();

            if ($user) {
                ?>
                <form method="post" action="update_password.php" class="reset-password-form">
                    <input type="hidden" name="key" value="<?php echo htmlspecialchars($key); ?>">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <input type="submit" value="Reset Password" class="btn btn-submit">
                </form>
                <?php
            } else {
                echo "<p class='message'>Invalid or expired reset key.</p>";
            }
        } else {
            echo "<p class='message'>No reset key provided.</p>";
        }
        ?>
        <div class="reset-password-links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
