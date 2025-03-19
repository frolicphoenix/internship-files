<?php

function get_countries() {
    $countries = [];

    $csv_path = __DIR__ . '/country.csv';

    if(($handle = fopen($csv_path,'r')) !== FALSE) {
        fgetcsv($handle);

        while (($data =fgetcsv($handle)) !== FALSE) {
            if(count($data) >= 2) {
                $countries[trim($data[0])] = trim($data[1]);
            }
        }
        fclose($handle);
    } else {
        error_log('Failed to get countries');
    }
    return $countries;
}