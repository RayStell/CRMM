<?php
session_start();

// Добавить запись обращения в БД
// client = id текущего пользователя
// admin = пустое значение

// Включаем вывод ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подключаемся к базе данных
require_once '../DB.php';

// Выводим информацию о POST-данных для отладки
echo "POST данные: ";
var_dump($_POST);

// Получаем данные из формы
$type = $_POST['support-type'] ?? '';
$message = $_POST['support-message'] ?? '';

// Определяем ID клиента
// Если пользователь авторизован, берем ID из сессии, иначе используем значение по умолчанию (например, 1)
$client_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

echo "Тип: " . $type . "<br>";
echo "Сообщение: " . $message . "<br>";
echo "ID клиента: " . $client_id . "<br>";

// Проверяем, что все необходимые данные получены
if (empty($message)) {
    echo "Ошибка: Пустое сообщение";
    exit;
}

// Загрузка файла, если он был прикреплен
$file_path = null;
if (isset($_FILES['files']) && $_FILES['files']['error'] == 0) {
    $upload_dir = '../../uploads/tickets/';
    
    // Создаем директорию, если она не существует
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = time() . '_' . $_FILES['files']['name'];
    $file_path = $upload_dir . $file_name;
    
    // Перемещаем загруженный файл
    move_uploaded_file($_FILES['files']['tmp_name'], $file_path);
    
    // Сохраняем только относительный путь в БД
    $file_path = 'uploads/tickets/' . $file_name;
}

try {
    // Проверяем, существует ли таблица tickets
    $checkTable = $DB->query("SHOW TABLES LIKE 'tickets'");
    if ($checkTable->rowCount() == 0) {
        // Таблица не существует, создаем её
        $DB->exec("
            CREATE TABLE tickets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                clients INT NOT NULL,
                admin INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "Таблица tickets создана<br>";
    }
    
    // Подготавливаем и выполняем запрос на добавление тикета
    $query = "INSERT INTO tickets (type, message, clients, admin) VALUES (:type, :message, :clients, :admin)";
    
    $stmt = $DB->prepare($query);
    
    $params = [
        ':type' => $type,
        ':message' => $message,
        ':clients' => $client_id,
        ':admin' => 1
    ];
    
    $result = $stmt->execute($params);
    
    if ($result) {
        $lastId = $DB->lastInsertId();
        echo "Тикет успешно создан с ID: " . $lastId;
        
        // Перенаправляем пользователя обратно с сообщением об успехе
        // header('Location: ../../clients.php?success=ticket_created');
        // exit;
    } else {
        echo "Ошибка при выполнении запроса. Информация:";
        print_r($stmt->errorInfo());
    }
} catch (PDOException $e) {
    echo "Ошибка PDO: " . $e->getMessage();
    echo "<br>Код ошибки: " . $e->getCode();
    exit;
} catch (Exception $e) {
    echo "Общая ошибка: " . $e->getMessage();
    exit;
}
?>