<?php
function convertDate($date) {
    return date('d.m.Y', strtotime($date));
}

// добавить отображение имени администратора

function OutputOrders($orders) {
    foreach ($orders as $key => $order) {
        $status = isset($order['status']) ? $order['status'] : 'Хз';
        $fullname = $order['name'] ?? 'Неизвестно';
        $order_date = $order['order_date'] ? date('Y-m-d H:i:s', strtotime($order['order_date'])) : 'Неизвестно';
        $total_price = $order['total'] ?? '0';
        $order_items = $order['product_names'] ?? 'Нет данных';
        $id = $order['id'];
        $admin_name = $order['admin_name'] ?? 'Не назначен';

        echo "<tr>";
        echo "<td>{$order['id']}</td>";
        echo "<td>{$admin_name}</td>";
        echo "<td>{$fullname}</td>";
        echo "<td>{$order_date}</td>";
        echo "<td>{$total_price}</td>";
        echo "<td>{$order_items}</td>";
        echo "<td>{$status}</td>";
        echo "<td> <a href='api/orders/generateCheack.php?id=$id'><i class='fa fa-qrcode'></i></a></td>";
        echo "<td onclick=\"MicroModal.show('edit-modal')\"><i class='fa fa-pencil'></i></td>";
        echo "<td><a href='api/orders/OrdersDelete.php?id={$order['id']}'><i class='fa fa-trash'></i></a></td>";
        echo "</tr>";
    }
}
?>