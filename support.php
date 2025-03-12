<?php session_start();

require_once 'api/auth/AuthCheck.php';
AuthCheck('', 'login.php');

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/clients.css">
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>CRM | Техническая поддержка</title>
    <style>
        .support-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .support-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .support-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .form-group label {
            font-weight: bold;
            color: #555;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #45a049;
        }
        
        .btn-secondary {
            background-color: #f1f1f1;
            color: #333;
        }
        
        .btn-secondary:hover {
            background-color: #e1e1e1;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .header__buttons {
            display: flex;
            align-items: center;
        }
        
        .header__support {
            margin-right: 15px;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        
        .header__support:hover {
            background-color: #45a049;
        }
        
        @media (max-width: 768px) {
            .support-container {
                margin: 20px;
                padding: 15px;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <p class="header__admin">
                <?php 
                    require 'api/DB.php';
                    require_once 'api/clients/AdminName.php';
                    require_once 'api/helpers/getUserType.php';

                    echo AdminName($_SESSION['token'], $DB);
                    
                    // Получаем тип пользователя
                    $userType = getUserType($_SESSION['token']);
                ?>
            </p>
            <ul class="header__links">
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="product.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
                <?php
                if ($userType === 'tech'){
                    echo "<li><a href='tech.php'>Обращения пользователя</a></li>";
                }
                ?>
            </ul>
            <div class="header__buttons">
                <a href="support.php" class="header__support">Техподдержка</a>
                <a href="?do=logout" class="header__logout">Выйти</a>
            </div>
        </div>
    </header>

    <div class="container">
        <a href="clients.php" class="back-link"><i class="fa fa-arrow-left"></i> Вернуться к клиентам</a>
        
        <div class="support-container">
            <h1 class="support-title">Техническая поддержка</h1>
            
            <form class="support-form" action="#" method="post">
                <div class="form-group">
                    <label for="subject">Тема обращения</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="priority">Приоритет</label>
                    <select id="priority" name="priority">
                        <option value="low">Низкий</option>
                        <option value="medium" selected>Средний</option>
                        <option value="high">Высокий</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">Описание проблемы</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Отправить запрос</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='clients.php'">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 