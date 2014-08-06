<?php
class DownloadCommand extends CConsoleCommand
{
    public function actionSotmarketSitemap()
    {
        require 'phpQuery.php';
        $content = $this->getContent("http://www.sotmarket.ru/category/mobiles.html");
        if (!$content)
            return false;
        $html = phpQuery::newDocumentHTML($content);
        $nextPage = pq($html)->find("div.b-paginator-cell.type_list a")->attr("href");
        echo $content;
        echo $nextPage.PHP_EOL;
    }


    public function actionMobSitemap()
    {
        require 'phpQuery.php';
        $content = $this->getContent("http://mob.org/phone/samsung/samsung_galaxy_star_plus_gt-s7262.html");
        if (!$content)
            return false;
        $html = phpQuery::newDocumentHTML($content);
        $list = pq($html)->find("#brandSel > option");
        $brands = array();
        foreach ($list as $option)
        {
            $val = pq($option)->val();
            if (!empty($val))
                $brands[] = $val;
        }
        shuffle($brands);
        $models = array();
        foreach ($brands as $brand)
        {
            sleep(rand(1,2));
            $content = $this->getPost("http://mob.org/hint/showModelList/", "brand={$brand}&section=phone&");
            if (!$content)
                continue;
            $html = phpQuery::newDocumentHTML($content);
            $modelContent = pq($html)->find("#modelSel > option");
            foreach ($modelContent as $model)
            {
                $val = pq($model)->val();
                if (!empty($val))
                    $models[] = array($brand, $val);
            }
            shuffle($models);
            foreach ($models as $model)
            {
                $url = "http://mob.org/phone/{$model[0]}/{$model[0]}_{$model[1]}.html";
                echo $url.PHP_EOL;
                if (!Sitemaps::model()->findByAttributes(array('source'=>1, 'url'=>$url)))
                {
                    $sitemap = new Sitemaps();
                    $sitemap->url = $url;
                    $sitemap->source = 1;
                    $sitemap->save();
                }
            }
        }
    }
    
    public function actionMobPages()
    {
        $storage = Yii::app()->mob;
        $conn = Yii::app()->db;
        $select = "select * from sitemaps where completed = 0 limit 1000";
        $next = "select max(id)+1 as id from mob";
        $new = "insert ignore into mob (id, url, size) values (:id, :url, :size)";
        $update = "update sitemaps set completed = 1 where id = :id";
        $list = $conn->createCommand($select)->queryAll();
        foreach ($list as $item)
        {
            sleep(rand(1,2));
            $id = $conn->createCommand($next)->queryScalar();
            if (!$id)
                $id = 1;
            $filename = $storage->getFileName($id);
            if ($this->getFile($item['url'], $filename))
            {
                $size = filesize($filename);
                if (!$size)
                    continue;
                $conn->createCommand($new)->execute(array(
                    'id'=>$id,
                    'url'=>$item['url'],
                    'size'=>$size,
                ));
                $conn->createCommand($update)->execute(array(
                    'id'=>$item['id'],
                ));
            }
            
        }
    }
    
    public function getContent($url, $use_proxy = false)
    {
        $proxy = Proxy::getAlive();
        if (!$proxy)
            return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ($use_proxy)
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
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
    
    public function getPost($url, $fields, $use_proxy = false)
    {
        $proxy = Proxy::getAlive();
        if (!$proxy)
            return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        if ($use_proxy)
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
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
    
    public function getFile($url, $filename, $is_proxy = false)
    {
        $proxy = Proxy::getAlive();
        if (!$proxy)
            return false;
        if (empty($filename))
            return false;
        if (!$file = fopen($filename, 'w'))
            return false;
        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        if ($is_proxy)
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
    
    public static function getImage($url, $filename)
    {
        $proxy = Proxy::getAlive();
        if (!$proxy)
            return false;
        if (empty($filename))
            return false;
        if (!$file = fopen($filename, 'w'))
            return false;
        $ch = curl_init($url);
        // Юзерагент индексатора яндекс.картинок
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; YandexImages/3.0; +http://yandex.com/bots)");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
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
        curl_close($ch);
        return true;
    }
}