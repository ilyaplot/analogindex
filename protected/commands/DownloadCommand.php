<?php
class DownloadCommand extends CConsoleCommand
{

    public function actionGsmarenaList()
    {
        $brandsPage = $this->getContent("http://www.gsmarena.com/makers.php3");
        if (!$brandsPage)
        {
            echo "No main page. Exit.";
            exit();
        }
        $html = phpQuery::newDocumentHTML($brandsPage);
        $menu = pq($html)->find(".st-text tr td > a");
        $brands = array();
        foreach ($menu as $menuItem)
        {
            $brands[] = "http://www.gsmarena.com/".pq($menuItem)->attr("href");
        }
        $brands = array_unique($brands);
        shuffle($brands);
        $urls = array();
        foreach ($brands as $brand)
        {
            sleep(1);
            $content = $this->getContent($brand);
            if (!$content)
            {
                echo "ERROR download brand's page. {$brand}".PHP_EOL;
                continue;
            }
            
            $urls = array_merge($urls ,$this->_getGsmarenaItems($content));
            
            $html = phpQuery::newDocumentHtml($content);
            $pages = pq($html)->find("div.nav-pages > a");
            foreach ($pages as $page)
            {
                sleep(1);
                $page = "http://www.gsmarena.com/".pq($page)->attr("href");
                $content = $this->getContent($page);
                if (!$content)
                {
                    echo "ERROR download brand's subpage. {$page}".PHP_EOL;
                    continue;
                }
                $urls = array_merge($urls, $this->_getGsmarenaItems($content));
            }
        }
        foreach ($urls as $url)
        {
            $model = new SourcesGsmarena();
            $model->url = $url;
            if ($model->validate())
                $model->save();
            else
                var_dump($model->getErrors());
        }
    }
    
    public function actionGsmArena()
    {
        $urls = SourcesGsmarena::model()->findAllByAttributes(array(
            "file"=>0
        ));
        foreach ($urls as $url)
        {
            sleep(1);
            echo ".";
            $content = $this->getContent($url->url);
            if (!$content)
            {
                echo "Not downloaded {$url->url}";
                continue;
            }
            $file = new SourcesGsmarenaFiles();
            $file->save();
            if (!$file->putFile($content))
            {
                echo "Not put downloaded {$url->url}";
                $file->delete();
                continue;
            }
            $file->size = $file->getFilesize();
            $file->mime_type = $file->getMimeType();
            $file->name = "gsmarena.html";
            $file->save();
            $url->file = $file->id;
            $url->save();
        }
    }
    
    private function _getGsmarenaItems($content)
    {
        $html = phpQuery::newDocumentHTML($content);
        $items = pq($html)->find("div.makers ul > li a");
        $urls = array();
        foreach ($items as $item)
        {
            $urls[] = "http://www.gsmarena.com/".pq($item)->attr("href");
        }
        
        return array_unique($urls);
    }
    
    
    public function getContent($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        //curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $content = curl_exec($ch);
        if (curl_errno($ch))
        {
            echo "Curl error ".curl_error($ch).PHP_EOL;
            curl_close($ch);
            return false;
        }
        $info = curl_getinfo($ch);
        if ($info["http_code"] !== 200)
            return false;
        curl_close($ch);
        return $content;
    }
    
    public function getFile($url, $filename)
    {
        $proxy = Proxy::getAlive();
        if (!$proxy)
            return false;
        if (empty($filename))
            return false;
        if (!$file = fopen($filename, 'w'))
            return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_FILE, $file);
        curl_exec($ch);
        fclose($file);
        if (curl_errno($ch))
        {
            echo "Curl error ".curl_error($ch).PHP_EOL;
            curl_close($ch);
            return false;
        }
        
        $info = curl_getinfo($ch);
        if ($info['http_code'] !== 200)
            return false;
        curl_close($ch);
        return true;
    }
}