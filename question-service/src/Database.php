<?php
namespace App;

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class Database
{
    private $collection;

    public function __construct()
    {
        // Рядок підключення використовує 'mongo' - ім'я сервісу з docker-compose.yml [18, 19]
        // 'localhost' тут не спрацює, оскільки сервіси знаходяться в різних контейнерах.
        $client = new Client("mongodb://mongo:27017");
        $this->collection = $client->selectCollection('quizdb', 'questions');
    }

    /**
     * Отримати всі запитання
     */
    public function getQuestions()
    {
        // db.collection.find() [20]
        $cursor = $this->collection->find([], ['projection' => ['_id' => 1, 'text' => 1]]);
        $questions = [];
        foreach ($cursor as $document) {
            // Перетворюємо ObjectId в рядок для JSON
            $document['_id'] = (string)$document['_id'];
            $questions[] = $document;
        }
        return $questions;
    }

    /**
     * Створити нове запитання
     */
    public function createQuestion($data)
    {
        // db.collection.insertOne() [17, 20]
        $result = $this->collection->insertOne($data);
        return $result->getInsertedId();
    }

    /**
     * Видалити запитання за ID
     */
    public function deleteQuestion($id)
    {
        // db.collection.deleteOne() [17, 21]
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount();
        } catch (\Exception $e) {
            return 0;
        }
    }
}