<?php

class DownloadCommand extends CConsoleCommand
{
    
    public function actionPhonearena()
    {
        $referer = '';
        $list = PhonearenaUrls::model()->getDownloadList();
        foreach ($list as $item) {
            sleep(rand(2,3));
            if (!preg_match("/.*\/fullspecs$/isu", $item->fullurl)) {
                $item->fullurl .= "/fullspecs";
            }
            $ch = curl_init($item->fullurl);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_REFERER, $referer);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $content = curl_exec($ch);
            $referer = $item->fullurl;
            
            if (curl_errno($ch)) {
                echo "Curl error #".curl_errno($ch)." " . curl_error($ch)." " .$item->fullurl. PHP_EOL;
                
            } else {
                $item->setContent($content);
                echo $item->id." ".$item->fullurl.PHP_EOL;
                if ($url = preg_replace("/(.*)\/fullspecs$/isu", "$1/photos", $item->fullurl)) {
                    sleep(rand(1,2));
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_REFERER, $referer);
                    $content = curl_exec($ch);
                    if (curl_errno($ch)) {
                        echo "Curl error #".curl_errno($ch)." " . curl_error($ch)." " .$url. PHP_EOL;
                    } else {
                        $item->setPhotos($content);
                        echo $item->id." ".$url.PHP_EOL;
                    }
                }
            }
            
            
            curl_close($ch);
            
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
