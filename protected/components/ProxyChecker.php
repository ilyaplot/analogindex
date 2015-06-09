<?php
class ProxyChecker extends CComponent
{
    protected $_proxyList = [];
    protected $_listDelimiter = "\n";
    protected $_checkTimeout = 5;
    
    /**
     * Заполняет список прокси
     * @param str|array $list
     * @param bool $append
     */
    public function setList($list, $append = false)
    {
        if (is_string($list)) {
            $list = explode($this->_listDelimiter, $list);
        } elseif (!is_array($list)) {
            throw new CException(Yii::t('proxychecker','Параметр $list не является массивом или строкой.'));
        }
        
        $this->_proxyList = ($append) ? array_merge($this->_proxyList, $list) : $list;
        $this->filterProxyList();
    }
    
    /**
     * Устанавливает разделитель для списка прокси
     * @param str $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->_listDelimiter = $delimiter;
    }
    
    /**
     * Возвращает список отфильтрованных прокси
     * @return array
     */
    protected function getFiltered()
    {
        $proxylist = $this->_proxyList;
        $this->_proxyList = [];
        foreach ($proxylist as $proxy) {
            /**
             * @todo Ивент на невалидный прокси!
             */
            if (!preg_match("/(?P<ip>\d+\.\d+\.\d+\.\d+):(?P<port>\d+)/isu", trim($proxy), $matches)) {
                continue;
            }
            
            if (!filter_var($matches['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                continue;
            }
            
            if (!(($matches['port'] > 1) && ($matches['port']) < 65535)) {
                continue;
            }
            
            $this->_proxyList[] = "{$matches['ip']}:{$matches['port']}";
            
            return $this->_proxyList;
        }
    }

    public function getAlive()
    {
        foreach ($this->filtered as $proxy) {
            
        }
        
    }
    
    public function testProxy($proxy)
    {
        $ch = curl_init($this->host);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.".rand(100, 900)." Safari/537.".rand(10, 90));
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
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
    
    
    
}