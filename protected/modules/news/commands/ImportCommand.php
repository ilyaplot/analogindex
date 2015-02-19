<?php

class ImportCommand extends CConsoleCommand
{
    protected static $goods;
    protected static $tags;
    protected static $brands;
    
    public function actionIndex()
    {
        
        file_put_contents("/var/www/analogindex/logs/news_empty_source.txt", '');
        file_put_contents("/var/www/analogindex/logs/news_empty_paragraph.txt", '');
        file_put_contents("/var/www/analogindex/logs/news_empty_source.txt", '');
            
        $articlesFilter = new ArticlesFilter();
        echo "Загрузка тэгов".PHP_EOL;
        $criteria = new CDbCriteria();
        $criteria->condition = "disabled = 0";
        
        self::$tags = Tags::model()->findAll($criteria);
        unset($criteria);
        
        echo "Загрузка товаров".PHP_EOL;
        self::$goods = Goods::model()->with(array(
                "brand_data", 
                "type_data", 
                "synonims"
            ))->findAll(array(
            "order"=>"LENGTH(t.name) desc",
        ));
        
        echo "Загрузка брендов".PHP_EOL;
        self::$brands = Brands::model()->findAll(array("condition"=>"t.id not in (167) and t.name = 'apple'"));
        
        $urlManager = new UrlManager();

        foreach (self::$tags as $tag) {
 
            $ids = TopicTags::model()->getNewsByTag($tag->name);
            echo $tag->name." (".count($ids).")".PHP_EOL;
            if (empty($ids))
                continue;
            // Разбиваем массив что бы не было больших in списков
            $idsArray = array_chunk($ids, 20);
            
            foreach ($idsArray as $ids) {
                $news = $this->itemsByIds($ids);
                
                
                if (empty($news)) {
                    continue;
                }
                foreach ($news as $url=>$item) {
                    echo ".";
                    $newsCriteria = new CDbCriteria();
                    $newsCriteria->condition = "t.source_url = :url";
                    $newsCriteria->select = "t.id";
                    $newsCriteria->params = ['url'=>$url];
                    if ($news = Articles::model()->find($newsCriteria)) {
                        continue;
                    }
                    $model = new Articles();
                    $model->title = $item['title'];
                    $model->source_content = $item['content'];
                    $model->lang = $item['lang'];
                    $model->created = $item['date'];
                    $model->link = $urlManager->translitUrl($model->title);
                    $model->source_url = $url;
                    if ($model->validate()) {
                        $model->save();
                        $model = $articlesFilter->filter($model);
                        $model->save();
                        echo $tag->name.":::::".$model->title.PHP_EOL;
                        continue;
                        
                    }
                }
                echo PHP_EOL;
            }
        }
        
        foreach (self::$brands as $brand) {
            
            $memory = (!function_exists('memory_get_usage')) ? '' : round(memory_get_usage()/1024/1024, 2) . 'MB';
            echo "Mem:".$memory." ";
            unset($memory);
            
            $name = $brand->name;
            $news = $this->search(" %".$name."% ");
            echo $name.PHP_EOL;
            unset ($name);
            
            foreach ($news as $url=>$item) {
                
                $model = new Articles();
                $model->title = $item['title'];
                $model->source_content = $item['content'];
                $model->lang = $item['lang'];
                $model->created = $item['date'];
                $model->link = $urlManager->translitUrl($model->title);
                $model->source_url = $url;
                
                $id = '';
                
                if ($model->validate()) {
                    if ($model->save()) {
                        $id = $model->id;
                        $model = $articlesFilter->filter($model);
                        $model->save();
                        echo "+";
                    }
                } 
                echo $id.PHP_EOL;
            }
            echo PHP_EOL;
            unset($brand, $news, $url, $item);
        }

        foreach (self::$goods as $product) {
            
            $memory = (!function_exists('memory_get_usage')) ? '' : round(memory_get_usage()/1024/1024, 2) . 'MB';
            echo "Mem:".$memory." ";
            unset($memory);
            
            $name = $product->brand_data->name."% ".$product->name;
            $news = $this->search("%".$name."%");
            echo $name.PHP_EOL;
            unset ($name);
            
            foreach ($news as $url=>$item) {
                
                $model = new Articles();
                $model->title = $item['title'];
                $model->source_content = $item['content'];
                $model->lang = $item['lang'];
                $model->created = $item['date'];
                $model->link = $urlManager->translitUrl($model->title);
                $model->source_url = $url;
                
                $id = '';
                
                if ($model->validate()) {
                    if ($model->save()) {
                        $id = $model->id;
                        $model = $articlesFilter->filter($model);
                        $model->save();
                        echo "+";
                    }
                }
                
            }
            echo PHP_EOL;
            unset($product, $news, $url, $item);
        }
        GoodsArticles::model()->filter();
    }
    
    
    public function search($title)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "t.topic_id, t.topic_title, t.topic_date_add, t.user_id, t.exported";
        $criteria->condition = "t.topic_title LIKE :title and t.exported = 0";
        $criteria->params = ["title"=>$title];
        $criteria->order = "t.topic_date_add desc";

        
        
        $result = [];
        
