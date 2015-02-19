<?php

class DownloadCommand extends CConsoleCommand
{
    
    public function actionDevSpecSitemap()
    {
        $downloader = new Downloader("http://www.devicespecifications.com/", 20);
        $content = $downloader->getContent("http://www.devicespecifications.com/");
        $html = phpQuery::newDocumentHTML($content);
        $brands = pq($html)->find("table.link-table a");
        foreach ($brands as $brand) {
            
            if (!$brandModel = Brands::model()->findByAttributes(['name'=>pq($brand)->text()])) {
                $brandModel = new Brands();
                $brandModel->name = pq($brand)->text();
                if ($brandModel->validate()) {
                    $brandModel->save();
                    echo "Добавлен бренд {$brandModel->name}".PHP_EOL;
                }
            }
            
            $content = $downloader->getContent(pq($brand)->attr("href"));
            $brand = pq($brand)->text();
            $brandHtml = phpQuery::newDocumentHTML($content);
            $lists = pq($brandHtml)->find("div.model-listing-container-80");
            foreach ($lists as $list) {
                $blocks = pq($list)->find('div');
                foreach ($blocks as $block) {
                    $href = pq($block)->find('a:first-child')->attr("href");
                    $product = pq($block)->find('h3>a')->text();
                    $productModel = new Goods();
                    $productModel->brand = $brandModel->id;
                    $productModel->name = $product;
                    $productModel->type = 1;
                    $productModel->source_url = $href;
                    if ($productModel->validate()) {
                        $productModel->save();
                        echo "Добавлен продукт {$brand} {$product}".PHP_EOL;
                    } else {
                        var_dump($productModel->getErrors());
                    }
                    echo PHP_EOL;
                    if (preg_match("/^http:\/\/www\.devicespecifications\.com\/\w+\/model\/(?P<model>\w+)$/isu", $href, $matches)) {
                        $url = "http://www.devicespecifications.com/en/model/{$matches['model']}";
                        $sourceModel = new SourcesDevspec();
                        $sourceModel->url = $url;
                        $sourceModel->lang = 'en';
                        $sourceModel->brand = $brand;
                        $sourceModel->product = $product;
                        if ($sourceModel->validate()) {
                            $sourceModel->save();
                        }

                        $url = "http://www.devicespecifications.com/ru/model/{$matches['model']}";
                        $sourceModel = new SourcesDevspec();
                        $sourceModel->url = $url;
                        $sourceModel->lang = 'ru';
                        $sourceModel->brand = $brand;
                        $sourceModel->product = $product;
                        if ($sourceModel->validate()) {
                            $sourceModel->save();
                        }
                    } else {
                        echo "Не валидная ссылка {$href}".PHP_EOL;
                    }
                }
            }
        }
    }
    
