<?php

$list = array (
    ['Name', 'age', 'Gender'],
    ['Ash', '15', 'Male'],
    ['Misty', '14', 'Female'],
    ['Brock', '21', 'Male']
);

$fp = fopen('downloads/persons.csv', 'w');

foreach($list as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);