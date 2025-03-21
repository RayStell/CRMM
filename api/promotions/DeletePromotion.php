<?php
session_start();
require_once '../DB.php';

if (isset($_GET['id'])) {
    try {
        // Получаем информацию об акции перед удалением
        $stmt = $DB->prepare("SELECT path_to_image FROM promotions WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $promo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Удаляем изображение, если оно существует
        if ($promo['path_to_image'] && file_exists('../../' . $promo['path_to_image'])) {
            unlink('../../' . $promo['path_to_image']);
        }

        // Удаляем акцию из базы данных
        $stmt = $DB->prepare("DELETE FROM promotions WHERE id = ?");
        $stmt->execute([$_GET['id']]);

        header('Location: ../../promotions.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['promo_error'] = 'Ошибка при удалении акции: ' . $e->getMessage();
        header('Location: ../../promotions.php');
        exit;
    }
} else {
    header('Location: ../../promotions.php');
    exit;
} 