    public function actionDevspec()
    {
        $downloader = new Downloader("http://www.devicespecifications.com/", 10);
        $tasks = SourcesDevspec::model()->findAllByAttributes(['downloaded'=>0]);
        $emptyImageHash = md5_file("/var/www/analogindex/devspec-empty.jpg");
        
        foreach ($tasks as $task) {
            phpQuery::unloadDocuments();
            $url = $task->url;
            $brand = $task->brand;
            $product = $task->product;
            $criteria = new CDbCriteria();
            $criteria->condition = "t.name = :product and brand_data.name = :brand";
            $criteria->params = ['product'=>$product, 'brand'=>$brand];
            $productModel = Goods::model()->with(['brand_data', 'images'])->find($criteria);
            
            if (empty($productModel->brand_data->id)) {
                echo "Не найден продукт {$brand} {$product}.".PHP_EOL;
                $task->downloaded = 2;
                $task->save();
                continue;
            }

            
            if (preg_match("/^http:\/\/www\.devicespecifications\.com\/(?P<lang>\w{2})\/model\/(?P<model>\w{8})$/isu", $url, $matches)) {
                $lang = $matches['lang'];
                $model = $matches['model'];
                
                
                $content = $downloader->getContent($url);
                if (!empty($content)) {
                    $task->writeContent($content);
                    $task->downloaded = 1;
                    $task->save();
                } else {
                    echo "Empty content {$url}".PHP_EOL;
                    $task->downloaded = 2;
                    $task->save();
                    continue;
                }
                
                $html = phpQuery::newDocumentHTML($content); 
                
               
                    // Есть кнопка галереи
                    if (pq($html)->find("a[href='http://www.devicespecifications.com/{$lang}/model-gallery/{$model}']")->length() > 0) {
                        echo "Photo gallery exists.".PHP_EOL;
                        $imagesContent = $downloader->getContent("http://www.devicespecifications.com/{$lang}/model-gallery/{$model}", false);
                        if (!empty($imagesContent)) {
                            $imagesHtml = phpQuery::newDocumentHTML($imagesContent);
                            $images = pq($imagesHtml)->find(".gallery-container > img");
                            
                            foreach($images as $image) {
                                $model = new NImages();
                                $ext = explode(".", pq($image)->attr("src"));
                                $ext = end($ext);
                                $filename = "/tmp/_analogindex_download_".md5("{$brand} {$product}")."{$ext}";
                                $downloader->downloadFile(pq($image)->attr("src"), $filename);
                                if ($id = $model->create($filename, 'goods', "{$brand} {$product}.jpeg", pq($image)->attr("src"), "{$brand} {$product}")) {
                                    if ($model->copyExist == true) {
                                        unlink($filename);
                                    }
                                   
                                    
                                    $gi = new GoodsImagesCopy();
                                    $gi->goods = $productModel->id;
                                    $gi->image = $id;

                                    if ($gi->validate()) {
                                        $gi->save();
                                        echo "Добавлено изображение для товара {$brand} {$product}".PHP_EOL;
                                    }
                                }

                            }

                        }
                        //  Нет галереи, одна картинка
                    } else {
                        $pattern = "/url\((?P<url>http:\/\/www\.devicespecifications\.com\/images\/models\/{$model}\/320\/main\.jpg)\);/isu";
                        if (preg_match($pattern, $content, $matches)) {
                                $model = new NImages();
                            
                                $ext = explode(".", $matches['url']);
                                $ext = end($ext);
                                $file = new Files();
                                $file->name = "{$brand} {$product}.{$ext}";
                                $file->save();
                                $filename = "/tmp/_analogindex_download_".md5("{$brand} {$product}")."{$ext}";
                                $downloader->downloadFile($matches['url'], $filename);
                                if (md5_file($filename)!= $emptyImageHash) {
                                    if ($id = $model->create($filename, 'goods', "{$brand} {$product}.jpeg", $matches['url'], "{$brand} {$product}")) {
                                        if ($model->copyExist == true) {
                                            unlink($filename);
                                        }

                                        if (!$id) {
                                            echo "NOT ID";
                                            continue;
                                        }

                                        $gi = new GoodsImagesCopy();
                                        $gi->goods = $productModel->id;
                                        $gi->image = $id;

                                        if ($gi->validate()) {
                                            $gi->save();
                                            echo "Добавлено изображение для товара {$brand} {$product}";
                                        }
                                    }
                                } else {
                                    echo "Заглушка, удаляем файл".PHP_EOL;
                                    $file->delete();
                                }
                        }
                    }
                    
                
            } else {
                echo "Url {$url} не подходит под регулярное выражение.".PHP_EOL;
                $task->downloaded = 2;
                $task->save();
                continue;
            }
            echo date("Y-m-d H:i:s ")."{$brand} {$product} {$lang} ".PHP_EOL;
            
        }
    }

