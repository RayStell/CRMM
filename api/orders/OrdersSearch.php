<?php

function OrdersSearch($params, $DB) {
    $search = isset($params['search']) ? $params['search'] : '';
    //по умолчанию и убыванию
    $sort = isset($params['sort']) ? $params['sort'] : '0';
    //цена и количество
    $search_name = isset($params['search_name']) ? $params['search_name'] : '0';
    $search_status = isset($_SESSION['search_status']) ? $_SESSION['search_status'] : 'all';
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

    // Формируем WHERE с учетом поиска только если поисковая строка не пустая
    $whereClause = "";
    if (!empty($search)) {
        $whereClause = "WHERE (LOWER(clients.name) LIKE '%$search%' OR LOWER(products.name) LIKE '%$search%')";
    }

    // Добавляем условие статуса
    if ($search_status == '0') {  // Неактивные заказы (status = 0)
        $whereClause = $whereClause ? $whereClause . " AND orders.status = '0'" : "WHERE orders.status = '0'";
    } elseif ($search_status == '1') {  // Активные заказы (status = 1)
        $whereClause = $whereClause ? $whereClause . " AND orders.status = '1'" : "WHERE orders.status = '1'";
    } 
    // Для значения "0" (Все заказы) дополнительные условия не добавляются

    $orders = $DB->query(
    "SELECT
        orders.id,
        clients.name,
        orders.order_date,
        orders.total,
        GROUP_CONCAT(CONCAT(products.name,' ( ',order_items.quantity,'шт. : ',products.price,')') 
        SEPARATOR ', ') AS product_names,
        CASE 
            WHEN orders.status = '1' THEN 'Активный'
            WHEN orders.status = '0' THEN 'Неактивный'
        END AS status,
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
