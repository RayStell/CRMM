<?php
session_start();
require_once '../DB.php';
require_once '../helpers/getUserType.php';

// Проверяем, что пользователь является техподдержкой
$userType = getUserType($DB);
if ($userType !== 'tech') {
    echo json_encode(['error' => 'Доступ запрещен']);
    exit;
}

// Получаем данные
$ticket_id = $_POST['ticket_id'] ?? null;
$new_status = $_POST['status'] ?? null;

if (!$ticket_id || !$new_status) {
    echo json_encode(['error' => 'Неверные параметры']);
    exit;
}

// Проверяем допустимые статусы
$allowed_statuses = ['waiting', 'work', 'complete'];
if (!in_array($new_status, $allowed_statuses)) {
    echo json_encode(['error' => 'Недопустимый статус']);
    exit;
}

try {
    $stmt = $DB->prepare("UPDATE tickets SET status = :status WHERE id = :id");
    $result = $stmt->execute([
        ':status' => $new_status,
        ':id' => $ticket_id
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Ошибка обновления']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 