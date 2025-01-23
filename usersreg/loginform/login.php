<?php
require_once __DIR__ . '/../db_connect.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && md5($password) === $user['password']) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['admin_type_id'] = $user['admin_type_id'];
        
        // Redirect based on admin type
        if ($user['admin_type_id'] == 1) {
            header("Location: superadmin_dashboard.php");

        } else if($user['admin_type_id'] == 2 || $user['admin_type_id'] == 3 || $user['admin_type_id'] == 4  || $user['admin_type_id'] == 5) {
            header("Location: table.php");

        } else if ($user['admin_type_id'] == 8 || $user['admin_type_id'] == 1){
           header("Location: ../importlogicf/index.php");

        } else {
            header("Location: welcome.php");
        }
        exit();
    
    } else {
        echo "Invalid username or password";
    }
}
?>

<link rel='stylesheet' href="formstyles.css"> 

<body>

    <div class="card">
        <h2 class="card-title">Login</h2>
        <form method="post" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="submit" value="Login" class="btn btn-submit">
        </form>
        <div class="login-links">
            <a href="forgot_password.php">Forgot Password?</a>
            <a href="http://localhost/usersreg/loginform/register.php">Register</a>
        </div>
    </div>
</body>

