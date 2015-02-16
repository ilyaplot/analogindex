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
        //exit(0);
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
    
    public function actionFilterThread()
    {
        //exit(0);
        $criteria = new CDbCriteria();
        $criteria->condition = "has_filtered = 0 and id > ".rand(1,145330);
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
            b.link as brand,
            concat(b.name, ' ', g.name) as alt
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
            if (!file_exists($image->filename)){
                continue;
            }
            $ext = explode(".", $image->name);
            $ext = end($ext);
            $filename = "/tmp/_move_".md5($image->name).".{$ext}";
            copy($image->filename, $filename);
            if ($id = $model->create($filename, 'goods', "{$image->brand}_{$image->product}.jpeg", $image->source_url, $image->alt)) {
                if ($model->copyExist == true) {
                    unlink($filename);
                }
                if (!$id) {
                    echo "NOT ID";
                    exit();
                }
                $gi = new GoodsImagesCopy();
                $gi->goods = $image->goods;
                $gi->image = $id;
                
                if ($gi->validate()) {
                    $gi->save();
                }
            }
        }
    }
    
    public function actionGallery()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 't.goods = :goods';
        $criteria->params = ['goods'=>'4415'];
        $criteria->order = 't.id asc';
        $gallery = Gallery::model()->with(['image_data'])->findAll($criteria);
        foreach ($gallery as $galleryItem) {
            echo $galleryItem->image_data->getHtml('130x130').PHP_EOL;
        }
    }
}
