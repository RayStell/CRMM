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

try {
    // Получаем тип пользователя
    $userType = getUserType($DB);
    
    // Проверяем структуру таблицы tickets
    $columnsQuery = "SHOW COLUMNS FROM tickets";
    $columnsStmt = $DB->query($columnsQuery);
    $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Выводим отладочную информацию о структуре таблицы
    $debug = [
        'token' => $token,
        'userType' => $userType,
        'columns' => $columns
    ];
    
    // Получаем обращения с учетом структуры таблицы
    $query = "SELECT t.* FROM tickets t ORDER BY t.created_at DESC";
    
    $stmt = $DB->prepare($query);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Добавляем информацию о запросе в отладку
    $debug['query'] = $query;
    $debug['tickets_count'] = count($tickets);
    
    // Преобразуем данные для вывода
    $result = [];
    foreach ($tickets as $ticket) {
        $statusText = [
            'waiting' => 'Ожидает',
            'work' => 'В работе',
            'complete' => 'Выполнено'
        ][$ticket['status'] ?? 'waiting'];

        $statusIcon = [
            'waiting' => 'clock-o',
            'work' => 'cog',
            'complete' => 'check'
        ][$ticket['status'] ?? 'waiting'];
        
        $typeText = ($ticket['type'] ?? 'tech') === 'tech' ? 'Техническая неполадка' : 'Проблема с CRM';
        
        // Обрабатываем файлы
        $files = [];
        if (!empty($ticket['file_path'])) {
            $filesPaths = explode('|', $ticket['file_path']);
            foreach ($filesPaths as $filePath) {
                if (empty($filePath)) continue;
                
                $filePath = ltrim($filePath, '/');
                $fileName = basename($filePath);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $fileIcon = 'file-o';
                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $fileIcon = 'file-image-o';
                    $files[] = [
                        'path' => $filePath,
                        'name' => $fileName,
                        'icon' => $fileIcon,
                        'type' => 'image'
                    ];
                }
            }
        }
        
        $result[] = [
            'id' => $ticket['id'],
            'type' => $ticket['type'] ?? 'tech',
            'typeText' => $typeText,
            'message' => $ticket['message'] ?? '',
            'status' => $ticket['status'] ?? 'waiting',
            'statusText' => $statusText,
            'statusIcon' => $statusIcon,
            'created_at' => !empty($ticket['created_at']) ? date('d.m.Y H:i', strtotime($ticket['created_at'])) : date('d.m.Y H:i'),
            'admin_name' => 'Администратор',
            'client_name' => 'Клиент',
            'files' => $files
        ];
    }
    
    echo json_encode(['success' => true, 'tickets' => $result, 'debug' => $debug]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
} 