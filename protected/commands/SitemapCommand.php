<?php

/**
 * Создает сайтмап для всего сайта
 */
class SitemapCommand extends ConsoleCommand
{
    private $_sitemapDirectory;
    
    
    public function beforeAction($action, $params)
    {
        $this->_sitemapDirectory = "/var/www/analogindex/www/sitemaps/";

        if (!is_dir($this->_sitemapDirectory) || !is_writable($this->_sitemapDirectory)) {
            $this->Log("Папка {$this->_sitemapDirectory} не существует или недоступна для записи.");
            exit();
        }
        date_default_timezone_set("Europe/Moscow");
        return parent::beforeAction($action, $params);
    }

    public function actionIndex()
    {
        
        $domains = [
            'ru'=>'ru', 
            'en'=>'com'
        ];
        $criteria = new CDbCriteria();
        $criteria->select = "t.link, brand_data.link, type_data.link, t.updated";

        $products = Goods::model()->with([
            "brand_data", 
            "type_data",
            "gallery_count",
        ])->findAll($criteria);
        
        $criteria = new CDbCriteria();
        $criteria->select = "t.link";

        $brands = Brands::model()->findAll($criteria);
        
        foreach ($domains as $lang=>$domain) {
            Yii::app()->language = $lang;
            $urls = [
                [
                    'url'=>"http://analogindex.{$domain}/",
                    'lastmod'=>date("Y-m-d\TH:i:s+00:00"),
                ]
            ];
            
            foreach($products as $product) {
                $urls[] = [
                    'url'=>str_replace("+", "%2B", "http://analogindex.{$domain}/{$product->type_data->link}/{$product->brand_data->link}/{$product->link}.html"),
                    'lastmod'=>date("Y-m-d\TH:i:s+00:00", strtotime($product->updated)),
                ];
                    
                if ($product->gallery_count > 0) {
                    $urls[] = [
                        'url'=>str_replace("+", "%2B", "http://analogindex.{$domain}/gallery/{$product->brand_data->link}_{$product->link}.html"),
                    ];
                }
                
            }
            
            foreach($brands as $brand) {
                
                if (empty($brand->link)) {
                    continue;
                }
                
                $urls[] = [
                    'url'=>str_replace("+", "%2B", "http://analogindex.{$domain}/brand/{$brand->link}.html"),
                ];
            }
            
            $criteria = new CDbCriteria();
            $criteria->condition = "t.lang = :lang";
            $criteria->params = ['lang'=>$lang];
            $criteria->select = "t.link, t.id";

            $articles = Articles::model()->findAll($criteria);
            
            foreach ($articles as $article) {
                $urls[] = [
                    'url'=>str_replace("+", "%2B", "http://analogindex.{$domain}/{$article->type}/{$article->link}_{$article->id}.html"),
                ];
            }
            
            $criteria = new CDbCriteria();
            $criteria->condition = "t.lang = :lang and disabled = 0";
            $criteria->params = ['lang'=>$lang];
            $criteria->select = "t.link, t.id, goods_data.link";
            $reviews = Reviews::model()->with(['goods_data'])->findAll($criteria);
            
            foreach ($reviews as $review) {
                if (empty($review->goods_data->link)) {
                    continue;
                }
                $urls[] = [
                    'url'=>str_replace("+", "%2B", "http://analogindex.{$domain}/review/{$review->goods_data->link}/{$review->link}_{$review->id}.html"),
                ];
            }
            
            echo $domain.": ".count($urls).PHP_EOL;
            
            $sitemapList = array_chunk($urls, 50000);
            $sitemaps = [];
            
            foreach ($sitemapList as $id=>$urls) {
                $this->_createSitemap($id, $domain, $urls);
                $sitemaps[] = [
                    'loc'=>"http://analogindex.{$domain}/sitemaps/{$domain}.sitemap{$id}.xml.gz",
                    'lastmod'=> date("Y-m-d\TH:i:s+00:00"),
                ];
            }
            $this->_createSitemapIndex($sitemaps, $domain);
        }
    }
    
    private function _createSitemap($id, $domain, $urls)
    {
        $filename = $this->_sitemapDirectory . "{$domain}.sitemap";
        $dom = new domDocument("1.0", "utf-8");
        $root = $dom->createElement("urlset");
        $root->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
        foreach ($urls as $url) {
            $urlNode = $dom->createElement("url");
            $locNode = $dom->createElement("loc", $url['url']);
            if (!empty($url['lastmod'])) {
                $lastModNode = $dom->createElement("lastmod", $url['lastmod']);
                $urlNode->appendChild($lastModNode);
            }
            $urlNode->appendChild($locNode);
            $root->appendChild($urlNode);
        }
        $dom->appendChild($root);
        $dom->save($filename);
        try {
            $fp = gzopen("{$filename}{$id}.xml.gz", 'w');
            gzwrite($fp, file_get_contents($filename));
            gzclose($fp);
        } catch (Exception $ex) {
            throw $ex;
        }

        unlink($filename);
    }
    
    private function _createSitemapIndex($sitemaps, $domain)
    {
        $filename = $this->_sitemapDirectory . "{$domain}.sitemapindex.xml";
        $dom = new domDocument("1.0", "utf-8");
        $root = $dom->createElement("sitemapindex");
        $root->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
        foreach ($sitemaps as $sitemap) {
            $sitemapNode = $dom->createElement("sitemap");
            $locNode = $dom->createElement("loc", $sitemap['loc']);
            $sitemapNode->appendChild($locNode);
            if (!empty($sitemap['lastmod'])) {
                $lastModNode = $dom->createElement("lastmod", $sitemap['lastmod']);
                $sitemapNode->appendChild($lastModNode);
            }
            $root->appendChild($sitemapNode);
        }
        $dom->appendChild($root);
        $dom->save($filename);
        echo $filename.PHP_EOL;
    }
}
