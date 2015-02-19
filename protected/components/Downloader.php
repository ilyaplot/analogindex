<?php
/**
 * 
 */
class Downloader
{
    /**
     *
     * @var type 
     */
    public $host;
    
    /**
     *
     * @var type 
     */
    public $proxyPath = '/var/www/analogindex/proxylist.txt';
    
    /**
     *
     * @var type 
     */
    public $limit = 0;
    
    /**
     *
     * @var type 
     */
    public $referer = '';
    
    /**
     *
     * @var type 
     */
    public $list = [];
    
    /**
     * 
     * @param type $host
     * @param type $limit
     */
    public function __construct($host, $limit=0, $disableCheck = false)
    {
        $this->limit = $limit;
        $this->cacheKey = md5(time().microtime());
        $this->setHost($host);
        $this->proxyList('', $disableCheck);
    }
    
    /**
     * 
     * @param type $host
     */
    public function setHost($host)
    {
        $host = preg_replace("/(http:\/\/[\w\.\-]+\/).*/isu", "$1", $host);
        $this->host = $host;
    }
    
    /**
     * 
     * @param type $filename
     */
    public function proxyList($filename = '', $disableCheck = false)
    {
        echo "Загрузка списка прокси...".PHP_EOL;
        $filename = empty($filename) ? $this->proxyPath : $filename;
        $list = file_get_contents($filename);
        $list = explode("\n", $list);
        $list = array_unique($list);
        //shuffle($list);
        $count = count($list);
        echo "Тестирование списка прокси ({$count})...".PHP_EOL;
        if ($this->limit) {
            echo "Установлен лимит {$this->limit} прокси".PHP_EOL;
        }
        $iteration = 0;
        foreach ($list as &$proxy) {
            if ($this->limit && $this->limit <= $iteration) {
                echo PHP_EOL."Выбран лимит проверок";
                break;
            }
            if (!preg_match("/^\d+\.\d+\.\d+\.\d+:\d+/isu", $proxy)) {
                unset($proxy);
                continue;
            }
            if ($disableCheck || $this->testProxy($proxy)) {
                $this->addProxy($proxy);
                $iteration++;
            } else {
                unset($proxy);
            }
        }
        echo PHP_EOL;
        echo "Завершено. Работающих прокси ".count($this->list).PHP_EOL;
        
        if (empty($this->list)) {
            $this->sendMessage();
        }
    }

    /**
     * 
     * @param type $proxy
     * @return boolean
     */
    public function testProxy($proxy)
    {
        $ch = curl_init($this->host);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.".rand(100, 900)." Safari/537.".rand(10, 90));
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        
        if (!empty($this->referer)) {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            echo "-";
            return false;
        }
        
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        if ($info['http_code'] != 200 && $info['http_code'] != 203) {
            echo "@";
            return false;
        }
        echo "+";
        return true;
    }
    
    /**
     * 
     * @param type $proxy
     */
    public function addProxy($proxy) 
    {
        $this->list[$proxy] = $proxy;
    }

    /**
     * 
     */
    public function getProxy()
    {
        $proxy = reset($this->list);
        if (!$proxy) {
            $this->sendMessage();
            return false;
        }
        return $proxy;
    }
    
    /**
     * 
     * @param type $proxy
     */
    public function deleteProxy($proxy)
    {
        unset($this->list[$proxy]);
    }
    
    /**
     * 
     * @param type $url
     */
    public function setReferer($url)
    {
        $this->referer = $url;
    }
    
    /**
     * 
     * @param type $url
     * @param type $filename
     * @return boolean
     */
    public function downloadFile($url, $filename, $skipCode=null)
    {
        if (!$fh = fopen($filename, "w")) {
            echo "Невозможно открыть файл для записи {$filename}".PHP_EOL;
            return false;
        }
        
        if (!$proxy = $this->getProxy()) {
            echo "Нет свободных прокси".PHP_EOL;
            return false;
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 
                "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.".rand(100, 900)." Safari/537.".rand(10, 90));
        curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_FILE, $fh);
        curl_exec($ch);
        fclose($fh);

        if (curl_errno($ch)) {
            $this->deleteProxy($proxy);
            echo "Curl error #".curl_errno($ch)." ".curl_error($ch)." ".$url.PHP_EOL;
            @unlink($filename);
            return $this->downloadFile($url, $filename);
        } else {
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (is_array($skipCode) && in_array($code, $skipCode)) {
                echo "Http code {$code} {$url}.".PHP_EOL;
                return false;
            }
            
            if ($code != 200 && $code != 203) {
                $this->deleteProxy($proxy);
                echo "Http code {$code} {$url}.".PHP_EOL;
                @unlink($filename);
                return $this->downloadFile($url, $filename);
            }
        }
        return true;
    }
    
    /**
     * 
     * @param type $url
     * @return boolean
     */
    public function getContent($url, $recursion = true)
    {
        if (!$proxy = $this->getProxy()) {
            echo "Нет свободных прокси".PHP_EOL;
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 
                "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.".rand(100, 900)." Safari/537.".rand(10, 90));
        curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->deleteProxy($proxy);
            echo "Curl error #".curl_errno($ch)." ".curl_error($ch)." ".$url.PHP_EOL;
            return $this->getContent($url);
        } else {
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($code != 200 && $code != 203) {
                if ($recursion) {
                    $this->deleteProxy($proxy);
                } else {
                    if ($code == 404) {
                        return false;
                    }
                }
                echo "Http code {$code} {$url}.".PHP_EOL;
                return ($recursion) ? $this->getContent($url) : false;
            }
        }
        $this->referer = $url;
        return $content;
    }
    
    /**
     * 
     */
    public function sendMessage()
    {
        $to = "ilyaplot@gmail.com";
        $subject = "Закончились прокси!";
        $message = "Empty proxy list for {$this->host}!";

        $mailer = Yii::app()->Smtpmail;
        $mailer->IsSMTP();
        $mailer->IsHTML(true);
        $mailer->Subject = $subject;
        $mailer->AddAddress($to);
        $mailer->Body = "<h1>".$message."</h1>";

        echo $message.PHP_EOL;
        
        if (!$mailer->Send()) {
            echo $mailer->ErrorInfo . PHP_EOL;
        } else {
            Echo 'Email OK' . PHP_EOL;
        }
        exit();
    }
}