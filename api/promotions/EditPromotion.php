<?php
session_start();
require_once '../DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['id', 'code_promo', 'title', 'body', 'discount', 'max_uses'];
    $errors = [];

    // Проверка обязательных полей
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $errors[] = "Поле $field обязательно для заполнения";
        }
    }

    if (!empty($errors)) {
        $_SESSION['promo_error'] = implode('<br>', $errors);
        header('Location: ../../promotions.php');
        exit;
    }

    try {
        // Получаем текущую информацию об акции
        $stmt = $DB->prepare("SELECT path_to_image FROM promotions WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $current_promo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Обработка загруженного изображения
        $image_path = $current_promo['path_to_image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../uploads/promotions/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($file_extension, $allowed_extensions)) {
                $_SESSION['promo_error'] = 'Недопустимый формат файла. Разрешены только: ' . implode(', ', $allowed_extensions);
                header('Location: ../../promotions.php');
                exit;
            }

            // Удаляем старое изображение, если оно существует
            if ($current_promo['path_to_image'] && file_exists('../../' . $current_promo['path_to_image'])) {
                unlink('../../' . $current_promo['path_to_image']);
            }

            $file_name = uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $image_path = 'uploads/promotions/' . $file_name;
            }
        }

        $stmt = $DB->prepare("
            UPDATE promotions SET
                code_promo = :code_promo,
                title = :title,
                body = :body,
                discount = :discount,
                max_uses = :max_uses,
                cancel_at = :cancel_at,
                path_to_image = :path_to_image
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $_POST['id'],
            ':code_promo' => $_POST['code_promo'],
            ':title' => $_POST['title'],
            ':body' => $_POST['body'],
            ':discount' => $_POST['discount'],
            ':max_uses' => $_POST['max_uses'],
            ':cancel_at' => !empty($_POST['cancel_at']) ? $_POST['cancel_at'] : null,
            ':path_to_image' => $image_path
        ]);

        header('Location: ../../promotions.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['promo_error'] = 'Ошибка при обновлении акции: ' . $e->getMessage();
        header('Location: ../../promotions.php');
        exit;
    }
} else {
    header('Location: ../../promotions.php');
    exit;
} 