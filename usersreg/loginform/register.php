<?php
require_once __DIR__ . '/../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    // $admin_type_id = $_POST['admin_type_id'];

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (username, password, admin_type_id) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, 7]);
        $user_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO user_information (user_id, first_name, last_name, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $first_name, $last_name, $email]);

        $pdo->commit();
        echo "<p style='color:blue; position: relative;
bottom: 20px;'> Registration successful! </p>";

}
?>
<link rel='stylesheet' href="formstyles.css">

<body>
    
    <div class="card">
        <h2 class="card-title">Register</h2>
        <form method="post" class="register-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <input type="submit" value="Register" class="btn btn-submit">
        </form>
        <div class="register-links">
            <a href="login.php">Already have an account? Login here</a>
        </div>
    </div>
</body>