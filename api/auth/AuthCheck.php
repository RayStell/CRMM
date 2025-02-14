<?php

function AuthCheck($successPath = '', $errorPath = '') {
    require_once 'api/DB.php';
    require_once 'LogoutUser.php';

    // Проверка наличия ключа token в $_SESSION
    if (!isset($_SESSION['token'])) {

        if ($errorPath) {
            header("Location: $errorPath");
        }

        return;
    }
    // Токен текущего пользователя
    $token = $_SESSION['token'];
    // Получение ИД администратора по текущему токену
    $adminID = $DB->query(
        "SELECT id FROM users WHERE token='$token'
    ")->fetchAll();
    if (empty($adminID) && $errorPath) {
        LogoutUser($errorPath, $DB);

        header("Location: $errorPath");
    }
    if (!empty($adminID) && $successPath) {
        header("Location: $successPath");
    }
}

?>