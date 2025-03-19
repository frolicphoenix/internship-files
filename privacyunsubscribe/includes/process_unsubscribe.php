<?php
session_start();
require_once __DIR__ . '/db.php';

// Enable error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }

        // Get and validate PUB code
        $pub = strtoupper(filter_input(INPUT_POST, 'pub', FILTER_SANITIZE_STRING));
        if (!$pub || !preg_match('/^[A-Z]{2,5}$/', $pub)) {
            throw new Exception('Invalid PUB code format');
        }

        // Sanitize inputs
        $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);

        // Validate inputs
        if (!preg_match('/^[A-Za-z \-\']{2,50}$/', $first_name)) {
            throw new Exception('Invalid first name');
        }
        
        if (!preg_match('/^[A-Za-z \-\']{2,50}$/', $last_name)) {
            throw new Exception('Invalid last name');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }

        // Validate country against CSV
        $countries = array_map('str_getcsv', file('country.csv'));
        $valid_countries = [];
        foreach ($countries as $row) {
            if ($row[0] === 'id') continue;
            $valid_countries[$row[0]] = $row[1];
        }
        
        if (!array_key_exists($country, $valid_countries)) {
            throw new Exception('Invalid country selected');
        }

        // Database insertion
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("INSERT INTO unsubscribe 
            (first_name, last_name, email, country, pub, date)
            VALUES (:first_name, :last_name, :email, :country, :pub, NOW())");
            
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':country' => $country,
            ':pub' => $pub
        ]);

        // Success response
        $_SESSION['success_message'] = "Thank you! Your email will be removed from the subscription list within 2 business days!";
        header("Location: ../unsubscribe_form.php?pub=" . urlencode(strtoupper($pub)));
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['form_data'] = $_POST;
        header("Location: ../unsubscribe_form.php?pub=" . urlencode(strtoupper($pub)));
        exit();
    }
} else {
    header("Location: ../unsubscribe_form.php");
    exit();
}
