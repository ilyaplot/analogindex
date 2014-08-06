<?php
class DownloaderCommand extends ConsoleCommand
{
    /**
     * Уникализирует и записывает useragents
     * @param string $filename Путь к источнику формата useragent\n
     */
    public function actionPrepeareUserAgents($filename=null)
    {
        $curl = new CurlLayer();
        $userAgents = $curl->getUserAgents();
        $curl->setUserAgents($curl->prepeareUserAgents($userAgents));
    }
    
    
    public function actionGsmArena()
    {
        $settings = array
        (
            'random_useragent'=>true,
            'random_useragent_time'=>300,
            'forwarded_for'=>true,
            'random_forwarded_for'=>true,
            'random_forwarded_for_time'=>1800,
            'send_refferer'=>true,
        );
        $curl = new CurlLayer($settings);
        $model = new ManufacturersModel();
        $parser = new GsmArenaParser();
        $result = $parser->parseManufacturers();
        $filesModel = new FilesModel();
        $goodsModel = new GoodsModel();
        shuffle($result);
        foreach ($result as $item)
        {
            $curl->wait();
            $file = $curl->downloadFile($item['logo']);
            $file = $filesModel->add($file, null, $item['logo']);
            $m = $model->add(1, $item['name'], '', $file);
            
            $c = Yii::app()->db;
            $c->createCommand("update manufacturers set logo = :logo where name = :name")
                ->execute(array('logo'=>$file, 'name'=>$item['name']));
            $pdas = array();
            $link = "http://m.gsmarena.com/".$item['link'];
            $curl->wait();
            // Если есть пагинатор
            if ($pages = $parser->getPaginstionMax($link))
            {
                for ($p = 1; $p<$pages[0]; $p++)
                {
                    $pdas = array_merge($pdas, $parser->getPDALinks("http://m.gsmarena.com/{$pages[1]}p{$p}.php"));
                }
            }  
            $curl->wait();
            $pdas = array_merge($pdas, $parser->getPDALinks("http://m.gsmarena.com/".$item['link']));
            foreach ($pdas as $pitem)
            {
                $pitem['link'] = "http://m.gsmarena.com/".$pitem['link'];
                $curl->wait(); 
                $goodsModel->add(1, $parser->parseMain($pitem, $item['name']), $parser->getSourceName(), 'en');
            }
        }
    }

    public function actionUpdate()
    {
        $settings = array
        (
            'random_useragent'=>true,
            'random_useragent_time'=>300,
            'forwarded_for'=>true,
            'random_forwarded_for'=>true,
            'random_forwarded_for_time'=>1800,
            'send_refferer'=>true,
        );
        $curl = new CurlLayer($settings);
        $result = $curl->getContent("http://devdb.ru/pda", null, true);
        if(!$result['content'])
            return false;
         
        $pattern = "~<li><a href=\"(?P<link>http://devdb\.ru/pda/[^\"]+)\">[^<]+</a></li>~";
        if (!preg_match_all($pattern, $result['content'], $matches))
            throw new CException("Не удалось получить список производителей");
        
        shuffle($matches['link']);
        
        foreach($matches['link'] as $link)
        {
            $curl->wait();
            $content = $curl->getContent($link, null, true);
            if ($content['code'] !== 200)
                throw new CException("Error at {$link} Http code is {$content['code']}");
                
            $pattern = "~<a href=\"(?P<device>http://devdb.ru/[\w\d_]+)\">.*</a> <div class=\"rate\">~";
            if (!preg_match_all($pattern, $content['content'], $list))
                throw new CException("Не удалось получить список девайсов");
 
            shuffle($list['device']);
            
            foreach ($list['device'] as $device)
            {
                if(!isset($content['from_cache']))
                    $curl->wait();
                echo $device.PHP_EOL;
                $content = $curl->getContent($device);
                if ($content['code'] !== 200)
                    throw new CException("Error at {$device} Http code is {$content['code']}");
                $parser = new DevdbPdaParser($content['content']);
                $result = $parser->parseMain();
                $model = new GoodsModel();
                $model->add(1, $result, $parser->getSourceName());
            }
        }
    }
    
