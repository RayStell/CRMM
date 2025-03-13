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
$type = $_POST['type'] ?? '';
$message = $_POST['message'] ?? '';

// Определяем ID клиента
// Если пользователь авторизован, берем ID из сессии, иначе используем значение по умолчанию (например, 1)
$client_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

echo "Тип: " . $type . "<br>";
echo "Сообщение: " . $message . "<br>";
echo "ID клиента: " . $client_id . "<br>";

// Проверяем, что все необходимые данные получены
if (empty($message) || empty($type)) {
    echo "Ошибка: Заполните все обязательные поля";
    exit;
}

// Обработка загруженного файла
$file_path = null;
if (isset($_FILES['files']) && $_FILES['files']['error'][0] != UPLOAD_ERR_NO_FILE) {
    $upload_dir = '../../uploads/tickets/';
    $web_upload_dir = '/uploads/tickets/'; // Путь для веб-доступа
    
    // Создаем директорию, если она не существует
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $uploaded_files = [];
    $files = $_FILES['files'];
    
    // Перебираем все загруженные файлы
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] == UPLOAD_ERR_OK) {
            $tmp_name = $files['tmp_name'][$i];
            $original_name = $files['name'][$i];
            
            // Генерируем уникальное имя файла
            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $original_name);
            $server_path = $upload_dir . $file_name;
            $web_path = $web_upload_dir . $file_name;
            
            // Перемещаем файл
            if (move_uploaded_file($tmp_name, $server_path)) {
                $uploaded_files[] = $web_path;
            }
        }
    }
    
    // Объединяем пути к файлам через разделитель
    $file_path = !empty($uploaded_files) ? implode('|', $uploaded_files) : null;
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
                status VARCHAR(50) DEFAULT 'waiting',
                file_path TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "Таблица tickets создана<br>";
    }
    
    // Подготавливаем и выполняем запрос на добавление тикета
    $query = "INSERT INTO tickets (type, message, clients, admin, status, file_path) 
              VALUES (:type, :message, :clients, :admin, :status, :file_path)";
    
    $stmt = $DB->prepare($query);
    
    $params = [
        ':type' => $type,
        ':message' => $message,
        ':clients' => $client_id,
        ':admin' => 1,
        ':status' => 'waiting',
        ':file_path' => $file_path
    ];
    
    $result = $stmt->execute($params);
    
    if ($result) {
        header('Location: ../../clients.php?success=ticket_created');
        exit;
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