<?php
class ExportController extends CController
{
    public function actionNews($tags, $lang, $limit)
    {
        $tags = explode(",", $tags);
        $tags = array_map(function($value){return trim($value);}, $tags);
        if (!empty($tags)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('name', $tags);
            $criteria->select = "id";
            $tags = Tags::model()->findAll($criteria);
            $in = [];
            foreach($tags as $tag) {
                $in[] = $tag->id;
            }
            if (!empty($in)) {
                $criteria = new CDbCriteria();
                $criteria->addInCondition("t.tag", $in);
                $criteria->compare("news_data.lang", $lang);
                $criteria->order = "field(tag_data.type, 'product', 'brand', 'os', 'word'), news_data.created desc";
                $criteria->limit = $limit;
                $criteria->group = "news_data.id";
                $newsTags = NewsTags::model()->with(['news_data', 'tag_data'])->findAll($criteria);
                echo "<ul>".PHP_EOL;
                foreach ($newsTags as $link) {
                    $news = $link->news_data;
                    $link = Yii::app()->createAbsoluteUrl("news/index", ['link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getZoneForLang($news->lang)]);
                    echo "<li>".CHtml::link($news->title, $link)."</li>".PHP_EOL;
                }
                echo "</ul>";
            }
        }
    }
    
    public function actionVideos($tags, $lang)
    {
        Yii::app()->language = $lang;
        $tags = explode(",", $tags);
        $tags = array_map(function($value){return trim($value);}, $tags);
        if (!empty($tags)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('name', $tags);
            $criteria->select = "id, name";
            $tags = Tags::model()->with(['goods'=>['joinType'=>'inner join']])->findAll($criteria);
            $in = [];
            foreach($tags as $tag) {
                if (empty($tag->goods)) {
                    continue;
                }
                $in[] = $tag->goods->goods;
            }

            if (!empty($in)) {
                $criteria = new CDbCriteria();
                $criteria->condition = 'lang = :lang and t.goods in ('.implode(", ", $in).')';
                $criteria->params = ['lang'=>$lang];
                $criteria->limit = count($in);
                $criteria->order = "t.priority";
                $criteria->group = 't.goods, t.lang';
                $videos = Videos::model()->findAll($criteria);
                echo "<ul>".PHP_EOL;
                foreach ($videos as $video) {
                   
                    echo "<li>";
                    echo "<h2>".Yii::t("models", "Видео обзор");
                    echo " {$video->goods_data->brand_data->name} {$video->goods_data->name}</h2>";
                    echo $video->getTemplate();
                    echo "</li>";
                }
                echo "</ul>";
            }
        }
    }
    
    public function actionAll()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'created desc';
        $criteria->limit = 1000;
        $news = News::model()->findAll($criteria);
        echo "<ul>";
        foreach ($news as $item) {
            $link = Yii::app()->createAbsoluteUrl("news/index", ['link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getZoneForLang($item->lang)]);
            echo "<li>{$item->id} ".CHtml::link($item->title, $link)." {$item->created}</li>".PHP_EOL;
        }
        echo "</ul>";
    }
    
    public function actionCompare($tags, $lang, $limit=20)
    {
        Yii::app()->language = $lang;
        $tags = explode(",", $tags);
        $tags = array_map(function($value){return trim($value);}, $tags);
        if (!empty($tags)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('name', $tags);
            $criteria->select = "id, name";
            $criteria->limit = $limit;
            $tags = Tags::model()->with(['goods'=>['joinType'=>'inner join']])->findAll($criteria);
            $in = [];
            foreach($tags as $tag) {
                if (empty($tag->goods)) {
                    continue;
                }
                $in[] = $tag->goods->goods;
            }

            if (!empty($in)) {
                $criteria = new CDbCriteria();
                $criteria->condition = 't.id in ('.implode(", ", $in).')';
                $criteria->group = 't.id';
                $goods = Goods::model()->findAll($criteria);
            }
        }
        
        $data = [];
        if (!empty($goods)) {
            $chList = Goods::model()->with('brand_data')->getCharacteristicsList();
            $chCompare = [];
            $goodsNames = [];
            foreach ($goods as $product) {
                $chCompare[$product->id] = $product->getCharacteristicsCompare();
                $goodsNames[$product->id] = "{$product->brand_data->name} {$product->name}";
            }
            
            foreach ($chList as $catalog=>$list) {
                foreach ($list as $id=>$ch) {
                    $row = [];
                    $exist = false;
                    foreach ($goodsNames as $productId=>$name) {
                        $row[$productId] = !empty($chCompare[$productId][$id]) ? $chCompare[$productId][$id] : null;
                        if ($row[$productId] !== null) {
                            $exist = true;
                        }
                    }
                    if ($exist) {
                        $key = $chList[$catalog][$id]['characteristic_name'];
                        if (empty($key))
                            $key = $chList[$catalog][$id]['catalog_name'];
                        
                        $data[$key] = $row;
                    }
                }
            }
            $this->layout = 'empty';
            $this->render('compare', ['goods'=>$goodsNames, 'data'=>$data]);
        }

    }
}

