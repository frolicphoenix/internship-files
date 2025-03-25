<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_type_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Fetch gender values
$genderQuery = "SELECT DISTINCT gender_type FROM gender";
$genderStmt = $pdo->query($genderQuery);
$genders = $genderStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch top size values
$topSizeQuery = "SELECT DISTINCT size FROM top_size";
$topSizeStmt = $pdo->query($topSizeQuery);
$topSizes = $topSizeStmt->fetchAll(PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Information Table</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="pagestyles.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <style>
        .header {
            background-color: var(--background-color);
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header h1 {
            margin-bottom: 10px;
        }
        .header nav {
            display: flex;
            gap: 10px;
        }
        btn-container {
           padding: 20rem; 
        }
        .modal {
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, <span id="adminType"></span></h1>
        <nav>
            <button id="logoutBtn" class="btn">Logout</button>
            <a href="superadmin_dashboard.php" class="btn">DASHBOARD</a>
        </nav>
    </div>
    <div class="btn-container">
        <table id="mockTable" class="display">
            <thead>
                <tr>
                    <!-- <th>id</th> -->
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Salary</th>
                    <th>Position</th>
                    <th>Top Size</th>
                    <th>Date Started</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
    
    <div id="updateModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Information</h2>
            <form id="updateForm" style="white-space: pre">
                <input type="hidden" id="updateId" name="id">
                <label for="updateFirstName">First Name:</label>
                <input type="text" id="updateFirstName" name="first_name" required>
                <label for="updateLastName">Last Name:</label>
                <input type="text" id="updateLastName" name="last_name" required>
                <label for="updateEmail">Email:</label>
                <input type="email" id="updateEmail" name="email" required>
                <label for="updateGender">Gender:</label>
                <select id="updateGender" name="gender" required>
                    <?php foreach ($genders as $gender): ?>
                        <option value="<?php echo htmlspecialchars($gender); ?>"><?php echo htmlspecialchars($gender); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="updateSalary">Salary:</label>
                <input type="text" id="updateSalary" name="salary" required>
                <label for="updatePosition">Position:</label>
                <input type="text" id="updatePosition" name="position" required>
                <label for="updateTopSize">Top Size:</label>
                <select id="updateTopSize" name="top_size" required>
                    <?php foreach ($topSizes as $size): ?>
                        <option value="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($size); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="updateDateStarted">Date Started:</label>
                <input type="date" id="updateDateStarted" name="date_started" required>
                <!-- <button type="submit">Update</button> -->
            </form>
        </div>
    </div>


    <script src="sd_script.js"></script>
</body>
</html>
