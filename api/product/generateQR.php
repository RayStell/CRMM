<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../DB.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Получаем информацию о товаре из базы данных
    $stmt = $DB->prepare("SELECT id, name, description, price FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        // Создаем JSON с информацией о товаре
        $productInfo = json_encode([
            'id' => $product['id'],
            'name' => $product['name'],
            'description' => $product['description'],
            'price' => $product['price']
        ], JSON_UNESCAPED_UNICODE);
        
        // Генерируем QR код
        $qrCode = new QrCode($productInfo);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        // Выводим изображение
        header('Content-Type: ' . $result->getMimeType());
        echo $result->getString();
    } else {
        echo "Товар не найден";
    }
}
?>