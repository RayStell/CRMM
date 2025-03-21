<?php

function OrdersSearch($GET, $DB) {
    $per_page = 5; // Количество записей на странице

    // Базовый запрос для подсчета общего количества записей
    $count_query = "
        SELECT COUNT(DISTINCT o.id) as total_count
        FROM orders o
        LEFT JOIN clients c ON o.client_id = c.id
        LEFT JOIN users u ON o.admin = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products pr ON oi.product_id = pr.id
    ";

    // Добавляем условия поиска если они есть
    $conditions = [];
    
    if (isset($GET['search']) && !empty($GET['search'])) {
        $search = $GET['search'];
        if (isset($GET['search_name']) && $GET['search_name'] === 'client') {
            $conditions[] = "c.name LIKE '%$search%'";
        } elseif (isset($GET['search_name']) && $GET['search_name'] === 'admin') {
            $conditions[] = "u.name LIKE '%$search%'";
        }
    }

    if (!empty($conditions)) {
        $count_query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Получаем общее количество записей
    $total_count = $DB->query($count_query)->fetch(PDO::FETCH_ASSOC)['total_count'];
    
    // Вычисляем максимальное количество страниц
    $max_pages = ceil($total_count / $per_page);
    
    // Проверяем и корректируем номер текущей страницы
    $page = isset($GET['page']) ? (int)$GET['page'] : 1;
    $page = max(1, min($page, $max_pages)); // Убеждаемся, что страница в допустимых пределах
    
    $offset = ($page - 1) * $per_page;

    // Основной запрос для получения данных
    $query = "
        SELECT 
            o.id,
            o.status,
            o.total as total,
            o.original_total,
            o.promo_code,
            o.order_date,
            c.name as client_name,
            u.name as admin_name,
            p.discount,
            GROUP_CONCAT(CONCAT(pr.name, ' (', oi.quantity, 'шт.)') SEPARATOR ', ') as product_names
        FROM orders o
        LEFT JOIN clients c ON o.client_id = c.id
        LEFT JOIN users u ON o.admin = u.id
        LEFT JOIN promotions p ON o.promo_code = p.code_promo
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products pr ON oi.product_id = pr.id
    ";

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " GROUP BY o.id";

    // Добавляем сортировку
    if (isset($GET['sort']) && $GET['sort'] !== '0') {
        $query .= " ORDER BY o.order_date " . ($GET['sort'] === '1' ? 'ASC' : 'DESC');
    } else {
        $query .= " ORDER BY o.order_date DESC";
    }

    // Добавляем LIMIT и OFFSET для пагинации
    $query .= " LIMIT $per_page OFFSET $offset";

    return $DB->query($query)->fetchAll();
}

?>
