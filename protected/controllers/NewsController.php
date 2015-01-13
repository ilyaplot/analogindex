<?php

class NewsController extends Controller
{
    public function actionIndex($link, $id, $debug = false)
    {
        $news = News::model()->with(['tags'])->findByAttributes(['link'=>$link, 'id'=>$id, 'lang'=>Yii::app()->language]);
        
        $widget_in = [];
        $tag_ids = [];
        
        
        if (!$news) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        } else {
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
            
            if (!$debug)
                $news->content = $news->filteredContent();

            $export = new Export();
            $this->addDescription($news->getDescription());
            $this->setPageTitle($news->title);
            $this->render("view", ['news'=>$news, 'widget_in'=>$widget_in, 'export'=>$export]);
        }
    }
    
    public function actionGoodsList($brand, $product, $page=null)
    {
        $brand = Brands::model()->findByAttributes(array("link" => $brand));
        
        if (!$brand) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        }
        
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
        
        $newsCount = News::model()->cache(60*60)->with([
            'product'=>['on'=>'product.goods = :goods', 'params'=>['goods'=>$product->id]],
        ])->count($criteria);
        
        $pages = new CPagination($newsCount);
        $pages->setPageSize(15);
        $pages->applyLimit($criteria);
        
        $news = News::model()->cache(60*60)->with([
            'product'=>['on'=>'product.goods = :goods', 'params'=>['goods'=>$product->id]],
            //'preview_image'
        ])->findAll($criteria);
        
        
        $this->render("list", ["news"=>$news, "product"=>$product, 'pages'=>$pages]);
    }
    
    public function actionBrandList($brand)
    {
        
    }

    public function actionAll()
    {
        $criteria = new CDbCriteria();
        $criteria->order = "t.id desc";
        $criteria->limit = 500;
        
        $news = News::model()->findAll($criteria);
        
        $this->render("test_list", ["news"=>$news]);
    }
}