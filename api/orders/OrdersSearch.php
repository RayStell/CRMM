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

    // Добавляем параметр для текущей страницы
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $per_page = 5; // Количество записей на странице
    $offset = ($page - 1) * $per_page;

    // Добавляем условие WHERE для фильтрации по статусу
    $whereClause = "WHERE (LOWER(clients.name) LIKE '%$search%' OR LOWER(products.name) LIKE '%$search%')";
    if ($search_status == '1') {  // Активные заказы
        $whereClause .= " AND orders.status = '1'";
    } elseif ($search_status == '2') {  // Неактивные заказы
        $whereClause .= " AND orders.status = '0'";
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
    " . $whereClause . "
    GROUP BY
        orders.id, clients.name, orders.order_date, orders.total, orders.status
    " . $orderBy . "
    LIMIT $per_page OFFSET $offset")->fetchAll();

    return $orders;
}

?>
