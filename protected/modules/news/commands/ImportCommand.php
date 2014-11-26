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
            $resIterator = $search->search($searchCriteria); // interator result
        } catch (Exception $ex) {
            
        }
        $items = [];
        if (!empty($resIterator) && $resIterator->getTotal()) {
            $pages->setItemCount($resIterator->getTotalFound());
            $criteria = new CDbCriteria();
            $criteria->select = "t.topic_id, t.topic_title, t.topic_date_add, t.user_id";
            $criteria->addInCondition("t.topic_id", $resIterator->getIdList());
            $criteria->order = "t.topic_date_add desc";
            $criteria->limit = 100;
            $items = Topics::model()->with("topic_content")->findAll($criteria);
        }
        $result = [];
        foreach ($items as &$item)
        {
            if (empty($item->topic_content->source_url) || empty($item->topic_content->topic_text)) {
                continue;
            }
            
            $result[$item->topic_content->source_url] = [
                'id' => $item->topic_id,
                'title' => $item->topic_title,
                'url' => $item->topic_content->source_url,
                'date' => $item->topic_date_add,
                'lang' => $item->lang,
                'content' => $item->topic_content->topic_text,
            ];
        }
        return $result;
    }
}