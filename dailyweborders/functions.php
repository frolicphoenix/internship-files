<?php

function getSearchTerm() {
    return isset($_GET['search']) ? $_GET['search'] : '';
}

function getStartDate() {
    return isset($_GET['start_date']) ? $_GET['start_date'] : '';
}

function getEndDate() {
    return isset($_GET['end_date']) ? $_GET['end_date'] : '';
}

function getSortColumn() {
    $allowedColumns = ['Email', 'OrderDateFM', 'SoldBy'];
    $column = isset($_GET['sort']) ? $_GET['sort'] : 'OrderDateFM';
    return in_array($column, $allowedColumns) ? $column : 'OrderDateFM';
}

function getSortOrder() {
    return isset($_GET['order']) && (strtoupper($_GET['order']) == 'ASC' || strtoupper($_GET['order']) == 'DESC') ? strtoupper($_GET['order']) : 'DESC';
}

function getSignupListURL($column, $order) {
    $url = 'signup_list.php?sort=' . urlencode($column) . '&order=' . urlencode($order);

    // Preserve the search links
    $searchTerm = getSearchTerm();
    if (!empty($searchTerm)) {
        $url .= '&search=' . urlencode($searchTerm);
    }

    $startDate = getStartDate();
    if(!empty($startDate)) {
        $url .= '&start_date=' . urlencode($startDate);
    }

    $endDate = getEndDate();
    if(!empty($endDate)) {
        $url .= '&end_date=' . urlencode($endDate);
    }


    return $url;
}

function getUserHistoryURL($column, $currentSortColumn, $currentSortOrder, $email) {
    $sort = $column;
    $order = 'ASC';

    if ($column == $currentSortColumn) {
        $order = ($currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
    }

     $url = 'user_history.php?email=' . urlencode($email) . '&sort=' . urlencode($sort) . '&order=' . urlencode($order);

    return $url;
}


function isValidColumn($column) {
    $allowedColumns = ['Email', 'SoldBy', 'OrderDateFM'];
    return in_array($column, $allowedColumns);
}
?>
