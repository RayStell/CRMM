<?php
session_start();

require_once __DIR__ . '/../DB.php';
require_once __DIR__ . '/../auth/AuthCheck.php';

if (!AuthCheck('', '../../login.php', $DB)) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

    if ($id > 0 && !empty($name) && !empty($description) && $price >= 0 && $stock >= 0) {
        try {
            $stmt = $DB->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $stock, $id]);
            
            header('Location: ../../product.php?success=updated');
            exit;
        } catch (PDOException $e) {
            header('Location: ../../product.php?error=update_failed');
            exit;
        }
    } else {
        header('Location: ../../product.php?error=invalid_data');
        exit;
    }
}

header('Location: ../../product.php');
exit;
?> 