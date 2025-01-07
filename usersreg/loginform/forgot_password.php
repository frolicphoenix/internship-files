<?php
require_once __DIR__ . '/../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT u.user_id FROM users u JOIN user_information ui ON u.user_id = ui.user_id WHERE ui.email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $unique_key = bin2hex(random_bytes(32));
        
        $stmt = $pdo->prepare("INSERT INTO user_key (user_id, unique_key) VALUES (?, ?)");
        $stmt->execute([$user['user_id'], $unique_key]);

        // echo "Password reset instructions sent to your email.";
        
        echo "<p style='color: black; font-size: 24px;'>Password reset instructions sent to your email. Click here to reset: 
            <a style='color: blue;' href='reset_password.php?key=" . $unique_key . "'>Reset Password</a>";

        
    } else {
        echo "Email not found.";
    }
}
?>

<link rel='stylesheet' href="formstyles.css">

<body>
    
    <div class="card">
        <h2 class="card-title">Forgot Password</h2>
        <form method="post" class="forgot-password-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <input type="submit" value="Reset Password" class="btn btn-submit">
        </form>
        <div class="forgot-password-links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
