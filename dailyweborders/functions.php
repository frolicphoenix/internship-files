<?php

function getSearchTerm() {
    return isset($_GET['search']) ? $_GET['search'] : '';
}

function getSortColumn() {
    $allowedColumns = ['Email', 'OrderDateFM', 'SoldBy'];
    $column = isset($_GET['sort']) ? $_GET['sort'] : 'OrderDateFM';
    return in_array($column, $allowedColumns) ? $column : 'OrderDateFM';
}

function getSortOrder() {
    return isset($_GET['order']) && (strtoupper($_GET['order']) == 'ASC' || strtoupper($_GET['order']) == 'DESC') ? strtoupper($_GET['order']) : 'DESC';
}

function getSignupListURL($column, $currentSortColumn, $currentSortOrder) {
    $sort = $column;
    $order = 'ASC';

    if ($column == $currentSortColumn) {
        $order = ($currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
    }

    $url = 'signup_list.php?sort=' . urlencode($sort) . '&order=' . urlencode($order);

    //preserve the search term
    $searchTerm = getSearchTerm();
    if (!empty($searchTerm)) {
        $url .= '&search=' . urlencode($searchTerm);
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
