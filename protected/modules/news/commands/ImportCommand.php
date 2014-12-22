<?php

class ImportCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        
        $urlManager = new UrlManager();
        $brands = Brands::model()->findAll();

        foreach ($brands as $brand) {
            $news = $this->search($brand->name);
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
                    }
                } else {
                    $model = News::model()->findByAttributes(['source_url'=>$url]);
                    if (!empty($model)) {
                        $id = $model->id;
                    }
                }
                
                
                if (!empty($id)) {
                    $model = new BrandsNews();
                    $model->brand = $brand->id;
                    $model->news = $id;
                    if ($model->validate()) {
                        echo ".";
                        $model->save();
                    }
                }
            }
        }
        
        unset($brand, $brands);
        echo PHP_EOL;
        $products = Goods::model()->with(["brand_data"])->findAll();
        
        foreach ($products as $product) {
            $name = $product->brand_data->name." ".$product->name;
            $news = $this->search($name);
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
                
                if (!empty($id)) {
                    $model = new GoodsNews();
                    $model->goods = $product->id;
                    $model->news = $id;
                    if ($model->validate()) {
                        echo ".";
                        $model->save();
                    }
                }
            }
        }
        
        GoodsNews::model()->filter();
    }
    
    
    public function search($query)
    {
        $searchCriteria = new stdClass();
        $search = Yii::app()->search;



        $pages = new CPagination(10000000000000);
        $pages->pageSize = 10000000000000;
        

        $searchCriteria->paginator = $pages;
       
        $searchCriteria->from = 'topics_index';
        try {
            $query = $search->escape($query);
            $searchCriteria->query = "{$query}";
            $pages->applyLimit($searchCriteria);
            $search->setMatchMode(SPH_MATCH_EXTENDED2);
            $resIterator = $search->search($searchCriteria); 
        } catch (Exception $ex) {
            
        }

        if (!empty($resIterator) && $resIterator->getTotal()) {
            return $this->itemsByIds($resIterator->getIdList());
        }
        
        return [];
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
                        
                        $task= $gmc->addTaskHighBackground("news_tag", serialize($model));
                        $task= $gmc->addTaskHighBackground("news_filter", serialize($model));
                        
                        $newsTags = new NewsTags();
                        $newsTags->news = $news->id;
                        $newsTags->tag = $tag->id;
                        if ($newsTags->validate()) {
                            $newsTags->save();
                        }
                        continue;
                        echo $model->title.PHP_EOL;
                    }
                }
                echo PHP_EOL;
                GoodsNews::model()->filter();
            }
        }
        
        if (! $gmc->runTasks())
        {
            echo "ERROR " . $gmc->error() . "\n";
            exit;
        }
    }
    
}