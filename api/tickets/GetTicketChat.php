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
    echo json_encode(['error' => 'Пользователь не авторизован']);
    exit;
}

// Получаем ID тикета из запроса
$ticket_id = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : null;

if (!$ticket_id) {
    echo json_encode(['error' => 'Не указан ID обращения']);
    exit;
}

try {
    // Проверяем существование тикета
    $checkQuery = "SELECT id FROM tickets WHERE id = :ticket_id";
    $checkStmt = $DB->prepare($checkQuery);
    $checkStmt->execute([':ticket_id' => $ticket_id]);
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Обращение не найдено']);
        exit;
    }
    
    // Получаем тип пользователя
    $userType = getUserType($DB);
    
    // Проверяем структуру таблицы ticket_messages
    $columnsQuery = "SHOW COLUMNS FROM ticket_messages";
    $columnsStmt = $DB->query($columnsQuery);
    $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Выводим отладочную информацию о структуре таблицы
    $debug = [
        'columns' => $columns,
        'userType' => $userType
    ];
    
    // Получаем сообщения чата с учетом структуры таблицы
    $query = "SELECT m.* FROM ticket_messages m 
             WHERE m.ticket_id = :ticket_id
             ORDER BY m.created_at ASC";
    
    $stmt = $DB->prepare($query);
    $stmt->execute([':ticket_id' => $ticket_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Преобразуем данные для вывода
    $result = [];
    foreach ($messages as $message) {
        // Определяем, от кого сообщение (админ или клиент)
        $isAdmin = isset($message['user_id']) && $message['user_id'] == 1;
        
        // Если текущий пользователь - администратор, то меняем логику отображения
        if ($userType === 'tech') {
            // Для админа: его сообщения справа (user), сообщения клиента слева (admin)
            $result[] = [
                'id' => $message['id'],
                'message' => $message['message'],
                'is_admin' => !$isAdmin, // Инвертируем для админа
                'sender_name' => $isAdmin ? 'Вы' : 'Клиент',
                'created_at' => date('d.m.Y H:i', strtotime($message['created_at']))
            ];
        } else {
            // Для клиента: его сообщения справа (user), сообщения админа слева (admin)
            $result[] = [
                'id' => $message['id'],
                'message' => $message['message'],
                'is_admin' => $isAdmin,
                'sender_name' => $isAdmin ? 'Администратор' : 'Вы',
                'created_at' => date('d.m.Y H:i', strtotime($message['created_at']))
            ];
        }
    }
    
    echo json_encode(['success' => true, 'messages' => $result, 'debug' => $debug]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
} 