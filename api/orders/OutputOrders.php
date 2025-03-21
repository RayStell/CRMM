<?php
function convertDate($date) {
    return date('d.m.Y', strtotime($date));
}

function OutputOrders($orders) {
    foreach ($orders as $order) {
        $status = $order['status'] == 1 ? 'Активный' : 'Неактивный';
        
        echo "<tr>";
        echo "<td>" . $order['id'] . "</td>";
        echo "<td>" . $order['admin_name'] . "</td>";
        echo "<td>" . ($order['client_name'] ?? 'не указано') . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', strtotime($order['order_date'])) . "</td>";
        echo "<td>" . $order['total'] . "₽</td>";
        echo "<td><i class='fa fa-eye'></i></td>";
        echo "<td>" . $status . "</td>";
        echo "<td><a href='api/orders/generateCheack.php?id=" . $order['id'] . "'><i class='fa fa-qrcode'></i></a></td>";
        echo "<td onclick=\"editOrder('" . $order['id'] . "', '" . $order['status'] . "')\"><i class='fa fa-pencil'></i></td>";
        echo "<td><a href='api/orders/OrdersDelete.php?id=" . $order['id'] . "'><i class='fa fa-trash'></i></a></td>";
        echo "</tr>";
    }
}
?>