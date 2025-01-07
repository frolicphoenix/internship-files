<?php
require_once __DIR__ . '/../db_connect.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="pagestyles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        </header>
        <main>
            <p>You have a guest account.</p>
        </main>
        <footer>
            <a href="logout.php" class="btn">Logout</a>
        </footer>
    </div>
</body>
</html>
