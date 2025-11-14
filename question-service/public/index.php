<?php
// Завантажуємо автозавантажувач Composer
require_once __DIR__. '/../vendor/autoload.php';

use App\QuestionController;

$controller = new QuestionController();

// Визначаємо, який метод HTTP використовується
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $controller->handleGet();
        break;
    case 'POST':
        $controller->handlePost();
        break;
    case 'DELETE':
        $controller->handleDelete();
        break;
    default:
        // Метод не дозволений
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}