<?php

class NewsController extends Controller
{
    public function actionIndex($link, $id)
    {
        $news = News::model()->with(['tags'])->findByAttributes(['link'=>$link, 'id'=>$id, 'lang'=>Yii::app()->language]);
        
        $widget_in = [];
        $tag_ids = [];
        foreach ($news->tags as $tag) {
            if (!empty($tag->tag_data) && $tag->tag_data->disabled == 0)
                $tag_ids[] = $tag->tag;
        }
        $tag_ids = array_unique($tag_ids);
        
        if (!empty($tag_ids)) {
            $criteria = new CDbCriteria();
            $criteria->select = "goods";
            $criteria->addInCondition("tag", $tag_ids);
            $product_ids = GoodsTags::model()->findAll($criteria);
            foreach($product_ids as $pid) {
                $widget_in[] = $pid->goods;
            }
        }
        
        if (!$news) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        } else {
            $news->content = $news->filteredContent();
            $this->render("view", ['news'=>$news, 'widget_in'=>$widget_in]);
        }
    }
    
    public function actionGoodsList($brand, $product)
    {
        $brand = Brands::model()->findByAttributes(array("link" => $brand));
        
        if (!$brand)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        
        $criteria = new CDbCriteria();
        $criteria->condition = "t.link = :link and t.brand = :brand";
        $criteria->params = array("link" => $product, "brand" => $brand->id);
        $product = Goods::model()->find($criteria);

        if (!$product) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        }
        
        $criteria = new CDbCriteria();
        $criteria->order = "t.created desc";
        $criteria->condition = "t.lang = :lang";
        $criteria->params = ['lang'=>Yii::app()->language];
        $news = News::model()->with([
            'product'=>['on'=>'product.goods = :goods', 'params'=>['goods'=>$product->id]],
        ])->findAll($criteria);

        foreach ($news as $item) {
            echo CHtml::link($item->title, Yii::app()->createUrl("news/index", ['link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]))." ".$item->created."<br>".PHP_EOL;
        }
    }
    
    public function actionBrandList($brand)
    {
        
    }

}