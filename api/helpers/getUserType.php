<?php
//Функция лежит по пути api/helpers/getUserType.php

function getUserType($token_or_db = null){
    // Проверяем, является ли переданный параметр объектом PDO
    if ($token_or_db instanceof PDO) {
        // Если передан объект PDO, используем его для запроса
        $db = $token_or_db;
        
        // Получаем токен из сессии
        if (isset($_SESSION['token'])) {
            $token = $_SESSION['token'];
            
            // Выполняем запрос через PDO
            $stmt = $db->prepare("SELECT type FROM users WHERE token = :token");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            // Получаем результат
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['type'];
            }
        }
        
        return false;
    } else {
        // Если передан токен или null, используем старую логику с mysqli
        $token = $token_or_db;
        
        // Если токен не передан, пытаемся получить его из заголовков запроса
        if ($token === null) {
            // Получаем заголовки
            $headers = getallheaders();
            
            // Проверяем наличие заголовка Authorization
            if (isset($headers['Authorization'])) {
                // Формат: "Bearer {token}"
                $auth_header = $headers['Authorization'];
                $token = str_replace('Bearer ', '', $auth_header);
            } else {
                return false; // Токен не найден
            }
        }
        
        // Подключение к базе данных
        $conn = new mysqli("localhost", "root", "", "crm");
        
        // Проверка соединения
        if ($conn->connect_error) {
            return false; // Ошибка подключения к БД
        }
        
        // Подготовка запроса для поиска пользователя по токену
        $stmt = $conn->prepare("SELECT type FROM users WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        
        // Получение результата
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Пользователь найден, возвращаем его тип
            $row = $result->fetch_assoc();
            $userType = $row['type'];
            
            // Закрываем соединение
            $stmt->close();
            $conn->close();
            
            return $userType;
        } else {
            // Пользователь не найден
            $stmt->close();
            $conn->close();
            return false;
        }
    }
}

?>