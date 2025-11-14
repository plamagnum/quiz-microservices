<?php
namespace App;

class ApiController
{
    // Адреса приватного мікросервісу.
    // 'http://question-service-php' - це ім'я сервісу з docker-compose.yml [23]
    private $serviceUrl = 'http://question-service-php/public/index.php';

    public function __construct()
    {
        // Встановлюємо заголовок JSON для відповіді клієнту
        header('Content-Type: application/json');
    }

    public function handleGet()
    {
        // Простий GET-запит до сервісу питань 
        $response = @file_get_contents($this->serviceUrl);
        if ($response === false) {
             http_response_code(503); // Service Unavailable
             echo json_encode(['error' => 'Question service is unavailable']);
             return;
        }
        // Просто "прокидаємо" відповідь від question-service назад клієнту
        echo $response;
    }

    public function handlePost()
    {
        // Отримуємо тіло запиту, яке нам надіслав Nginx
        $requestBody = file_get_contents('php://input');

        // Використовуємо cURL для пересилання POST-запиту [3, 24]
        $ch = curl_init($this->serviceUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER,);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        http_response_code($httpCode);
        echo $response;
    }

    public function handleDelete()
    {
        // Отримуємо query string (напр., 'id=12345') від Nginx
        $queryString = $_SERVER;
        $url = $this->serviceUrl. '?'. $queryString;

        // Використовуємо cURL для пересилання DELETE-запиту
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // [25]

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        http_response_code($httpCode);
        echo $response;
    }
}