    public function actionImages($type = 1)
    {
        $parser = new PdadbParser();
        $curl = new CurlLayer();
        $filesModel = new FilesModel();
        $imagesModel = new ImagesModel();
        $model = new ImagesModel();
        $goodsModel = new GoodsModel();
        $devices = $goodsModel->getListForImages(1);
        $pattern = "~.*\..*/([\w\d\-_]*)\..*$~";
        shuffle($devices);
        foreach ($devices as $device)
        {
            $link = $parser->search($device['name']);
            if (!$link)
                continue;
            
            if ($images = $parser->getImages($link))
            {
                foreach ($images as $image)
                {
                    $link = basename(str_replace("://", "/", $image));
                    $exp = explode(".", $link);
                    $ext = end($exp);
                    echo $image.PHP_EOL;
                    $filename = $curl->downloadFile($image, $ext);
                    $curl->wait();
                    $fileId = $filesModel->add($filename, $ext);
                    $imagesModel->add($type, $device['id'], $fileId, $image, $link);
                }
            }
        }
    }
    

    public function actionHumanImages($type = 1)
    {
        $parser = new PdadbParser();
        $curl = new CurlLayer();
        $filesModel = new FilesModel();
        $imagesModel = new ImagesModel();
        $model = new ImagesModel();
        $goodsModel = new GoodsModel();
        while (true)
        {
            $query = "select * from temp_images i where i.completed = 0";
            $c = Yii::app()->db;
            $all = $c->createCommand($query)->queryAll();
            foreach ($all as $item)
            {
                $link = basename(str_replace("://", "/", $item['url']));
                $exp = explode(".", $link);
                $ext = end($exp);
                echo $item['id'].": ".$item['url'].PHP_EOL;
                $filename = $curl->downloadFile($item['url'], $ext);
                $curl->wait();
                echo $filename.PHP_EOL;
                if (filesize($filename))
                {
                    $fileId = $filesModel->add($filename, $ext);
                    $imagesModel->add($type, $item['image'], $fileId, $item['url'], $link);
		echo "Успешно добавлено! File id: {$fileId}".PHP_EOL;
		   $sql = "update temp_images set completed = 1 where id = {$item['id']}"; 
                } else {
                   $sql = "update temp_images set completed = 2 where id = {$item['id']}";
		   echo "Файл не удалось скачать! Task id: ({$item['id']})".PHP_EOL;
		}
                $c->createCommand($sql)->execute();
            }
            
            sleep (1);
        }
    }
    
    function actionIrecommendSitemap()
    {
        $curl = new CurlLayer();
        $connection = Yii::app()->db;
        $sitemap = $curl->downloadFile("http://irecommend.ru/sitemap.xml");
        $xml = simplexml_load_file($sitemap);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        unset($json);
        unlink($sitemap);
        $update = "insert into irecommend_sitemaps (url, lastmod) values (:url, :lastmod) on duplicate key update lastmod = :lastmod";
        foreach ($array['sitemap'] as $item)
        {
            $connection->createCommand($update)->execute(array(
                'url'=>$item['loc'],
                'lastmod'=>$item['lastmod'],
            ));
        }
        
    }
    
