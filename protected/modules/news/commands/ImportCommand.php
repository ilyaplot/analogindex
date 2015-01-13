<?php

class ImportCommand extends CConsoleCommand
{
    protected static $goods;
    protected static $tags;
    protected static $brands;
    
    public function actionIndex()
    {
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
            "order"=>"LENGTH(t.name) desc"
        ));
        
        echo "Загрузка брендов".PHP_EOL;
        self::$brands = Brands::model()->findAll(array("condition"=>"t.id not in (167)"));
        
        $urlManager = new UrlManager();
        
        
        foreach (self::$goods as $product) {
            
            $memory = (!function_exists('memory_get_usage')) ? '' : round(memory_get_usage()/1024/1024, 2) . 'MB';
            echo "Mem:".$memory." ";
            unset($memory);
            
            $name = $product->brand_data->name."% ".$product->name;
            $news = $this->search("%".$name."%");
            echo $name.PHP_EOL;
            unset ($name);
            
            foreach ($news as $url=>$item) {
                
                $model = new News();
                $model->title = $item['title'];
                $model->content = $item['content'];
                $model->lang = $item['lang'];
                $model->created = $item['date'];
                $model->link = $urlManager->translitUrl($model->title);
                $model->source_url = $url;
                
                $id = '';
                
                if ($model->validate()) {
                    if ($model->save()) {
                        $id = $model->id;
                        
                        echo "+";
                    }
                } else {
                    $model = News::model()->findByAttributes(['source_url'=>$url]);
                    $model->content = $item['content'];
                    $model->save();
                    echo "u";
                    if (!empty($model)) {
                        $id = $model->id;
                    }
                }
                echo $id.PHP_EOL;
                if (!empty($id)) {
                    $this->news_filter($model);
                    $this->news_tag($model);
                    unset($model, $id);
                }
                
                
            }
            echo PHP_EOL;
            unset($product, $news, $url, $item);
        }
        GoodsNews::model()->filter();
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
            $items = Topics::model()->with("topic_content")->findAll($criteria);
            unset ($criteria);
            $ids = [];
            
            foreach ($items as &$item)
            {
                if (empty($item->topic_content->source_url) || empty($item->topic_content->topic_text)) {
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
                    $connection->createCommand($query)->execute();
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
        //$criteria->compare('t.exported', 0);
        $criteria->order = "t.topic_date_add desc";

        $items = Topics::model()->with("topic_content")->findAll($criteria);
        
        $result = [];
        $ids = [];
        foreach ($items as &$item)
        {
            if (empty($item->topic_content->source_url) || empty($item->topic_content->topic_text)) {
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
                $connection->createCommand($query)->execute();
            }
        }
        
        return $result;
    }
    
    public function actionTags()
    {
        $urlManager = new UrlManager();
        $tags = Tags::model()->findAllByAttributes(['disabled'=>0]);
        
        $gmc= new GearmanClient();
        $gmc->addServer();
        
        
        foreach ($tags as $tag) {
            
            $ids = TopicTags::model()->getNewsByTag($tag->name);
            echo $tag->name." (".count($ids).")".PHP_EOL;
            if (empty($ids))
                continue;
            // Разбиваем массив что бы не было больших in списков
            $idsArray = array_chunk($ids, 20);
            
            foreach ($idsArray as $ids) {
                $news = $this->itemsByIds($ids);
                foreach ($news as $url=>$item) {
                    echo ".";
                    if ($news = News::model()->findByAttributes(['source_url'=>$url])) {
                        $newsTags = new NewsTags();
                        $newsTags->news = $news->id;
                        $newsTags->tag = $tag->id;
                        if ($newsTags->validate()) {
                            $newsTags->save();
                        }
                        continue;
                    }
                    $model = new News();
                    $model->title = $item['title'];
                    $model->content = $item['content'];
                    $model->lang = $item['lang'];
                    $model->created = $item['date'];
                    $model->link = $urlManager->translitUrl($model->title);
                    $model->source_url = $url;
                    if ($model->validate()) {
                        $model->save();
                        
                        $this->news_filter($model);
                        $this->news_tag($model);
                        
                        continue;
                        echo $model->title.PHP_EOL;
                    }
                }
                echo PHP_EOL;
                GoodsNews::model()->filter();
            }
        }

    }
    
    
    public function news_tag($item)
    {
        echo ".";
        echo "news_tag".PHP_EOL;
        foreach (self::$tags as $tag) {
            if ($this->hasTag($item->title." ".$item->content, $tag->name)) {
                $model = new NewsTags();
                $model->tag = $tag->id;
                $model->news = $item->id;
                if ($model->validate()) {
                    $model->save();
                    
                    
                    echo $tag->type."_".$tag->link.PHP_EOL;
                }
                unset($model);
            }
        }
        $connection = Yii::app()->db;
        $connection->createCommand("update {{news}} t set t.updated_tags = now() where t.id = {$item->id}")->execute();
        unset($job, $item, $tag, $connection);
        echo PHP_EOL;
        return true;
    }
    
    protected function filter_images($content, $referer, $news, $title, $language) {
        if (mb_strlen($content, 'UTF-8') < 10) {
            return $content;
        }
        $html = phpQuery::newDocumentHTML($content);
        unset($content);
        
        foreach (pq($html)->find("img") as $image) {
            $image = pq($image);
            $alt = $image->attr("alt");
            $alt_replaced = 0;
            if (empty($alt)) {
                $alt_replaced = 1;
                $alt = mb_substr($title, 0, 255,'UTF-8');
            }
            $url = $image->attr("src");
            // Если пустой url, удаляем изображение
            if (empty($url)) {
                $image->remove();
                Yii::app()->db->createCommand("update ai_news set broken_image = 1 where id = {$news}")->execute();
                continue;
            } else {
                // Относительный url
                if (preg_match("/^\/\w+.*/isu", $url)) {
                    $host = preg_replace("/^(http:\/\/[\w\.\-]+)\/.*/isu","$1", $referer);
                    if (empty($host)) {
                        echo "empty host".PHP_EOL;
                        $image->remove();
                        Yii::app()->db->createCommand("update ai_news set broken_image = 1 where id = {$news}")->execute();
                        continue;
                    }
                    $url = $host.$url;
                    echo "Относительный url ".$url.PHP_EOL;
                // Без http
                } elseif (preg_match("/^\/\/\w+.*/isu", $url)) {
                    $url = "http:".$url;
                    echo "Url без http ".$url.PHP_EOL;
                }
                
                if (!$imageModel = NewsImages::model()->findByAttributes(['source_url'=>$url, 'news'=>$news])) {
                
                    $imageModel = new NewsImages();
                    $imageModel->source_url = $url;
                    $tmpfname = tempnam("/tmp", "_analogindex_tmp");
                    if (!$file = fopen($tmpfname, 'w')) {
                        echo "Не могу открыть файл для записи {$tmpfname}".PHP_EOL;
                        Yii::app()->db->createCommand("update ai_news set broken_image = 1 where id = {$news}")->execute();
                        @unlink($tmpfname);
                        continue;
                    }
                    
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
                    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                    curl_setopt($ch, CURLOPT_REFERER, $referer);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
                    curl_setopt($ch, CURLOPT_FILE, $file);
                    
                    curl_exec($ch);
                    fclose($file);
                    
                    if (curl_errno($ch)) {
                        echo "Curl error #".curl_errno($ch)." " . curl_error($ch)." " .$url. PHP_EOL;
                        $image->remove();
                        Yii::app()->db->createCommand("update ai_news set broken_image = 1 where id = {$news}")->execute();
                        @unlink($tmpfname);
                        continue;
                    } 
                    curl_close($ch);
                    $imageModel->save();
                    if($imageModel->setFile($tmpfname)) {
                        $imageModel->name = Yii::app()->urlManager->translitUrl($title).".".$imageModel->getExt();
                        $imageModel->news = $news;
                        $imageModel->alt = htmlspecialchars(strip_tags($alt));
                        $imageModel->alt_replaced = $alt_replaced;
                        $imageModel->save();
                        echo "OK".PHP_EOL;
                        @unlink($tmpfname);
                    } else {
                        echo "Not saved File".PHP_EOL;
                        $image->remove();
                        Yii::app()->db->createCommand("update ai_news set broken_image = 1 where id = {$news}")->execute();
                        @unlink($tmpfname);
                        continue;
                    }
                } elseif($imageModel->alt_replaced != $alt_replaced) {
                    $imageModel->alt_replaced = $alt_replaced;
                    $imageModel->save();
                }
                
                $url = Yii::app()->createAbsoluteUrl("files/newsimage", [
                    'language' => Language::getZoneForLang($language),
                    'id'=>$imageModel->id,
                    'name'=>$imageModel->name,
                ]);
                $alt = htmlspecialchars(strip_tags($alt));
                $image->replaceWith('<img src="'.$url.'" alt="'.$alt.'" />'); 
                echo $news.PHP_EOL;
            }
        }
        return (string) $html;
    }
    
    public function news_filter($news)
    {
        $content = $news->filterContent();

        Yii::app()->db->createCommand("update ai_news set broken_image = 0 where id = {$news->id}")->execute();
        $content = $this->filter_images($content, $news->source_url, $news->id, $news->title, $news->lang);
        
        foreach (self::$goods as $product) {
            $pattern = preg_quote("{$product->brand_data->name} {$product->name}", "~");
            $titlePattern = "~".preg_quote("{$product->brand_data->name}", "~").".* ".preg_quote("{$product->name}", "~")."[^w]+~isu";
            
            if (preg_match($titlePattern, $news->title)) {
                $goodsNews = new GoodsNews();
                $goodsNews->goods = $product->id;
                $goodsNews->news = $news->id;
                if ($goodsNews->validate()) {
                    $goodsNews->save();
                    echo "Привязан товар к новости".PHP_EOL;
                    echo $titlePattern.PHP_EOL;
                }
                unset($goodsNews);
            }
            unset($titlePattern);
            
            
            $value = CHtml::link("{$product->brand_data->name} {$product->name}", "http://".Yii::app()->createUrl("site/goods", array(
                'link' => $product->link,
                'brand' => $product->brand_data->link,
                'type' => $product->type_data->link,
                'language' => Language::getZoneForLang(($news->lang) ? $news->lang : 'ru'),
            )));

            do {
                $replaced = $this->replaceRecursive($content, $pattern, $value);
                if ($replaced !== false) {
                    $content = $replaced;
                }
            } while($replaced !== false);
            unset($replaced, $pattern, $value);
            
            /**
             * Перебираем синонимы товара
             */
            if (is_array($product->synonims)) {
                foreach ($product->synonims as $synonim) {

                    $pattern = preg_quote("{$product->brand_data->name} {$synonim->name}", "~");


                    $value = CHtml::link("{$product->brand_data->name} {$product->name}", "http://".Yii::app()->createUrl("site/goods", array(
                        'link' => $product->link,
                        'brand' => $product->brand_data->link,
                        'type' => $product->type_data->link,
                        'language' => Language::getZoneForLang(($news->lang) ? $news->lang : 'ru'),
                    )));

                    do {
                        $replaced = $this->replaceRecursive($content, $pattern, $value);
                        if ($replaced !== false) {
                            $content = $replaced;
                        }
                    } while($replaced !== false);
                    unset($replaced, $pattern, $value);
                }
                unset($synonim);
            }
        }

        /**
         * Расставляем ссылки на бренды
         */
        foreach (self::$brands as $key=>$brand) {
            if (empty($brand->name)) {
                echo "Empty brand! {$brand->id}".PHP_EOL;
                unset(self::$brands[$key], $key, $brand);
                continue;
            }
            $pattern = preg_quote($brand->name, "~");
            $value = CHtml::link($brand->name, "http://".Yii::app()->createUrl("site/brand", array(
                "language" => Language::getZoneForLang(($news->lang) ? $news->lang : 'ru'),
                "link" => $brand->link,
            )));
            do {
                $replaced = $this->replaceRecursive($content, $pattern, $value);
                if ($replaced !== false) {
                    $content = $replaced;
                }
            } while($replaced !== false);
            unset($pattern, $value);
        }
        unset($brand);
        
        if (!empty($content)) {
            echo "news_filter: {$news->id}".PHP_EOL;
            
            $sql = "update {{news}} set content_filtered = :content where id = :id";
            Yii::app()->db->createCommand($sql)->execute([
                'content'=>(string) $content,
                'id'=>$news->id,
            ]);
            
            echo "LENGTH: ".mb_strlen($content, 'UTF-8').PHP_EOL;

        }
        unset($content);
        return true;
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
}