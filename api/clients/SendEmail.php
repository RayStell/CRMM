<?php
$imagePath = '../../img/hoh.jpg';
if (file_exists($imagePath)) {
    $backgroundImage = 'url("' . $imagePath . '")';
} else {
    echo "Ошибка: Файл не найден по пути: " . $imagePath;
    $backgroundImage = 'none';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Сибирский гостинец</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            background-image: <?php echo $backgroundImage; ?> !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: rgba(249, 249, 249, 0.95);
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            color: #4a2c2c;
        }
        .content {
            line-height: 1.6;
            color: #333;
            text-align: center;
            max-width: 80%;
            margin: 0 auto;
            padding: 20px 0;
        }
        .content p {
            text-align: center;
            margin-bottom: 15px;
        }
        .content h2 {
            text-align: center;
        }
        .contact-info {
            margin-top: 30px;
            color: #666;
            text-align: right;
            padding-right: 0;
        }
        .contact-info p {
            margin: 5px 0;
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #333;
        }
        h2 {
            color: #4a2c2c;
        }
        a {
            color: #0066cc;
            text-decoration: none;
        }
        p {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>Магаз</h1>
        </div>
        <div class="content">
            <h2>Дорогие коллеги!</h2>
            <p>Компания «Сибирский гостинец» — это российский производитель натуральных продуктов из экологически чистого сырья. Мы перерабатываем и реализуем дикорастущие лесные ягоды с применением инновационных технологий сублимации, а также выпускаем снековую продукцию (кедровый орех и сушеные грибы).</p>
            <p>Мы работаем с 2012 года, но уже наладили взаимовыгодные партнёрские отношения с крупными российскими торговыми сетями: «Лента», «Ашан», «Магнит», «Звездный», «Линия», «Глобус» и другие. Нас ценят за высокое качество продукта и строгое соблюдение сроков. А мы ценим своих партнёров и всегда рады новым!</p>
            <p>Больше полезной информации о нашей компании и продукте вы найдете в презентации во вложении (либо <a href="#">по ссылке</a>).</p>
        </div>
        <div class="contact-info">
            <p>(3462) 77-40-59</p>
            <p>info@sg-trade.ru</p>
            <p>628406, РФ, ХМАО-Югра,</p>
            <p>г. Сургут, ул. Университетская, 4</p>
        </div>
        <div class="footer">
            <h2>СИБИРЬ БЛИЖЕ, ЧЕМ ВЫ ДУМАЕТЕ...</h2>
        </div>
    </div>
</body>
</html>