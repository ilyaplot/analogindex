<?php

class ArticlesController extends Controller
{
    public function actionIndex($type, $link, $id, $debug = false)
    {
        $article = Articles::model()->with(['tags'])->findByAttributes(['link'=>$link, 'id'=>$id, 'lang'=>Yii::app()->language]);
        
        
        
        $widget_in = [];
        $tag_ids = [];
        
        
        if (!$article) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        } else {
            if ($article->type != $type) {
                Yii::app()->request->redirect("/{$article->type}/{$article->link}_{$article->id}.html", true, 302);
            }
            
            foreach ($article->tags as $tag) {
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

            $export = new Export();
            $this->addDescription($article->description);
            $this->setPageTitle($article->title);
            $this->render($article->type, ['article'=>$article, 'widget_in'=>$widget_in, 'export'=>$export, 'type'=>$article->type]);
        }
    }

    public function actionAll()
    {
        $criteria = new CDbCriteria();
        $criteria->order = "t.id desc";
        $criteria->limit = 50;
        
        $news = Articles::model()->findAll($criteria);
        
        $this->render("test_list", ["news"=>$news]);
    }
}