        try {
            $items = Topics::model()->with(["topic_content"])->findAll($criteria);

            unset ($criteria);
            $ids = [];
            
            foreach ($items as &$item)
            {
                
                if (empty($item->topic_content->source_url) || empty($item->topic_content->topic_text)) {
                    echo "empty {$item->topic_id}".PHP_EOL;
                    unset($item);
                    continue;
                }
                
                $ids[] = $item->topic_id;
                
                $result[$item->topic_content->source_url] = [
                    'id' => $item->topic_id,
                    'title' => $item->topic_title,
                    'url' => $item->topic_content->source_url,
                    'date' => $item->topic_date_add,
                    'lang' => $item->lang,
                    'content' => $item->topic_content->topic_text,
                ];
                
                if (!$item->exported) {
                    $connection = Yii::app()->teta;
                    $query = "update ls_topic set exported = 1 where topic_id = {$item->topic_id}";
                    usleep(1000);
                    try {
                        $connection->createCommand($query)->execute();
                    } catch (CDbException $ex) {
                        
                    }
                }
                
                unset($item, $connection, $query);
            }
            unset ($items);
        } catch (CDbException $ex) {
            echo $ex->getMessage().PHP_EOL;
            unset ($ex, $items, $connection);
            echo "Ошибка базы данных teta, попытка перезапуска соединения.".PHP_EOL;
            try {
                sleep(5);
                Yii::app()->teta->setActive(false);
                Yii::app()->teta->setActive(true);
                echo "Repeat {$title}".PHP_EOL;
                return $this->search($title);
            } catch (CDbException $ex) {
                echo $ex->getMessage().PHP_EOL;
                unset ($ex, $items, $connection);
                echo "Повторная базы данных teta, попытка перезапуска соединения.".PHP_EOL;
                echo "Repeat {$title}".PHP_EOL;
                return $this->search($title);
            }
        }
        return $result;
    }
    
    protected function itemsByIds($ids)
    {

        $criteria = new CDbCriteria();
        $criteria->select = "t.topic_id, t.topic_title, t.topic_date_add, t.user_id, t.exported";
        $criteria->addInCondition("t.topic_id", (array) $ids);
        $criteria->compare('t.exported', 0);
        $criteria->order = "t.topic_date_add desc";
        
        try {
            $items = Topics::model()->with("topic_content")->findAll($criteria);
        } catch (CDbException $ex) {
            
            while (true) {
                echo "Ошибка базы данных teta, попытка перезапуска соединения.".PHP_EOL;
                try {
                    sleep(5);
                    try {
                        Yii::app()->teta->setActive(false);
                        Yii::app()->teta->setActive(true);
                    } catch (CDbException $ex) {
                        echo $ex->getMessage().PHP_EOL;
                        continue;
                    }
                    echo "Repeat items by ids".PHP_EOL;
                    $items = Topics::model()->with("topic_content")->findAll($criteria);
                    break;
                } catch (CDbException $ex) {
                    echo $ex->getMessage().PHP_EOL;
                    echo "Повторная попытка перезапуска соединения c teta.".PHP_EOL;
                }
            }
        }
        
        
        $result = [];
        $ids = [];

        foreach ($items as &$item)
        {
            if (empty($item->topic_content->source_url) || empty($item->topic_content->topic_text)) {
                echo "EMPTY SOURCE URL {$item->topic_id}".PHP_EOL;
                continue;
            }
            $ids[] = $item->topic_id;
            $result[$item->topic_content->source_url] = [
                'id' => $item->topic_id,
                'title' => $item->topic_title,
                'url' => $item->topic_content->source_url,
                'date' => $item->topic_date_add,
                'lang' => $item->lang,
                'content' => $item->topic_content->topic_text,
            ];
            if (!$item->exported) {
                $connection = Yii::app()->teta;
                $query = "update ls_topic set exported = 1 where topic_id = {$item->topic_id}";
                //usleep(100);

                while (true) {
                    try {
                        $connection->createCommand($query)->execute();
                        break;
                    } catch (CDbException $ex) {
                        sleep(5);
                        try {
                            Yii::app()->teta->setActive(false);
                            Yii::app()->teta->setActive(true);
                        } catch (CDbException $ex) {
                            echo $ex->getMessage().PHP_EOL;
                            continue;
                        }
                        $connection = Yii::app()->teta;
                        echo "Repeat update exported items".PHP_EOL;
                        echo $ex->getMessage().PHP_EOL;
                        echo "Повторная попытка перезапуска соединения c teta.".PHP_EOL;
                    }
                }
            }
        }
        
        return $result;
    }
    
   
    protected function hasTag($content, $tag) 
    {
        $content = strip_tags($content);
        $pattern = preg_quote($tag, '/');
        $exp = "/[^\w]{1}{$pattern}[^\w]{1}/isu";
        $result =  preg_match($exp, $content);
        unset($pattern, $tag, $content, $exp);
        return $result;
    }
    
    public function replaceRecursive($content, $pattern, $value, $id=null)
    {
        $exp = "~(<[^aA][^>]*?>[^<\"]*?[^\w\d\-:])({$pattern})([^\w\d\-][^>\"]*?)~iu";
        //"~(.{0,10}[^>\"/\-\w\d\._\[\]#]{1})({$pattern})([^<\"/\-\w\d_\[\]#]{1}.{0,10})~iu"
        if (preg_match_all($exp, $content, $matches, PREG_SET_ORDER)) {
            $match = $matches[0];

            $content = str_replace($match[0], $match[1].$value.$match[3], $content);
            //echo $id." : ". $pattern." : ".$match[0]." : ".$match[1].$value.$match[3].PHP_EOL;
            unset($pattern, $value, $id, $matches);
            return $content;
            
        }
        unset($content, $pattern, $value, $id, $matches);
        return false;
    }
}