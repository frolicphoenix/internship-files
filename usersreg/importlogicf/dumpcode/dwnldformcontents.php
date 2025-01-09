<?php

$data = array(
    $_POST['name'],
    $_POST['email'],
    $_POST['phone']
);

// open file in append mode
$fp = fopen('database.csv', 'a');

fputcsv($fp, $data);

fclose($fp);