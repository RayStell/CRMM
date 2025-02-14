<?php

function ClientsSearch($params, $DB) {
    $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';
    $search = isset($params['search']) ? $params['search'] : '';
    $sort = isset($params['sort']) ? $params['sort'] : '0';

    $search = strtolower($search);

    $orderBy = '';
    if ($sort == '1') {
        $orderBy = " ORDER BY $search_name ASC";
    } elseif ($sort == '2') {
        $orderBy = " ORDER BY $search_name DESC";
    }

    $clients = $DB->query(
        "SELECT * FROM clients WHERE LOWER($search_name) LIKE '%$search%'$orderBy"
    )->fetchAll();

    return $clients;
}

?>
