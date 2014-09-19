<?php
/**
 * Создает сайтмап для всего сайта
 */
class SitemapCommand extends ConsoleCommand
{
    private $_sitemapDirectory;
    
    public function beforeAction($action, $params)
    {
        $this->_sitemapDirectory = Yii::app()->basePath."/runtime/sitemaps/";
        
        if (!is_dir($this->_sitemapDirectory) || !is_writable($this->_sitemapDirectory))
        {
            $this->Log("Папка {$this->_sitemapDirectory} не существует или недоступна для записи.");
            exit();
        }
        date_default_timezone_set("Europe/Moscow");
        return parent::beforeAction($action, $params);
    }
    
    public function actionIndex()
    {
        $goods = Goods::model()->with(array("brand_data", "type_data"))->findAll();
        $links = array(
            "http://analogindex.ru/index.html",
            "http://analogindex.com/index.html",
        );
        foreach ($goods as $item)
        {
            echo ".";
            $links[] = "http://analogindex.ru/".$item->type_data->link."/".$item->brand_data->link."/".$item->link.".html";
            $links[] = "http://analogindex.com/".$item->type_data->link."/".$item->brand_data->link."/".$item->link.".html";
        }
        echo PHP_EOL;
        $this->_createSitemap($links);
    }
    
    private function _createSitemap($urlset)
    {
        $filename = $this->_sitemapDirectory."sitemap";
        $dom = new domDocument("1.0", "utf-8");
        $root = $dom->createElement("urlset");
        $root->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
        foreach ($urlset as $url)
        {
            $urlNode = $dom->createElement("url");
            $locNode = $dom->createElement("loc", $url);
            $urlNode->appendChild($locNode);
            $root->appendChild($urlNode);
        }
        $dom->appendChild($root);
        $dom->save($filename);
        try {
            $fp = gzopen($filename.".xml.gz", 'w');
            gzwrite ($fp, file_get_contents($filename));
            gzclose($fp);
        } catch (Exception $ex) {
            throw $ex;
        }
       
        unlink($filename);
    }
}