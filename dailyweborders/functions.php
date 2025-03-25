<?php

// Retrieves the 'search' parameter from the URL query string
function getSearchTerm() {
    return isset($_GET['search']) ? $_GET['search'] : '';
}

// Retrieves the 'start_date' parameter from the URL query string
function getStartDate() {
    return isset($_GET['start_date']) ? $_GET['start_date'] : '';
}

// Retrieves the 'end_date' parameter from the URL query string
function getEndDate() {
    return isset($_GET['end_date']) ? $_GET['end_date'] : '';
}

// Gets the sort column from the URL query string, ensuring it is one of the allowed columns
// Defaults to 'OrderDateFM' if the provided column is not allowed or not set
function getSortColumn() {
    $allowedColumns = ['Email', 'OrderDateFM', 'SoldBy'];
    $column = isset($_GET['sort']) ? $_GET['sort'] : 'OrderDateFM';
    return in_array($column, $allowedColumns) ? $column : 'OrderDateFM';
}

// Gets the sort order from the URL query string, ensuring either 'ASC' or 'DESC'
function getSortOrder() {
    return isset($_GET['order']) && (strtoupper($_GET['order']) == 'ASC' || strtoupper($_GET['order']) == 'DESC') 
           ? strtoupper($_GET['order']) 
           : 'DESC';
}

// Constructs the URL for the signup list page with sorting and filtering parameters
// It includes the sort column, sort order, and any search or date filters if provided
function getSignupListURL($column, $order) {
    $url = 'signup_list.php?sort=' . urlencode($column) . '&order=' . urlencode($order);

    // Append search term if available.
    $searchTerm = getSearchTerm();
    if (!empty($searchTerm)) {
        $url .= '&search=' . urlencode($searchTerm);
    }

    // Append start date if available.
    $startDate = getStartDate();
    if (!empty($startDate)) {
        $url .= '&start_date=' . urlencode($startDate);
    }

    // Append end date if available.
    $endDate = getEndDate();
    if (!empty($endDate)) {
        $url .= '&end_date=' . urlencode($endDate);
    }

    return $url;
}

// Constructs the URL for the user history page with sorting options
// It toggles the sort order if the same column is requested again
function getUserHistoryURL($column, $currentSortColumn, $currentSortOrder, $email) {
    $sort = $column;
    $order = 'ASC'; // Default

    // If the requested sort column is already active, toggle the sort order
    if ($column == $currentSortColumn) {
        $order = ($currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
    }

    $url = 'user_history.php?email=' . urlencode($email) . '&sort=' . urlencode($sort) . '&order=' . urlencode($order);
    return $url;
}

// Checks if the provided column is valid by comparing it against a list of allowed columns
function isValidColumn($column) {
    $allowedColumns = ['Email', 'SoldBy', 'OrderDateFM'];
    return in_array($column, $allowedColumns);
}

// Attempts to parse a raw date string in various formats and returns a DateTime object if successful
// Supports formats: YYYYMMDD, MM/DD/YYYY, and YYYY-MM-DD. Returns false if none match
function parseOrderDate($raw) {
    $raw = trim($raw);
    if (preg_match('/^\d{8}$/', $raw)) {
        // Format: YYYYMMDD
        return DateTime::createFromFormat('Ymd', $raw);
    } elseif (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $raw)) {
        // Format: MM/DD/YYYY (or M/D/YYYY)
        return DateTime::createFromFormat('m/d/Y', $raw);
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
        // Format: YYYY-MM-DD
        return DateTime::createFromFormat('Y-m-d', $raw);
    }
    return false;
}

// Formats a DateTime object into a 'Y-m-d' string; returns 'Invalid Date' if the input is not a valid DateTime
function formatDate($dateObj) {
    return $dateObj instanceof DateTime ? $dateObj->format('Y-m-d') : 'Invalid Date';
}

?>
