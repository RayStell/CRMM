<?php session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = $_POST;
    $fields = ['client', 'products'];
    $errors = [];

    if ($formData['client'] === 'new') {
        $fields[] = 'email';
    }
    
    $_SESSION['orders_error'] = '';

    foreach ($fields as $field) {
        if (!isset($formData[$field]) || empty($_POST[$field])) {
            $errors[$field][] = 'Gavno ne rabotaet';
        }
    }

    if (!empty($errors)) {
        $errorList = '<ul>';
        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                $errorList .= '<li>' . ucfirst($field) . ': ' . $message . '</li>';
            }
        }
        $errorList .= '</ul>';
        
        $_SESSION['orders_error'] = $errorList;
        header('Location: ../../orders.php');
        exit;
    }

    require_once '../DB.php';
    //ид выбранных товаров
    $products = $formData['products'];
    
    //получаем сумму выбранных товаров через SQL
    $placeholders = str_repeat('?,', count($products) - 1) . '?';
    $stmt = $DB->prepare("SELECT SUM(price) as total FROM products WHERE id IN ($placeholders)");
    $stmt->execute($products);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $result['total'];

    //все товары из бд (нужны для последующего использования)
    $allProducts = $DB->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

    $clientID = $formData['client'] === 'new' ? time() : $formData['client'];

    if ($formData['client'] === 'new') {
        $stmt = $DB->prepare("INSERT INTO clients (id, name, email, phone, birthday) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $clientID,
            $formData['name'] ?? 'не указано',
            $formData['email'],
            'не указано', // значение по умолчанию для phone
            'не указано'  // значение по умолчанию для birthday
        ]);
    }

    $stmt = $DB->prepare("SELECT id FROM users WHERE token = ?");
    $stmt->execute([$_SESSION['token']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $adminID = $result['id'];

    // 1. создание заказа с полями
    $orders = [
        'id' => time(),
        'client_id' => $clientID,
        'total' => $total,
        'admin' => $adminID,
    ];

    $stmt = $DB->prepare(
        "INSERT INTO orders (id, client_id, total, admin) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $orders['id'],
        $orders['client_id'],
        $orders['total'],
        $orders['admin'],
    ]);

    // 2. Добавление элементов заказа в order_items
    $stmt = $DB->prepare(
        "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)"
    );

    foreach ($products as $product_id) {
        // Находим информацию о продукте
        $productInfo = array_filter($allProducts, function($p) use ($product_id) {
            return $p['id'] == $product_id;
        });
        $productInfo = reset($productInfo);

        // Добавляем запись в order_items
        $stmt->execute([
            $orders['id'],      // order_id
            $product_id,        // product_id
            1,                  // quantity (по умолчанию 1)
            $productInfo['price'] // price
        ]);
    }

    // Редирект на страницу заказов после успешного создания
    header('Location: ../../orders.php');
    exit;

} else {
    echo json_encode(['error' => 'Invalid method']);
    exit;
}

?>