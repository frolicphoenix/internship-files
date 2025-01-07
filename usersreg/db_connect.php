<?php
$host = 'localhost';
$dbname = 'usersreg';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // PDO::ATTR_ERRMODE: error reporting mode for PDO. 
    // Here, takes PDO::ERRMODE_EXCEPTION which throws PDOException (represents an error raised by PDO)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {

    die("Connection failed: " . $e->getMessage());
}

