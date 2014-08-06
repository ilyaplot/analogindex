<?php
/**
 * Обертка для curl
 * @example 
  // Создаем экземпляр класса. Любой параметр не обязателен. Указаны значения по-умолчанию
  $curl = new CurlLayer(array(
       // Если true, параметр useragent меняется автоматически
        'random_useragent'=>false,
        // Частота смены юзерагента в секундах
        'random_useragent_time'=>600,
        // Useragent. Не меняется, если опция random_useragent = false
        'useragent'=>'Mozilla/5.0 (Windows NT 6.1; rv:24.0) Gecko/20100101 Firefox/24.0',
        // Если true, подставляется заголовок HTTP_X_FORWARDED_FOR
        'forwarded_for'=>false,
        // Зависит от forwarded_for, если true, меняет ip для HTTP_X_FORWARDED_FOR
        'random_forwarded_for'=>false,
        // Время смены ip для HTTP_X_FORWARDED_FOR в секундах
        'random_forwarded_for_time'=>600,
        // Рабочая папка для curl
        'runtime_path'=>false,
        // Посылает предыдущий url
        'send_refferer'=>true,
        // Предыдущий url
        'refferer'=>'',
  ));
  $content = $curl->getContent("http://ya.ru", array('post_field'=>'post_value'));
  //Возвращает массив [code, content], где code - http код ответа сервера, content - спарсенная страница
  $content2 = $curl->getContent("http://ya.ru/2", array('post_field'=>'post_value2'));
  // Для выполнения следующего запроса не требуется заново создавать экземпляр класса,
  // в refferer передается предыдущий url (http://ya.ru)
 * 
 * @description
  Обертка для curl, позволяющая парсить множество страниц. 
  В заголовки добавлен forwarded for для имитации прокси, т.к. большинство админов
  считают, что реальный ip - это как раз и есть forwarded for.
  Так же меняются юзерагенты. Тем самым, скрипт пытается выглядеть прокси сервером, 
  за которым скрывается целая подсеть, которую банить решится не каждый.
  В заголовках передается url предыдущей страницы, иммитируя переход из браузера.
 */

// Папка для хранения кэш-файлов
if (!defined("RUNTIME"))
    define("RUNTIME", Yii::app()->runtimePath );

class CurlLayer
{
    public $connection;
    /**
     * Путь к временной папке
     * @var type 
     */
    private $_runtime;
    /**
     * Время последней смены useragent в unixtime
     * @var int
     */
    private $_userAgentStartTime;
    /**
     * Время следующей смены useragent
     * @var int
     */
    private $_userAgentEndTime;
    /**
     * Строка useragent
     * @var string
     */
    private $_userAgent;
    /**
     * Массив useragent'ов доля подмены
     * @var array()
     */
    private $_userAgentList;
    /**
     * Время последней смены forwarded for
     * @var int
     */
    private $_forwardedForStartTime;
    /**
     * Время следующей смены forwarded for
     * @var int
     */
    private $_forwardedForEndTime;
    
    private $_forwarded_for_ip;
    
    const FORWARDED_FOR_SUBNET = "192.168.100.";
    
    /**
     * Параметры парсинга
     */
    public $settings = array
    (
        // Если true, параметр useragent меняется автоматически
        'random_useragent'=>false,
        // Частота смены юзерагента в секундах
        'random_useragent_time'=>600,
        // Useragent. Не меняется, если опция random_useragent = false
        'useragent'=>'Mozilla/5.0 (Windows NT 6.1; rv:24.0) Gecko/20100101 Firefox/24.0',
        // Если true, подставляется заголовок HTTP_X_FORWARDED_FOR
        'forwarded_for'=>false,
        // Зависит от forwarded_for, если true, меняет ip для HTTP_X_FORWARDED_FOR
        'random_forwarded_for'=>false,
        // Время смены ip для HTTP_X_FORWARDED_FOR в секундах
        'random_forwarded_for_time'=>600,
        // Рабочая папка для curl
        'runtime_path'=>false,
        // Посылает предыдущий url
        'send_refferer'=>true,
        // Предыдущий url
        'refferer'=>'',
    );
    
