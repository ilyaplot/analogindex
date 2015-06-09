<?php
Yii::import('ext.crawler.*');
Yii::import('ext.crawler.Enums.*');
Yii::import('ext.crawler.CookieCache.*');
Yii::import('ext.crawler.ProcessCommunication.*');
Yii::import('ext.crawler.UrlCache.*');
Yii::import('ext.crawler.Utils.*');

class Crawler extends PHPCrawler
{
    protected $callback = null;
    
    public function handleDocumentInfo($DocInfo)
    {
        if (!empty($this->callback) && is_callable($this->callback)) {
            return call_user_func_array($this->callback, ['DocInfo'=>$DocInfo]);
        } else {
            // Print the URL and the HTTP-status-Code
            echo "Page requested: " . $DocInfo->url . " (" . $DocInfo->http_status_code . ")" . PHP_EOL;
            flush();
        }
    }
    
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('Callback is not callabale');
        }
        $this->callback = $callback;
    }

}

