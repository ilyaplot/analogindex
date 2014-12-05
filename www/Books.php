<?php

/**
 * 
 */
class Books
{
    protected $servers = array(
        'kappa3.academic.ru'
    );
    public static $connection = null;
    public static $itemsCount = null;
    protected $maxLimit = 20;

    public function __construct()
    {
        
    }

    /**
     * 
     * @param int $limit
     */
    public function getItems($limit = 3)
    {
        $limit = abs(intval($limit));
        $limit = (int) ($limit < $this->maxLimit) ? $limit : $this->maxLimit;
        $count = $this->getItemsCount();
        $ids = array();
        for ($i = 0; $i < $limit; $i++) {
            do {
                $rand = mt_rand(1, $count);
            } while (in_array($rand, $ids) && $count > $limit);
            $ids[] = $rand;
        }
        $ids = implode(", ", $ids);
        $query = "select id, picture, name, price FROM books where id2 in ({$ids}) order by field(id2, {$ids}) limit {$limit}";
        try {
            $command = $this->getDb()->prepare($query);
            $command->execute();
            return $command->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Получает количество записей с available=1
     * @return int
     * @throws Exception
     */
    public function getItemsCount()
    {
        if (self::$itemsCount === null) {
            $query = "select max(id2) from books";
            try {
                $command = $this->getDb()->prepare($query);
                $command->execute();
                self::$itemsCount = (int) $command->fetchColumn();
            } catch (Exception $ex) {
                throw $ex;
            }
        }
        return self::$itemsCount;
    }

    /**
     * @todo Вывести переменные в конфиг, если потребуется
     */
    protected function getDb()
    {
        if (self::$connection === null) {
            $server = $this->servers[array_rand($this->servers)];
            $user = "dic";
            $pass = "ddd3388";
            $dbname = "dic";
            $port = "3306";

            try {
                self::$connection = new PDO("mysql:host={$server};port={$port};dbname={$dbname};charset=UTF8", $user, $pass);
            } catch (Exception $ex) {
                throw $ex;
            }
        }

        return self::$connection;
    }

    public function getJsonItems($limit = 3)
    {
        $items = $this->getItems($limit);
        $items = array_map(function ($value) {
            $value['url'] = "http://books.academic.ru/book.nsf/{$value['id']}/" . urlencode($value['name']);
            return $value;
        }, $items);
        if (is_array($items) && !empty($items)) {
            return json_encode(array("items" => $items));
        }
    }

}

class BooksServer
{

    protected $model;

    const ANSWER_JSON = 1;
    const ANSWER_HTML = 2;

    public function __construct()
    {
        $this->model = new Books;
    }

    public function run($limit, $type = self::ANSWER_JSON)
    {
        try {
            if ($json = $this->model->getJsonItems($limit, $type)) {
                http_response_code(200);
                return $json;
            }
        } catch (Exception $ex) {
            http_response_code(500);
        }

        http_response_code(404);
    }

}

$server = new BooksServer();
$type = (isset($_POST['type']) && in_array($_POST['type'], array(1, 2))) ? $_POST['type'] : BooksServer::ANSWER_JSON;
$limit = isset($_POST['limit']) ? $_POST['limit'] : 3;
echo $server->run($limit, $type);
exit();
