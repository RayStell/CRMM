<?php
session_start();
require_once '../DB.php';
require_once '../auth/AuthCheck.php';

AuthCheck();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
    exit;
}

$ticket_id = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT);
$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

if (!$ticket_id || !$user_id || !$message) {
    echo json_encode(['success' => false, 'message' => 'Не все поля заполнены']);
    exit;
}

try {
    $stmt = $DB->prepare("INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())");
    $result = $stmt->execute([$ticket_id, $user_id, $message]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Сообщение успешно добавлено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении сообщения']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
} 