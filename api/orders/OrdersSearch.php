<?php

function OrdersSearch($params, $DB) {
    $search = isset($params['search']) ? $params['search'] : '';
    //по умолчанию и убыванию
    $sort = isset($params['sort']) ? $params['sort'] : '0';
    //цена и количество
    $search_name = isset($params['search_name']) ? $params['search_name'] : '0';
    $search_status = isset($params['search_status']) ? $params['search_status'] : '0';
    $search = strtolower($search);

    $orderBy = '';
    if ($sort == '1') {
        $orderBy = "ORDER BY $search_name ASC";
    } elseif ($sort == '2') {
        $orderBy = "ORDER BY $search_name DESC";
    }

    $orders = $DB->query(
    "SELECT
        orders.id,
        clients.name,
        orders.order_date,
        orders.total,
        GROUP_CONCAT(CONCAT(products.name,' ( ',order_items.quantity,'шт. : ',products.price,')') 
        SEPARATOR ', ') AS product_names,
        orders.status,
        users.name AS admin_name
    FROM
        orders
    JOIN
        clients ON orders.client_id = clients.id
    JOIN
        users ON orders.admin = users.id
    JOIN
        order_items ON orders.id = order_items.order_id
    JOIN
        products ON order_items.product_id = products.id
    WHERE LOWER(clients.name) LIKE '%$search%' OR LOWER(products.name) LIKE '%$search%'
    GROUP BY
        orders.id, clients.name, orders.order_date, orders.total, orders.status
    " . $orderBy)->fetchAll();

    return $orders;
}

?>