    /**
     * Обертка для curl
     * @param array $settings
     * @throws Exception
     */
    public function __construct(array $settings = array()) 
    {
        $this->connect = curl_init();
        foreach($settings as $option=>$value)
        {
            if (isset($this->settings[$option]))
                $this->settings[$option] = $value;
            else 
                throw new Exception("Опция \$settings['{$option}'] не определена в ".__CLASS__);
        }
        
        // Если указана папка для временных файлов, устанавливаем
        if ($this->settings['runtime_path'] !== false)
        {
            $this->_runtime = $this->settings['runtime_path'];
        } else {
            $this->_runtime = RUNTIME.DIRECTORY_SEPARATOR."curl";
        }
        // Попытка созать runtime path если он не существует
        try 
        {
            if (!file_exists($this->_runtime) || !is_dir($this->_runtime))
                mkdir($this->_runtime, 0777, true);
        } 
        catch (Exception $ex) 
        {
            throw $ex;
        }
        $this->_userAgentList = $this->getUserAgents();
        $this->_userAgent = $this->settings['useragent'];
        $this->_forwarded_for_ip = self::FORWARDED_FOR_SUBNET.rand(1,253);
        $this->beforeWork();
    }
    
    /**
     * Необходимо выполнять перед каждым следующим запросом
     * Запускает функции обновления 
     */
    public function beforeWork()
    {
        $this->_userAgentStartTime = time();
        $this->_forwardedForStartTime = time();
        $this->_updateUserAgent();
        $this->_updateForwardedFor();
    }
    
    /**
     * Основная функция парсинга
     * @param string $url 
     * @param array $post
     * @return array
     */
    public function getContent($url, $post = null, $nocache=false, $noreset = false)
    {
        $this->beforeWork();
        
        $postString = null;
        if (is_array($post))
            $postString = http_build_query($post);

        $filepath = RUNTIME."/curl/files/".md5($url.$postString).".html";
        
        if (!$nocache && file_exists($filepath))
            return array(
                'code'=>200,
                'content'=>file_get_contents($filepath),
                'from_cache'=>true,
            );
        if (!$noreset)
            $this->reset();
        if ($this->settings['send_refferer'] && !empty($this->settings['refferer']))
            curl_setopt ($this->connection, CURLOPT_REFERER, $this->settings['refferer']);
        curl_setopt($this->connection, CURLOPT_URL, $url);
        curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);
        
        // Если передан массив post, отправлем post запрос
        if ($postString !== null)
        {
            curl_setopt($this->connection, CURLOPT_POST, true);
            curl_setopt($this->connection, CURLOPT_POSTFIELDS, $postString);
        }
        
        $content = curl_exec($this->connection);
        $this->settings['refferer'] = $url;
        
                
        $result =  array(
            'code'=>curl_getinfo($this->connection, CURLINFO_HTTP_CODE),
            'content'=>$content,
        );
        
        if ($result['code'] == 200)
        {
            file_put_contents($filepath, $result['content']);
        }
        