    public function actionIrecommendSitemap()
    {
        $downloader = new Downloader("http://irecommend.ru/", 50);
        
        $lastPage = SourcesIrecommend::model()->getLastUrl("phones");
        
        $list = [];
        $page = "http://irecommend.ru/taxonomy/term/55/reviews";
        $content = $downloader->getContent($page);
        $html = phpQuery::newDocumentHTML($content);
        $last = pq($html)->find("li.pager-last > a")->attr("href");
        $last = preg_replace("/.*reviews\?page=(?P<last>\d+)/isu", "$1", $last);
        phpQuery::unloadDocuments();
        
        $list = array_merge($list, $this->_getIrecommendUrlList($content));
                
        if (!$last) {
            echo "Не удалось получить список страниц".PHP_EOL;
            exit();
        }

        for($i = 1; $i < $last; $i++) {
            $content = $downloader->getContent("http://irecommend.ru/taxonomy/term/55/reviews?page={$i}");
            $list = array_merge($list, $this->_getIrecommendUrlList($content));
            if (in_array($lastPage, $list)) {
            //    break;
            }
            
            if (SourcesIrecommend::model()->checkExists('phones', $list[count($list)-1])) {
                break;
            }
            
            echo "{$i} of {$last}".PHP_EOL;
        }

        $list = array_unique($list);
        foreach($list as $url) {
            $source = new SourcesIrecommend();
            $source->url = $url;
            $source->type = "phones";
            if ($source->validate()) {
                $source->save();
            } else {
                var_dump($source->getErrors());
                if (!empty($source->getError("url"))) {
                    break;
                }
            }
        }
        
        
        $downloader = new Downloader("http://irecommend.ru/", 50);
        
        $lastPage = SourcesIrecommend::model()->getLastUrl("tablets");
        
        $list = [];
        $page = "http://irecommend.ru/taxonomy/term/88/reviews?tid=228032";
        $content = $downloader->getContent($page);
        $html = phpQuery::newDocumentHTML($content);
        $last = pq($html)->find("li.pager-last > a")->attr("href");
        $last = preg_replace("/.*reviews\?page=(?P<last>\d+)\&.*/isu", "$1", $last);
        phpQuery::unloadDocuments();
        
        $list = array_merge($list, $this->_getIrecommendUrlList($content));
                
        if (!$last) {
            echo "Не удалось получить список страниц".PHP_EOL;
            exit();
        }

        for($i = 1; $i < $last; $i++) {
            $content = $downloader->getContent("http://irecommend.ru/taxonomy/term/88/reviews?page={$i}&tid=228032");
            $list = array_merge($list, $this->_getIrecommendUrlList($content));
            if (in_array($lastPage, $list)) {
                //break;
            }
            
            if (SourcesIrecommend::model()->checkExists('phones', $list[count($list)-1])) {
                //break;
            }
            
            echo "{$i} of {$last}".PHP_EOL;
        }
        
        $list = array_unique($list);
        foreach($list as $url) {
            $source = new SourcesIrecommend();
            $source->url = $url;
            $source->type = "tablets";
            if ($source->validate()) {
                $source->save();
            } else {
                var_dump($source->getErrors());
                if (!empty($source->getError("url"))) {
                    //break;
                }
            }
        }

    }
    
