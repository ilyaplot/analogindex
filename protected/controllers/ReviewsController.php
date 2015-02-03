<?php
class ReviewsController extends Controller
{
    public function actionIndex($goods, $link, $id)
    {
        $query = "select concat(b.link, '_', g.link) from {{goods}} g
            inner join {{brands}} b on b.id = g.brand
            where concat(b.link,'-',g.link) like :link";
        $link = Goods::model()->getDbConnection()->createCommand($query)->queryScalar(['link'=>$goods]);
        if ($link) {
            Yii::app()->request->redirect("/review/product/{$link}.html", true, 301);
            exit();
        }
        Yii::app()->request->redirect("/", true, 301);
        exit();
    }
}