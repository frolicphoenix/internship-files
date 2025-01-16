<?php
$host = 'localhost';
$dbname = 'usersreg';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    
    die("Connection failed: " . $e->getMessage());
}


// READ
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'read') {
    
    $user_id = $_GET['id'];

    if ($user_id) {

        //fetch a specific user
        $stmt = $pdo->prepare("SELECT m.*, g.gender_type, t.size FROM mock_data m LEFT JOIN gender g ON m.gender_id = g.id LEFT JOIN top_size t ON m.top_size_id = t.id WHERE m.id = ?");

        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user) {
            echo json_encode($user);
        } else {
            echo json_encode(["error"=> "User not found."]);
        }
    } else {
        //fetch all users 
        $stmt = $pdo->prepare("SELECT m.*, g.gender_type, t.size 
                   FROM mock_data m 
                   LEFT JOIN gender g ON m.gender_id = g.id 
                   LEFT JOIN top_size t ON m.top_size_id = t.id");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($users)) {
            echo json_encode(["message" => "No users found."]);
        } else {
            echo json_encode($users);
        }

    }
}


//CREATE
if($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'register') {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];


    $salary = $_POST['salary'] ?? null;
    $position = $_POST['position'] ?? null;
    $date_started = $_POST['date_started'] ?? null;
    // $gender_id = $_POST['gender_id'];
    // $top_size_id = $_POST['top_size_id'];

    // validate required fields
    if (!$first_name || !$last_name || !$email) {
        echo json_encode(["error" => "First name, Last name and Email are required."]);
        exit;
    }

    try { 

        $pdo->beginTransaction();

        // inserting into mock_data table
        $stmt = $pdo->prepare("INSERT INTO mock_data (first_name, last_name, email, salary, position, date_started) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $salary, $position, $date_started]);
        $userId = $pdo->lastInsertId();

        // generate unique key
        $uniqueKey = bin2hex(random_bytes(16));

        // insert into regusers_key
        $stmt = $pdo->prepare("INSERT INTO regusers_key (id, unique_key) VALUES (?, ?);");
        $stmt->execute([$userId, $uniqueKey]);

        $pdo->commit();

        echo json_encode(["message"=> "User registered successfully", "id" => $userId, "unique_key" => $uniqueKey]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["error"=> $e->getMessage()]);
    }
    
}

//UPDATE
if($_SERVER['REQUEST_METHOD'] === 'PUT' && $_GET['action'] === 'update') {
    $uniqueKey = $_GET['key'];

    if (!$uniqueKey) {
        echo json_encode(["error" => "Unique key is required"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM regusers_key WHERE unique_key = ?");
    $stmt->execute([$uniqueKey]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(["error" => "Invalid unique key"]);
        exit;
    }

    $userId = $result['id'];

    // read-only stream that allows to read the request body sent to it
    $data = json_decode(file_get_contents('php://input'), true);


    $updateFields = [];
    $params = [];
    foreach ($data as $key => $value) {
        if (in_array($key, ['first_name', 'last_name', 'email', 'salary', 'position', 'date_started', 'gender_id', 'top_size_id'])) {
            $updateFields[] = "$key = ?";
            $params[] = $value;
        }
    }
    $params[] = $userId;

    if (empty($updateFields)) {
        echo json_encode(["error" => "No valid fields to update"]);
        exit;
    }

    // $updateFields[] instead of bindParam=> name=:name, description=:description, email=:email
    $updateQuery = "UPDATE mock_data SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $pdo->prepare($updateQuery);
    
    if ($stmt->execute($params)) {
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update user"]);
    }
}

//DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $_GET['action'] === 'delete') {
    // $userId = $_GET['id'];
    $uniqueKey = $_GET['key'];

    if (!$uniqueKey) {
        echo json_encode(["error" => "Unique key is required"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM regusers_key WHERE unique_key = ?");
    $stmt->execute([$uniqueKey]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(["error" => "Invalid  unique key"]);
        exit;
    }

    $userId = $result['id'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM regusers_key WHERE id = ?");
        $stmt->execute([$userId]);

        $stmt = $pdo->prepare("DELETE FROM mock_data WHERE id = ?");
        $stmt->execute([$userId]);

        $pdo->commit();
        echo json_encode(["message" => "User deleted successfully"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["error" => "Failed to delete user: " . $e->getMessage()]);
    }
}