    public function actionIrecommend()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "downloaded = 0";
        $criteria->order = "created asc";
        //$criteria->limit = "50";
        $urls = SourcesIrecommend::model()->findAll($criteria);
        $downloader = new Downloader("http://irecommend.ru/", 5);
        foreach ($urls as $url) {
            echo $url->url.PHP_EOL;
            if ($downloader->downloadFile($url->url, $url->getFilename(), [403, 404]) == true) {
                $url->downloaded = 1;
                $url->size = filesize($url->getFilename());
            } else {
                $url->downloaded = 2;
            }
            if ($url->validate()) {
                echo "+";
                $url->save();
            }
        }
        echo PHP_EOL;
    }

    protected function _getIrecommendUrlList($content)
    {
        $result = [];
        $html = phpQuery::newDocumentHTML($content);
        $list = pq($html)->find("div.list-reviews");
        foreach($list as $block) {
            $result[] = "http://irecommend.ru".pq($block)->find("h3.review-head > a")->attr("href");
        }
        phpQuery::unloadDocuments();
        return $result;
    }
    
    
    public function actionPhonearenaRss()
    {
        $feed = "http://www.phonearena.com/rss/rss_phones.php";
        $downloader = new Downloader($feed, 1);
        $xml = $downloader->getContent($feed);
        $xml = new SimpleXMLElement($xml);
        $counter = 0;
        foreach ($xml->channel->item as $item) {
            $model = new PhonearenaUrls();
            $model->url = str_replace("http://www.phonearena.com", '', $item->link);
            $model->downloaded = 0;
            $model->parsed = 0;
            if ($model->validate()) {
                $model->save();
                $counter++;
            }
        }
        echo "Добавлено {$counter} URL.".PHP_EOL;
    }
    
    public function actionPhonearenaBrands()
    {
        $page = "http://www.phonearena.com/phones/manufacturers";
        $downloader = new Downloader($page, 10);
        $content = $downloader->getContent($page);
        $html = phpQuery::newDocumentHTML($content);
        $items = pq($html)->find("div.s_block_4_s115");
        foreach($items as $item) {
            $brand = pq($item)->find("span.title")->text();
            $image = pq($item)->find("a.s_thumb > img")->attr("src");
            if (!empty($brand) && !empty($image)) {
                if ($brand = Brands::model()->findByAttributes(['name'=>$brand, 'logo'=>0])) {
                    $filename = tempnam("/tmp", "_analogindex_brand");
                    echo $filename.PHP_EOL;
                    if ($downloader->downloadFile($image, $filename)) {
                        $brand->setFile($filename);
                        echo $brand->name.PHP_EOL;
                        sleep(1);
                    }
                }
            }
        }

    }

    public function actionPhonearena()
    {
        $downloader = new Downloader("http://www.phonearena.com/", 10);
        $list = PhonearenaUrls::model()->getDownloadList();
        foreach ($list as $item) {
            sleep(rand(1,3));
            if (!preg_match("/.*\/fullspecs$/isu", $item->fullurl)) {
                $item->fullurl .= "/fullspecs";
            }
            
            $content = $downloader->getContent($item->fullurl);
            
            if ($content) {
                $item->setContent($content);
                echo $item->id." ".$item->fullurl.PHP_EOL;
                if ($url = preg_replace("/(.*)\/fullspecs$/isu", "$1/photos", $item->fullurl)) {
                    sleep(rand(1,2));
                    $content = $downloader->getContent($url);
                    if ($content) {
                        $item->setPhotos($content);
                        echo $item->id." ".$url.PHP_EOL;
                    }
                }
            }
        }
    }
    
    public function actionAntutu($type = 1)
    {
        $types = array(
            1 => array(
                "source" => "mobile",
                "system" => 1,
            ),
            2 => array(
                "source" => "pad",
                "system" => 2,
            ),
        );
        $url = "http://www.antutu.com/en/Ranking.shtml?cmd={$types[$type]['source']}&page=";
        $startPage = 1;
        $content = $this->getContent($url . $startPage);
        if (!$content)
            exit();
        $html = phpQuery::newDocumentHTML($content);
        $lastUrl = pq($html)->find(".pagination>strong:last-child>a")->attr("href");
        $lastUrl = explode("=", $lastUrl);
        $maxPage = end($lastUrl);
        echo $maxPage . PHP_EOL;
        $page = 1;
        do {
            $page++;
            $devices = pq($html)->find("div.rank>ul>li");
            foreach ($devices as $device) {
                $name = pq($device)->find("div.fl.mobiletext>div.mobileT>a")->text();
                $value = pq($device)->find("div.fl.mobiletext>div.score>div.fl")->text();
                $value = intval(str_replace("Score:", '', $value));
                $criteria = new CDbCriteria();
                $criteria->condition = "(CONCAT(brand_data.name, ' ', t.name) LIKE :search "
                        . "OR CONCAT(brand_data.name, ' ', synonims.name) LIKE :search) ";
                $search = $name;
                $criteria->params = array(
                    "search" => $search,
                );
                echo $search . PHP_EOL;

                $product = Goods::model()->with("brand_data", "synonims")->find($criteria);
                if ($product) {
                    echo $search . ": ";
                    if (!$rank = GoodsRanking::model()->findByAttributes(array("source" => "antutu", "goods" => $product->id)))
                        $rank = new GoodsRanking();
                    $rank->source = "antutu";
                    $rank->goods = $product->id;
                    $rank->value = $value;
                    $rank->type = $types[$type]['system'];
                    $rank->save();
                    echo $rank->id . PHP_EOL;
                }
            }
            $content = $this->getContent($url . $page);
            if (!$content)
                exit();
            $html = phpQuery::newDocumentHTML($content);
            sleep(1);
        } while ($page < $maxPage + 1);
        echo "Done" . PHP_EOL;
    }

    public function actionSmartphoneuaList($source)
    {
        $sources = array(
            1 => "http://www.smartphone.ua/phones/",
            2 => "http://www.smartphone.ua/tablet-pc/",
            3 => "http://www.smartphone.ua/e-books/",
        );
        $brandsPage = $this->getContent($sources[$source]);
        if (!$brandsPage) {
            echo "No main page. Exit.";
            exit();
        }
        $html = phpQuery::newDocumentHTML($brandsPage);
        $brands = array();
        $menu = pq($html)->find("#firms li > a");
        foreach ($menu as $menuItem) {
            $brands[] = pq($menuItem)->attr("href");
        }
        $brands = array_unique($brands);
        shuffle($brands);
        $urls = array();
        foreach ($brands as $brand) {
            sleep(1);
            $content = $this->getContent($brand);
            if (!$content) {
                echo "ERROR download brand's page. {$brand}" . PHP_EOL;
                continue;
            }

            $urls = array_merge($urls, $this->_getSmartphoneuaItems($content));
            $html = phpQuery::newDocumentHtml($content);
            $pages = pq($html)->find("div.pages > a.digit");
            foreach ($pages as $page) {
                sleep(1);
                $href = pq($page)->attr("href");
                $page = $brand . substr($href, 2, strlen($href));
                $content = $this->getContent($page);
                if (!$content) {
                    echo "ERROR download brand's subpage. {$page}" . PHP_EOL;
                    continue;
                }
                $urls = array_merge($urls, $this->_getSmartphoneuaItems($content));
            }
        }
        foreach ($urls as $url) {
            echo ".";
            $model = new SourcesSmartphoneua();
            $model->url = $url;
            if ($model->validate())
                $model->save();
            else
                var_dump($model->getErrors());
        }
        echo PHP_EOL;
    }

    public function actionGsmarenaList()
    {
        $brandsPage = $this->getContent("http://www.gsmarena.com/makers.php3");
        if (!$brandsPage) {
            echo "No main page. Exit.";
            exit();
        }
        $html = phpQuery::newDocumentHTML($brandsPage);
        $menu = pq($html)->find(".st-text tr td > a");
        $brands = array();
        foreach ($menu as $menuItem) {
            $brands[] = "http://www.gsmarena.com/" . pq($menuItem)->attr("href");
        }
        $brands = array_unique($brands);
        shuffle($brands);
        $urls = array();
        foreach ($brands as $brand) {
            sleep(1);
            $content = $this->getContent($brand);
            if (!$content) {
                echo "ERROR download brand's page. {$brand}" . PHP_EOL;
                continue;
            }

            $urls = array_merge($urls, $this->_getGsmarenaItems($content));

            $html = phpQuery::newDocumentHtml($content);
            $pages = pq($html)->find("div.nav-pages > a");
            foreach ($pages as $page) {
                sleep(1);
                $page = "http://www.gsmarena.com/" . pq($page)->attr("href");
                $content = $this->getContent($page);
                if (!$content) {
                    echo "ERROR download brand's subpage. {$page}" . PHP_EOL;
                    continue;
                }
                $urls = array_merge($urls, $this->_getGsmarenaItems($content));
            }
        }
        foreach ($urls as $url) {
            echo ".";
            $model = new SourcesGsmarena();
            $model->url = $url;
            if ($model->validate())
                $model->save();
            else
                var_dump($model->getErrors());
        }
        echo PHP_EOL;
    }

    public function actionGsmArena()
    {
        $urls = SourcesGsmarena::model()->findAllByAttributes(array(
            "file" => 0
        ));
        foreach ($urls as $url) {
            sleep(1);
            echo ".";
            $content = $this->getContent($url->url);
            if (!$content) {
                echo "Not downloaded {$url->url}";
                continue;
            }
            $file = new SourcesGsmarenaFiles();
            $file->save();
            if (!$file->putFile($content)) {
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
            echo ".";
        }
        echo PHP_EOL;
    }

    public function actionSmartphoneua()
    {
        $urls = SourcesSmartphoneua::model()->findAllByAttributes(array(
            "file" => 0
        ));
        foreach ($urls as $url) {
            sleep(1);
            echo ".";
            $content = $this->getContent($url->url);
            if (!$content) {
                echo "Not downloaded {$url->url}";
                continue;
            }
            $file = new SourcesSmartphoneuaFiles();
            $file->save();
            if (!$file->putFile($content)) {
                echo "Not put downloaded {$url->url}";
                $file->delete();
                continue;
            }
            $file->size = $file->getFilesize();
            $file->mime_type = $file->getMimeType();
            $file->name = "smartphoneua.html";
            $file->save();
            $url->file = $file->id;
            $url->save();
            echo ".";
        }
        echo PHP_EOL;
    }

    private function _getSmartphoneuaItems($content)
    {
        $html = phpQuery::newDocumentHTML($content);
        $items = pq($html)->find("#ph_list div > a.green");
        $urls = array();
        foreach ($items as $item) {
            $urls[] = pq($item)->attr("href");
        }

        return array_unique($urls);
    }

    private function _getGsmarenaItems($content)
    {
        $html = phpQuery::newDocumentHTML($content);
        $items = pq($html)->find("div.makers ul > li a");
        $urls = array();
        foreach ($items as $item) {
            $urls[] = "http://www.gsmarena.com/" . pq($item)->attr("href");
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
        if (curl_errno($ch)) {
            echo "Curl error " . curl_error($ch) . PHP_EOL;
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
        if (curl_errno($ch)) {
            echo "Curl error " . curl_error($ch) . PHP_EOL;
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
