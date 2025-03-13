<?php
function convertDate($date) {
    return date('d.m.Y', strtotime($date));
}

function OutputOrders($orders) {
    foreach ($orders as $key => $order) {
        $status = isset($order['status']) ? $order['status'] : '0';
        $client_name = $order['client_name'] ?? 'Неизвестно';
        $order_date = $order['order_date'] ? date('Y-m-d H:i:s', strtotime($order['order_date'])) : 'Неизвестно';
        $total_price = $order['total'] ?? '0';
        $order_items = $order['product_names'] ?? 'Нет данных';
        $id = $order['id'];
        $admin_name = $order['admin_name'] ?? 'Не назначен';

        echo "<tr>";
        echo "<td>№{$order['id']}</td>";
        echo "<td>{$admin_name}</td>";
        echo "<td>{$client_name}</td>";
        echo "<td>{$order_date}</td>";
        echo "<td>{$total_price}₽</td>";
        echo "<td>{$order_items}</td>";
        echo "<td>" . ($status == '1' ? 'Активный' : 'Неактивный') . "</td>";
        echo "<td><a href='api/orders/generateCheack.php?id=$id'><i class='fa fa-qrcode'></i></a></td>";
        echo "<td onclick=\"editOrder('$id', '$status')\"><i class='fa fa-pencil'></i></td>";
        echo "<td><a href='api/orders/OrdersDelete.php?id={$order['id']}'><i class='fa fa-trash'></i></a></td>";
        echo "</tr>";
    }
}
?>