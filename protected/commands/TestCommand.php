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
        $criteria->condition = 'id = 167747';
        $articles = Articles::model()->findAll($criteria);
        $filter = new ArticlesFilter();

        foreach ($articles as $article) {
            echo date("Y-m-d H:i:s ") . $article->id . PHP_EOL;
            $article = $filter->filter($article);
            $article->save();
        }
        //GoodsArticles::model()->filter();
        echo PHP_EOL;
    }

    public function actionFilterThread()
    {
        //exit(0);
        $criteria = new CDbCriteria();
        $criteria->condition = "has_filtered = 0 and id > " . rand(1, 145330);
        $criteria->limit = 15;
        //$criteria->condition = 'id = 74170';
        $articles = Articles::model()->findAll($criteria);
        $filter = new ArticlesFilter();

        foreach ($articles as $article) {
            echo date("Y-m-d H:i:s ") . $article->id . PHP_EOL;
            $article = $filter->filter($article);
            $article->save();
        }
        //GoodsArticles::model()->filter();
        echo PHP_EOL;
    }

    public function actionSpec()
    {
        
        $criteria = new CDbCriteria();
        $criteria->condition = "t.characteristic = 36";
        $characteristics = GoodsCharacteristics::model()->findAll($criteria);
        foreach ($characteristics as $characteristic) {
            $values = @json_decode($characteristic->value, true);
            if (!is_array($values) || empty($values)) {
                continue;
            }
            $values = array_unique($values);
            $model = new SpecificationsValues();
            $model->goods = $characteristic->goods;
            $model->lang = $characteristic->lang;
            $model->raw = json_encode($values);
            $model->specification = 9;
            if ($model->validate()) {
                $model->save();
            }
        }
  
        $products = Goods::model()->with(['brand_data'=>['joinType'=>'inner join']])->findAll();
        foreach ($products as $product) {
            if (empty($product->name) || empty($product->brand_data->name)) {
                continue;
            }
            
            $model = new SpecificationsValues();
            $model->goods = $product->id;
            $model->lang = 'ru';
            $model->raw = $product->name;
            $model->model_id = $product->id;
            $model->specification = 1;
            if ($model->validate()) {
                $model->save();
            }
            
            $model = new SpecificationsValues();
            $model->goods = $product->id;
            $model->lang = 'en';
            $model->raw = $product->name;
            $model->specification = 1;
            $model->model_id = $product->id;
            if ($model->validate()) {
                $model->save();
            }
            
            $model = new SpecificationsValues();
            $model->goods = $product->id;
            $model->lang = 'ru';
            $model->raw = $product->brand_data->name;
            $model->model_id = $product->brand;
            $model->specification = 2;
            if ($model->validate()) {
                $model->save();
            }
            
            $model = new SpecificationsValues();
            $model->goods = $product->id;
            $model->lang = 'en';
            $model->raw = $product->brand_data->name;
            $model->model_id = $product->brand;
            $model->specification = 2;
            if ($model->validate()) {
                $model->save();
            }
        }
        
  
        
        $criteria = new CDbCriteria();
        $criteria->condition = "t.characteristic = 3";
        $characteristics = GoodsCharacteristics::model()->findAll($criteria);
        foreach ($characteristics as $characteristic) {
            if (!$characteristic->value) {
                continue;
            }
            
            $model = new SpecificationsValues();
            $model->goods = $characteristic->goods;
            $model->lang = $characteristic->lang;
            $model->raw = doubleval($characteristic->value);
            $model->specification = 7;
            if ($model->validate()) {
                $model->save();
            }
        }
        
      
        
        $criteria = new CDbCriteria();
        $criteria->condition = "t.characteristic = 4";
        $characteristics = GoodsCharacteristics::model()->findAll($criteria);
        foreach ($characteristics as $characteristic) {

            $values = @json_decode($characteristic->value, true);

            if (!is_array($values) || count($values) !== 3) {
                continue;
            }

            $model = new SpecificationsValues();
            $model->goods = $characteristic->goods;
            $model->lang = $characteristic->lang;
            $model->raw = doubleval($values[1]);
            $model->specification = 4;
            if ($model->validate()) {
                $model->save();
            }

            $model = new SpecificationsValues();
            $model->goods = $characteristic->goods;
            $model->lang = $characteristic->lang;
            $model->raw = doubleval($values[1]);
            $model->specification = 5;
            if ($model->validate()) {
                $model->save();
            }

            $model = new SpecificationsValues();
            $model->goods = $characteristic->goods;
            $model->lang = $characteristic->lang;
            $model->raw = doubleval($values[2]);
            $model->specification = 6;
            if ($model->validate()) {
                $model->save();
            }
        }
    }

}
