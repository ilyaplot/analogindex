<?php

class NewsController extends Controller
{
    public function actionIndex($link, $id, $debug = false)
    {
        Yii::app()->request->redirect("/news/{$link}_{$id}.html", true, 302);
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
        $criteria->condition = "t.lang = :lang and t.type = 'news'";
        $criteria->params = ['lang'=>Yii::app()->language];
        
        $newsCount = Articles::model()->cache(60*60)->with([
            'product'=>['on'=>'product.goods = :goods', 'params'=>['goods'=>$product->id]],
        ])->count($criteria);
        
        $pages = new CPagination($newsCount);
        $pages->setPageSize(15);
        $pages->applyLimit($criteria);
        
        $news = Articles::model()->cache(60*60)->with([
            'product'=>['on'=>'product.goods = :goods', 'params'=>['goods'=>$product->id]],
            //'preview_image'
        ])->findAll($criteria);
        
        
        $this->render("list", ["news"=>$news, "product"=>$product, 'pages'=>$pages]);
    }
    
    public function actionBrandList($brand)
    {
        
    }

}