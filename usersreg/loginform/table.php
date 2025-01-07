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
    <title>Mock Information Table</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="adminstyles.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
</head>
<body>
<div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div class="user-info">
                <p>You have <span id="adminType"></span> privileges</p>
                <nav>
                    <button id="logoutBtn" class="btn">Logout</button>
                </nav>
            </div>
        </header>

        <main>
            <div class="table-container">
                <table id="mockTable" class="display">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Salary</th>
                            <th>Position</th>
                            <th>Top Size</th>
                            <th>Date Started</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
