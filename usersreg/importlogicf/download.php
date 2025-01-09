<?php

session_start();
require_once __DIR__ . '/../db_connect.php';

$downloadDir = "downloads/";

if(!file_exists($downloadDir)) {
    mkdir($downloadDir, 0777, true);
}

if (isset($_SERVER['REMOTE_ADDR'])) {
    die(':)');
}
echo "running.";