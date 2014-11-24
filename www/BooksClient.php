<?php

class BooksClient
{
    const TEMPLATE_JSON = 1;
    const TEMPLATE_HTML = 2;
    
    protected $apiUrl = "http://analogindex.ru/Books.php";
    
    public function request($limit, $type)
    {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "limit"=>$limit,
            "type"=>$type,
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $answer = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return false;
        }
        curl_close($ch);
        $answer = @json_decode($answer, true);
        return isset($answer['items']) ? $answer['items'] : false;
    }
    
    public function render($limit = 3)
    {
        if ($items = $this->request($limit, self::TEMPLATE_JSON)) {
            ob_start();
            require dirname(__FILE__)."/template.php";
            return ob_get_clean();
        }
    }
}

$client = new BooksClient;
echo $client->render(3);