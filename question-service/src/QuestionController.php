<?php
namespace App;

class QuestionController
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        // Встановлюємо заголовок, щоб клієнт знав, що ми повертаємо JSON
        header('Content-Type: application/json');
    }

    public function handleGet()
    {
        $questions = $this->db->getQuestions();
        echo json_encode($questions);
    }

    public function handlePost()
    {
        // Отримуємо "сире" тіло POST-запиту, яке надіслав api-gateway 
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($data['text'])) {
            $insertedId = $this->db->createQuestion($data);
            echo json_encode(['success' => true, 'id' => (string)$insertedId]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid JSON or missing "text" field']);
        }
    }

    public function handleDelete()
    {
        // api-gateway пересилає нам ID у query string
        if (isset($_GET['id'])) {
            $deletedCount = $this->db->deleteQuestion($_GET['id']);
            if ($deletedCount > 0) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'Question not found']);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Missing "id" parameter']);
        }
    }
}