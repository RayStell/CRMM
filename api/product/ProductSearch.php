<?php

function ProductSearch($params, $DB) {
    $search = isset($params['search']) ? $params['search'] : '';
    //по умолчанию и убыванию
    $sort = isset($params['sort']) ? $params['sort'] : '0';
    //цена и количество
    $search_name = isset($params['search_name']) ? $params['search_name'] : '0';

    $search = strtolower($search);

    $orderBy = '';
    if ($sort == '1') {
        $orderBy = "ORDER BY $search_name ASC";
    } elseif ($sort == '2') {
        $orderBy = "ORDER BY $search_name DESC";
    }

    $product = $DB->query(
        "SELECT * FROM products WHERE LOWER(name) LIKE '%$search%'$orderBy"
    )->fetchAll();

    return $product;
}

?>