        return $result;
    }

    /**
     * Скачивает файл во временную директорию
     * @param type $url
     * @return string
     * @throws Exception
     */
    public function downloadFile($url, $ext = 'tmp') 
    {
        $this->beforeWork();
        try {
            $filesModel = new FilesModel();
            $filename = $filesModel->tmppath.md5(time().microtime()).".".$ext;
            //touch($filename);
            
            $fp = fopen ($filename, 'w+');
            $ch = curl_init(str_replace(" ","%20",$url));
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
            curl_setopt($ch, CURLOPT_FILE, $fp); 
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch); 
            curl_close($ch);
            fclose($fp);
        } catch (Exception $ex) {
            throw $ex;
        }
        
        return $filename;
    }
    
    /**
     * Подготавливает curl для нового запроса
     */
    public function reset()
    {
        if (function_exists('curl_reset'))
            curl_reset($this->connection);
        else 
        {
            @curl_close($this->connection);
            $this->connection = curl_init();
        }
        if ($this->settings['forwarded_for'])
        {
            $host= gethostname();
            $ip = gethostbyname($host);
            curl_setopt($this->connection, CURLOPT_HTTPHEADER, array(
                "X-Forwarded-For: {$this->_forwarded_for_ip}",
                "Via: 1.1 ".self::FORWARDED_FOR_SUBNET."254, 1.1 {$ip}",
            ));
        }
        curl_setopt($this->connection, CURLOPT_USERAGENT, $this->_userAgent);
    }

    private function _updateUserAgent()
    {
    
        if ($this->_userAgentEndTime > $this->_userAgentStartTime)
            return false;
        
        if (count($this->_userAgentList) < 2)
            return false;
        do
        {
            // Выбираем рандомный useragent
            $userAgent = $this->_userAgentList[array_rand($this->_userAgentList, 1)];
            
        }
        while($userAgent == $this->_userAgent);
        $this->_userAgent = $userAgent;
        
        $this->_updateUserAgentTime();
        
        //echo "UserAgent сменен на  \"{$userAgent}\" ".PHP_EOL;
        return true;
    }

    private function _updateForwardedFor()
    {
        if ($this->_forwardedForEndTime > $this->_forwardedForStartTime)
            return false;
        do
        {
            $forwardedIp = self::FORWARDED_FOR_SUBNET.rand(1,253);
        } 
        while($forwardedIp == $this->_forwarded_for_ip);
        
        $this->_forwarded_for_ip = $forwardedIp;
        
        $this->_updateForwardedForTime();
        
        //echo "HTTP_X_FORWARDED_FOR сменен на  \"{$forwardedIp}\" ".PHP_EOL;
        
        return true;
    }

    /**
     * Устанавливает время обновления useragent
     * @param int $startTime
     */
    private function _updateUserAgentTime(int $endTime = null)
    {
        $this->_userAgentStartTime = time();
        // Если требуется смена useragent, задаем таймаут смены
        if ($this->settings['random_useragent'])
            $this->_userAgentEndTime = (($endTime) ? $endTime : time()) + intval($this->settings['random_useragent_time']);
        else
            $this->_userAgentEndTime = -1;
    }
    
    /**
     * Устанавливает время обновления заголовка HTTP_X_FORWARDED_FOR
     * @param int $startTime
     */
    private function _updateForwardedForTime(int $endTime = null)
    {
        $this->_forwardedForStartTime = time();
        // Если требуется смена useragent, задаем таймаут смены
        if ($this->settings['random_forwarded_for'])
            $this->_forwardedForEndTime = ($endTime) ? $endTime : time() + intval($this->settings['random_forwarded_for_time']);
        else
            $this->_forwardedForEndTime = -1;
    }

    /**
     * Возвращает массив useragent-ов из текстового файла
     * @param string $filename Путь к файлу с юзерагентами
     * @return array
     * @throws Exception
     */
    public function getUserAgents(string $filename = null)
    {
        if (empty($filename))
            $filename = $this->_runtime.DIRECTORY_SEPARATOR."useragents.txt";
        try 
        {
            $userAgents = file_get_contents($filename);
        } 
        catch (Exception $ex) 
        {
            throw $ex;
        }
        // trim нужен для удаления постой пустой строки
        $result = explode(PHP_EOL, trim($userAgents));
        return $result;
    }
    
    /**
     * Записывает useragents в файл
     * @param array $useragents
     * @param string $filename
     * @return boolean
     * @throws Exception
     */
    public function setUserAgents(array $useragents, string $filename = null)
    {
        if (empty($filename))
            $filename = $this->_runtime.DIRECTORY_SEPARATOR."useragents.txt";
        
        if (!is_array($useragents))
            return false;
        
        try 
        {
            return file_put_contents($filename, implode(PHP_EOL, $useragents));
        } 
        catch (Exception $ex) 
        {
            throw $ex;
        }
    }
    
    /**
     * Выделяет клиентские user-агенты и уникализирует список
     * @param array $userAgents
     * @return array
     */
    public function prepeareUserAgents(array $userAgents)
    {
        $userAgents = array_unique($userAgents);
        foreach ($userAgents as $key=>$agent)
        {
            if (!preg_match("~^[Mozilla|Opera]~u", $agent))
                unset($userAgents[$key]);
        }
        return $userAgents;
    }
    
    public function wait()
    {
        sleep(rand(1, 5));
    }
}