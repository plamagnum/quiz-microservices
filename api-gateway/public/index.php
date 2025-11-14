<?php
// Простий PSR-4 автозавантажувач, оскільки тут немає Composer
spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'App\\';
    // base directory for the namespace prefix
    $base_dir = __DIR__. '/../src/';
    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len)!== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir. str_replace('\\', '/', $relative_class). '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\ApiController;

$controller = new ApiController();
$method = $_SERVER['REQUEST_METHOD'];

// Маршрутизація на основі методу 
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
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}