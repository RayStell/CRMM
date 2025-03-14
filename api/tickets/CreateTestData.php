<?php
session_start();
require_once '../DB.php';

// Включаем вывод ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Проверяем существование таблицы tickets
try {
    $checkTable = $DB->query("SHOW TABLES LIKE 'tickets'");
    $tableExists = $checkTable->rowCount() > 0;
    
    if (!$tableExists) {
        // Создаем таблицу tickets
        $DB->exec("CREATE TABLE tickets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            clients INT,
            admin INT,
            type VARCHAR(50) DEFAULT 'tech',
            message TEXT,
            status VARCHAR(50) DEFAULT 'waiting',
            file_path TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        echo "Таблица tickets создана<br>";
    } else {
        echo "Таблица tickets уже существует<br>";
    }
    
    // Проверяем существование таблицы ticket_messages
    $checkMessagesTable = $DB->query("SHOW TABLES LIKE 'ticket_messages'");
    $messagesTableExists = $checkMessagesTable->rowCount() > 0;
    
    if (!$messagesTableExists) {
        // Создаем таблицу ticket_messages
        $DB->exec("CREATE TABLE ticket_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ticket_id INT,
            client_id INT,
            admin_id INT,
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        echo "Таблица ticket_messages создана<br>";
    } else {
        echo "Таблица ticket_messages уже существует<br>";
    }
    
    // Получаем ID первого клиента
    $clientQuery = $DB->query("SELECT id FROM clients LIMIT 1");
    $client = $clientQuery->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        echo "Ошибка: Клиенты не найдены<br>";
        exit;
    }
    
    $clientId = $client['id'];
    
    // Получаем ID первого админа
    $adminQuery = $DB->query("SELECT id FROM users LIMIT 1");
    $admin = $adminQuery->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        echo "Ошибка: Администраторы не найдены<br>";
        exit;
    }
    
    $adminId = $admin['id'];
    
    // Проверяем, есть ли уже тестовые данные
    $checkData = $DB->query("SELECT COUNT(*) as count FROM tickets");
    $dataCount = $checkData->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($dataCount == 0) {
        // Создаем тестовые тикеты
        $tickets = [
            [
                'clients' => $clientId,
                'admin' => $adminId,
                'type' => 'tech',
                'message' => 'Не работает принтер, помогите пожалуйста',
                'status' => 'waiting',
                'file_path' => ''
            ],
            [
                'clients' => $clientId,
                'admin' => $adminId,
                'type' => 'crm',
                'message' => 'Не могу создать новый заказ в системе',
                'status' => 'work',
                'file_path' => ''
            ],
            [
                'clients' => $clientId,
                'admin' => $adminId,
                'type' => 'tech',
                'message' => 'Не запускается компьютер после обновления',
                'status' => 'complete',
                'file_path' => ''
            ]
        ];
        
        $ticketStmt = $DB->prepare("INSERT INTO tickets (clients, admin, type, message, status, file_path) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($tickets as $ticket) {
            $ticketStmt->execute([
                $ticket['clients'],
                $ticket['admin'],
                $ticket['type'],
                $ticket['message'],
                $ticket['status'],
                $ticket['file_path']
            ]);
            
            $ticketId = $DB->lastInsertId();
            
            echo "Создан тикет #$ticketId: {$ticket['message']}<br>";
            
            // Добавляем сообщения для тикета
            $messageStmt = $DB->prepare("INSERT INTO ticket_messages (ticket_id, client_id, admin_id, message) VALUES (?, ?, ?, ?)");
            
            // Сообщение от клиента
            $messageStmt->execute([
                $ticketId,
                $clientId,
                null,
                $ticket['message']
            ]);
            
            echo "- Добавлено сообщение от клиента<br>";
            
            // Сообщение от админа (если статус не waiting)
            if ($ticket['status'] !== 'waiting') {
                $adminMessage = 'Мы получили ваше обращение и работаем над решением проблемы.';
                
                $messageStmt->execute([
                    $ticketId,
                    null,
                    $adminId,
                    $adminMessage
                ]);
                
                echo "- Добавлено сообщение от администратора<br>";
            }
            
            // Дополнительное сообщение от админа (если статус complete)
            if ($ticket['status'] === 'complete') {
                $adminMessage = 'Проблема решена. Пожалуйста, проверьте и сообщите, если у вас остались вопросы.';
                
                $messageStmt->execute([
                    $ticketId,
                    null,
                    $adminId,
                    $adminMessage
                ]);
                
                echo "- Добавлено завершающее сообщение от администратора<br>";
            }
        }
        
        echo "Тестовые данные успешно созданы";
    } else {
        echo "Тестовые данные уже существуют ($dataCount записей)";
    }
    
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
} 