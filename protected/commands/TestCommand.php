<?php

class TestCommand extends CConsoleCommand
{
    public function beforeAction($action, $params)
    {
        date_default_timezone_set("Europe/Moscow");
        return parent::beforeAction($action, $params);
    }
    
    public function actionFilter()
    {
        $criteria = new CDbCriteria();
        $criteria->order = "id desc";
        $criteria->condition = "has_filtered = 0";
        $criteria->limit = 15;
        $criteria->condition = 'id = 147985';
        $articles = Articles::model()->findAll($criteria);
        $filter = new ArticlesFilter();
        
        foreach($articles as $article) {
            echo date("Y-m-d H:i:s ").$article->id.PHP_EOL;
            $article = $filter->filter($article);
            $article->save();
        }
        GoodsArticles::model()->filter();
        echo PHP_EOL;
    }
    
    public function actionRemoveImages()
    {
        $images = ArticlesImages::model()->findAll();
        foreach ($images as $image) {
            if (!$image->fileExists()) {
                echo "-";
                $image->delete();
            }
        }
        echo "END".PHP_EOL;
    }
    
    public function actionBrandImages()
    {
        include_once 'WideImage/WideImage.php';
        $list = file_get_contents("./logos.txt");
        $list = explode(PHP_EOL, $list);
        unset($list[count($list)-1]);

        $list = array_map(function($value){
            return explode(" ", $value);
        }, $list);
        $downloader = new Downloader("http://ya.ru/", 5);
        foreach ($list as $link) {
            if ($brand = Brands::model()->findByAttributes(['name'=>$link[0]])) {
                echo $link[0].PHP_EOL;
                $filename = tempnam("/tmp", '_brand');
                $downloader->downloadFile(trim($link[1]), $filename);
                $mime = mime_content_type($filename);
                $newFile = $filename.".".preg_replace("~image/(.*)~isu", "$1", $mime);
                rename($filename, $newFile);
                $filename = $newFile;
                WideImage::load($filename)->resize(150, 150)->saveToFile($filename);
                $brand->setFile($filename);
            }
        }
    }
}