    function actionIrecommend()
    {
        $select = "select * from irecommend_sitemaps where updated is null or lastmod > updated";
        $update = "update irecommend_sitemaps set updated = NOW()";
        $insert = "insert into irecommend_urls (url, lastmod) values (:url, :lastmod) on duplicate key update lastmod = :lastmod";
        $curl = new CurlLayer();
        $connection = Yii::app()->db;
        $sitemaps = $connection->createCommand($select)->queryAll();
        
        foreach ($sitemaps as $sitemap)
        {
            try
            {
                $tmp = $curl->downloadFile($sitemap['url']);
                $xml = simplexml_load_file($tmp);
                $json = json_encode($xml);
                $array = json_decode($json, true);
                unset($json, $xml);
                unlink($tmp);
                foreach ($array['url'] as $url)
                {
                    $connection->createCommand($insert)->execute(array(
                        'url'=>$url['loc'],
                        'lastmod'=>isset($url['lastmod']) ? $url['lastmod'] : date("Y-m-d H:i:s"),
                    ));
                }
                $connection->createCommand($update)->execute();
                $curl->wait();
            } catch (Exception $ex) {
                throw $ex;
            }   
        }  
    }
    
    
    public function actionIrecommendPages()
    {
        $select = "select * from irecommend_urls";
        $connection = Yii::app()->db;
        $urls = $connection->createCommand($select)->queryAll();
        $curl = new CurlLayer();
        foreach ($urls as $url)
        {
            $folder = "/inktomia/db/analogindex/irecommend/".ceil($url['id']/10000)."/";
            if (!file_exists($folder))
                mkdir ($folder, 0777);
            if (!file_exists($folder.md5($url['id'])))
            {
                $file = $curl->downloadFile($url['url']);
                rename($file, $folder.md5($url['id']));
                $curl->wait();
                echo ".";
            }
        }
        echo PHP_EOL;
    }
    
    
    public function actionIrecommendOpitions()
    {
        $curl = new CurlLayer();
        require('phpQuery.php');
        $pages = "select * from irecommend_urls where id not in (select url from irecommend_pages)";
        $insert = "insert into irecommend_pages (type, url, model, content, title, category) values (:type, :url, :model, :content, :title, :category) on duplicate key update category = :category, type = :type, model = :model, content = :content, title = :title";
        $connection = Yii::app()->db;
        $pages = $connection->createCommand($pages)->queryAll();
        $type = "~http://irecommend.ru/(?P<type>\w+)/.*~";
        foreach($pages as $url)
        {
            if (!preg_match($type, $url['url'], $matches))
                continue;
            $fields = array(
                'type'=>$matches['type'],
                'url'=>$url['id'],
                'model'=>'',
                'content'=>'',
                'title'=>'',
            );
            if ($fields['type'] == 'content')
            {
                
                $folder = "/inktomia/db/analogindex/irecommend/".ceil($url['id']/10000)."/";
                if (!@file_get_contents($folder.md5($url['id'])))
                {
                    if (!file_exists($folder))
                        mkdir ($folder, 0777);
                    if (!file_exists($folder.md5($url['id'])))
                    {
                        $file = $curl->downloadFile($url['url']);
                        rename($file, $folder.md5($url['id']));
                        $curl->wait();
                        echo ".";
                    }
                }
                $content = file_get_contents($folder.md5($url['id']));
                $results = phpQuery::newDocument($content);
                $elements = $results->find('h2.title');
                $fields['model'] = str_replace(" - отзыв", '', pq($elements)->text());
                $elements = $results->find('h1.summary');
                $fields['title'] = pq($elements)->text();
                $elements = $results->find('div.views-field-teaser');
                $elements->find('.social_buttons_wrapper, .add-ticket-button-wrapper')->remove();
                $fields['content'] = pq($elements)->html();
                $elements = $results->find('div.breadcrumb > a:last-child');
                $fields['category'] = pq($elements)->text();
                echo "+";
            }
            $connection->createCommand($insert)->execute($fields);
        }
    }
    
    public function actionOtzovikLists()
    {
        require('phpQuery.php');
        $href = "http://otzovik.com/technology/communication/cellular_phones/";
        $content = $this->_otzovikGetcontent($href);
        $results = phpQuery::newDocument($content);
        $paginator = pq($results)->find(".lpages")->html();
        $pattern = "~<a class=\"npage\" title=\"В конец\" href=\".*/(?P<pages>\d+)/\">»</a>~";
        $replaces = array("Сотовый телефон", "Смартфон", "Мобильный телефон", "Телефон");
        $conn = Yii::app()->db;
        $insert = "insert ignore into otzovik_urls (url) values (:url)";
        if (preg_match($pattern, $paginator, $matches))
        {
            $maxPage = $matches['pages'];
            for($i = 1; $i<$maxPage+1; $i++)
            {
                sleep(rand(1, 5));
                $content = $this->_otzovikGetcontent($href.$i."/");
                $content = phpQuery::newDocument($content);
                $links = pq($content)->find(".nprods > h3");
                foreach ($links as $link)
                {
                    $link = pq($link)->html();
                    foreach($replaces as $r)
                        $link = str_replace($r, '', $link);
                    $conn->createCommand($insert)->execute(array(
                        'url'=>$link,
                    ));
                }
            }
        }
    }
    
    
    public function actionOtzovikPages()
    {
        $select = "select * from otzovik_urls";
        $connection = Yii::app()->db;
        $urls = $connection->createCommand($select)->queryAll();
        $pattern = "~<a href=\"(?P<url>.*)\">~";
        foreach ($urls as $url)
        {
            $folder = "/inktomia/db/analogindex/otzovik/".ceil($url['id']/10000)."/";
            if (!file_exists($folder))
                mkdir ($folder, 0777);
            if (!file_exists($folder.md5($url['id'])))
            {
                if (preg_match($pattern, $url['url'], $matches))
                {
                    file_put_contents($folder.md5($url['id']), $this->_otzovikGetcontent("http://otzovik.com".$matches['url']));
                    sleep(rand(1,2));
                    echo ".";
                }
            }
        }
        echo PHP_EOL;
    }
    
    
    private function _otzovikGetcontent($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_PROXY, '86.51.26.16:3128');
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
}
