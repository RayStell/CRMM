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

// Получаем данные из запроса
$ticket_id = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : null;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
// Добавляем параметр для определения, от кого отправляется сообщение
$sender_type = isset($_POST['sender_type']) ? $_POST['sender_type'] : null;

if (!$ticket_id || empty($message)) {
    echo json_encode(['error' => 'Неверные параметры']);
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
        'userType' => $userType,
        'sender_type' => $sender_type
    ];
    
    // Определяем user_id в зависимости от типа отправителя
    // Если sender_type = 'admin', то сообщение от администратора (user_id = 1)
    // Если sender_type = 'user', то сообщение от пользователя (user_id = 0)
    // Если sender_type не указан, определяем по типу пользователя
    if ($sender_type === 'admin') {
        $user_id = 1; // Администратор
    } else if ($sender_type === 'user') {
        $user_id = 0; // Пользователь
    } else {
        // По умолчанию определяем по типу пользователя
        $user_id = $userType === 'tech' ? 1 : 0;
    }
    
    // Добавляем сообщение
    $query = "INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) 
             VALUES (:ticket_id, :user_id, :message, NOW())";
    $stmt = $DB->prepare($query);
    $result = $stmt->execute([
        ':ticket_id' => $ticket_id,
        ':user_id' => $user_id,
        ':message' => $message
    ]);
    
    $debug['query'] = $query;
    $debug['user_id'] = $user_id;
    
    if ($result) {
        // Обновляем статус тикета на "в работе", если он был "ожидает"
        $updateQuery = "UPDATE tickets SET status = 'work' WHERE id = :ticket_id AND status = 'waiting'";
        $updateStmt = $DB->prepare($updateQuery);
        $updateStmt->execute([':ticket_id' => $ticket_id]);
        
        // Определяем, как отображать сообщение в чате
        $is_admin = $user_id == 1;
        $sender_name = '';
        
        if ($userType === 'tech') {
            // Для админа: его сообщения справа (user), сообщения клиента слева (admin)
            $sender_name = $is_admin ? 'Вы' : 'Клиент';
            // Инвертируем is_admin для правильного отображения в чате
            $is_admin = !$is_admin;
        } else {
            // Для клиента: его сообщения справа (user), сообщения админа слева (admin)
            $sender_name = $is_admin ? 'Администратор' : 'Вы';
        }
        
        // Возвращаем информацию о добавленном сообщении
        $response = [
            'success' => true,
            'message' => [
                'id' => $DB->lastInsertId(),
                'message' => $message,
                'is_admin' => $is_admin,
                'sender_name' => $sender_name,
                'created_at' => date('d.m.Y H:i')
            ],
            'debug' => $debug
        ];
        
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Ошибка при добавлении сообщения', 'debug' => $debug]);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
} 