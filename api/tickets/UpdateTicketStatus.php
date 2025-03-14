<?php
session_start();
require_once '../DB.php';
require_once '../helpers/getUserType.php';

// Включаем вывод ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Получаем токен пользователя из сессии
$token = isset($_SESSION['token']) ? $_SESSION['token'] : null;

if (!$token) {
    echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
    exit;
}

// Проверяем, что пользователь является администратором
$userType = getUserType($DB);
if ($userType !== 'tech') {
    echo json_encode(['success' => false, 'error' => 'Недостаточно прав для выполнения операции']);
    exit;
}

// Получаем данные из запроса
$ticket_id = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;

// Проверяем корректность данных
if (!$ticket_id || !$status) {
    echo json_encode(['success' => false, 'error' => 'Неверные параметры']);
    exit;
}

// Проверяем, что статус имеет допустимое значение
$allowedStatuses = ['waiting', 'work', 'complete'];
if (!in_array($status, $allowedStatuses)) {
    echo json_encode(['success' => false, 'error' => 'Недопустимый статус']);
    exit;
}

try {
    // Проверяем существование тикета
    $checkQuery = "SELECT id FROM tickets WHERE id = :ticket_id";
    $checkStmt = $DB->prepare($checkQuery);
    $checkStmt->execute([':ticket_id' => $ticket_id]);
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'error' => 'Обращение не найдено']);
        exit;
    }
    
    // Обновляем статус тикета
    $updateQuery = "UPDATE tickets SET status = :status WHERE id = :ticket_id";
    $updateStmt = $DB->prepare($updateQuery);
    $result = $updateStmt->execute([
        ':status' => $status,
        ':ticket_id' => $ticket_id
    ]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении статуса']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 