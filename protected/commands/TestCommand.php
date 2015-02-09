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
        //$criteria->condition = 'id = 142283';
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
    
    public function actionImage()
    {
        $connection = Yii::app()->db;
        $query = "select 
            concat('/inktomia/db/analogindex/storage/', ceil(f.id / 10000) , '/', md5(f.id), '.file') as filename,
            f.id as file,
            f.name as name,
            i.source as source_url,
            i.size2,
            i.size3,
            i.size4,
            i.size6,
            gi.goods as goods,
            g.link as product,
            b.link as brand
            from ai_images i
            inner join ai_files f on f.id = i.file
            inner join ai_goods_images gi on gi.image = i.id
            inner join ai_goods g on g.id = gi.goods
            inner join ai_brands b on b.id = g.brand
            ";
        $images = $connection->createCommand($query)->queryAll();
        foreach ($images as $image) {
            $model = new NImages();
            $image = (object) $image;
            copy($image->filename, "/tmp/_move_{$image->name}");
            if ($model->create("/tmp/_move_{$image->name}", 'goods', "{$image->brand}_{$image->product}.jpeg", $image->source_url)) {
                $gi = new GoodsImagesCopy();
                $gi->goods = $image->goods;
                $gi->image = $model->id;
                
                if ($gi->save()) {
                    
                    $ext = explode(".", $image->name);
                    $ext = end($ext);
                    
                    $redirect = new Redirects();
                    $redirect->from = "/_image/id{$image->file}/{$image->name}";
                    $redirect->to = "/image/{$model->id}/1024x1024/{$image->brand}_{$image->product}.jpeg";
                    $redirect->save();
                    
                    $redirect = new Redirects();
                    $redirect->from = "/_image/id{$image->file}/{$image->brand}_{$image->product}.{$ext}";
                    $redirect->to = "/image/{$model->id}/1024x1024/{$image->brand}_{$image->product}.jpeg";
                    $redirect->save();
                    
                    echo "/_image/id{$image->file}/{$image->brand}_{$image->product}.{$ext}".PHP_EOL;
                    echo "/image/{$model->id}/1024x1024/{$image->brand}_{$image->product}.jpeg".PHP_EOL;
                    echo "/_image/id{$image->size3}/{$image->brand}_{$image->product}.{$ext}".PHP_EOL;
                    echo "/_image/id{$image->file}/{$image->name}".PHP_EOL;
                    
                    if (!empty($image->size2)) {
                        $redirect = new Redirects();
                        $redirect->from = "/_image/id{$image->size2}/{$image->brand}_{$image->product}.{$ext}";
                        $redirect->to = "/image/{$model->id}/510x510/{$image->brand}_{$image->product}.jpeg";
                        $redirect->save();
                        
                        $redirect = new Redirects();
                        $redirect->from = "/_image/id{$image->size2}/{$image->name}";
                        $redirect->to = "/image/{$model->id}/510x510/{$image->brand}_{$image->product}.jpeg";
                        $redirect->save();
                    }
                    
                    if (!empty($image->size3)) {
                        $redirect = new Redirects();
                        $redirect->from = "/_image/id{$image->size3}/{$image->brand}_{$image->product}.{$ext}";
                        $redirect->to = "/image/{$model->id}/91x91/{$image->brand}_{$image->product}.png";
                        $redirect->save();
                        
                        $redirect = new Redirects();
                        $redirect->from = "/_image/id{$image->size3}/{$image->name}";
                        $redirect->to = "/image/{$model->id}/91x91/{$image->brand}_{$image->product}.png";
                        $redirect->save();
                    }
                    
                    if (!empty($image->size4)) {
                        $redirect = new Redirects();
                        $redirect->from = "/_image/id{$image->size4}/{$image->brand}_{$image->product}.{$ext}";
                        $redirect->to = "/image/{$model->id}/30x37/{$image->brand}_{$image->product}.gif";
                        $redirect->save();
                        
                        $redirect = new Redirects();
                        $redirect->from = "/_image/id{$image->size4}/{$image->name}";
                        $redirect->to = "/image/{$model->id}/30x37/{$image->brand}_{$image->product}.gif";
                        $redirect->save();
                    }
                    
                    if (!empty($image->size6)) {
                        $redirect = new Redirects();
                        $redirect->from = "/_image/id{$image->size6}/{$image->brand}_{$image->product}.{$ext}";
                        $redirect->to = "/image/{$model->id}/130x130/{$image->brand}_{$image->product}.png";
                        $redirect->save();
                        
                        $redirect = new Redirects();
                        $redirect->from = "/_image/id{$image->size6}/{$image->name}";
                        $redirect->to = "/image/{$model->id}/130x130/{$image->brand}_{$image->product}.png";
                        $redirect->save();
                    }
                }
            }
        }
    }
}
