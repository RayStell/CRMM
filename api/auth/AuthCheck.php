<?php

function AuthCheck($successPath = '', $errorPath = '', $DB = null) {
    if ($DB === null) {
        require_once __DIR__ . '/../DB.php';
    }
    require_once __DIR__ . '/LogoutUser.php';

    // Проверка наличия ключа token в $_SESSION
    if (!isset($_SESSION['token'])) {
        if ($errorPath) {
            header("Location: $errorPath");
            exit;
        }
        return false;
    }
    
    // Токен текущего пользователя
    $token = $_SESSION['token'];
    
    // Получение ИД администратора по текущему токену
    $adminID = $DB->query(
        "SELECT id FROM users WHERE token='$token'
    ")->fetchAll();
    
    if (empty($adminID)) {
        if ($errorPath) {
            LogoutUser($errorPath, $DB);
            header("Location: $errorPath");
            exit;
        }
        return false;
    }
    
    return true;
}

?>