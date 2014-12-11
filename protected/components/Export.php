<?php
class Export 
{
    public function News($tags, $lang, $limit)
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
                $newsTags = NewsTags::model()->cache(60)->with(['news_data', 'tag_data'])->findAll($criteria);
                
                ob_start();
                extract(['newsTags'=>$newsTags]);
                require Yii::app()->basePath."/views/export/news.php";
                echo ob_get_clean();
            }
        }
    }
    
    public function Videos($tags, $lang)
    {
        Yii::app()->language = $lang;
        $tags = explode(",", $tags);
        $tags = array_map(function($value){return trim($value);}, $tags);
        if (!empty($tags)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('name', $tags);
            $criteria->select = "id, name";
            $tags = Tags::model()->cache(60)->with(['goods'=>['joinType'=>'inner join']])->findAll($criteria);
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

    
    public function Compare($tags, $lang, $limit=20)
    {
        Yii::app()->language = $lang;
        $tags = explode(",", $tags);
        $tags = array_map(function($value){return trim($value);}, $tags);
        if (!empty($tags)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('name', $tags);
            $criteria->compare("disabled", 0);
            $criteria->select = "id, name";
            $criteria->limit = $limit;
            $tags = Tags::model()->cache(60)->with(['goods'=>['joinType'=>'inner join']])->findAll($criteria);
            $in = [];
            foreach($tags as $tag) {
                if (empty($tag->goods)) {
                    continue;
                }
                $in[] = $tag->goods->goods;
            }
            $in = array_unique($in);
            
            if (!empty($in)) {
                $criteria = new CDbCriteria();
                $criteria->condition = 't.id in ('.implode(", ", $in).')';
                $criteria->group = 't.id';
                $criteria->order = 't.updated desc';
                $goods = Goods::model()->findAll($criteria);
            }
        }
        
        $data = [];
        if (!empty($goods)) {
            $chList = Goods::model()->cache(60)->with('brand_data')->getCharacteristicsList();
            $chCompare = [];
            $goodsNames = [];
            $index = 0;
            foreach ($goods as &$product) {
                $chCompare[$index][$product->id] = $product->getCharacteristicsCompare();
                
                if (!empty($chCompare[$index][$product->id])) {
                    $characteristicsLinks = new CharacteristicsLinks($chCompare[$index][$product->id]);
                    $chCompare[$index][$product->id] = $characteristicsLinks->getCharacteristics($product->type_data->link);
                
                    $goodsNames[$index][$product->id] = [
                        'name'=>"{$product->brand_data->name} {$product->name}",
                        'model'=>$product,
                    ];

                    if (count($chCompare[$index]) > 4) {
                        $index++;
                    }
                } else {
                    unset ($chCompare[$index][$product->id], $goodsNames[$index][$product->id], $product);
                }

            }
            $data =[];
            foreach ($chList as $catalog=>$list) {
                $row = [];
                foreach ($list as $id=>$ch) {
                    $exist = false;
                    foreach ($goodsNames as $index=>$goodsNamesGroup) {
                        $row = [];
                        foreach ($goodsNamesGroup as $productId=>$name) {
                            $row[$productId] = !empty($chCompare[$index][$productId][$id]) ? $chCompare[$index][$productId][$id] : null;
                            
                            if ($row[$productId] !== null) {
                                $key = $chList[$catalog][$id]['characteristic_name'];
                                if (empty($key)) {
                                    $key = $chList[$catalog][$id]['catalog_name'];
                                }
                                $data[$index][$key] = $row;
                            } else {
                                if (empty($data[$index][$key][$productId])) {
                                    $data[$index][$key][$productId] = null;
                                }
                            }
                        }
                    }
                }
            }
            ob_start();
            extract(['goodsIndex'=>$goodsNames, 'data'=>$data]);
            require Yii::app()->basePath."/views/export/compare.php";
            echo ob_get_clean();
        }
    }
    
    
    public function Products($tags, $lang, $limit=20)
    {
        Yii::app()->language = $lang;
        $tags = explode(",", $tags);
        $tags = array_map(function($value){return trim($value);}, $tags);
        if (!empty($tags)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('name', $tags);
            $criteria->compare("disabled", 0);
            $criteria->select = "id, name";
            $criteria->limit = $limit;
            $tags = Tags::model()->cache(60)->with(['goods'=>['joinType'=>'inner join']])->findAll($criteria);
            $in = [];
            foreach($tags as $tag) {
                if (empty($tag->goods)) {
                    continue;
                }
                $in[] = $tag->goods->goods;
            }
            $in = array_unique($in);
            
            if (!empty($in)) {
                $criteria = new CDbCriteria();
                $criteria->condition = 't.id in ('.implode(", ", $in).')';
                $criteria->group = 't.id';
                $criteria->order = "t.updated desc";
                $goods = Goods::model()->cache(60)->with(["brand_data", 'type_data'])->findAll($criteria);
            }
        }
        
        if (!empty($goods)) {
            ob_start();
            extract(['goods'=>$goods]);
            require Yii::app()->basePath."/views/export/products.php";
            echo ob_get_clean();
        }
    }
    
    public function Trends($tags, $lang, $limit=20)
    {
        Yii::app()->language = $lang;
        $tags = explode(",", $tags);
        $tags = array_map(function($value){return trim($value);}, $tags);
        if (!empty($tags)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('name', $tags);
            $criteria->compare("disabled", 0);
            $criteria->select = "id, name";
            $criteria->limit = $limit;
            $tags = Tags::model()->cache(60)->with(['goods'=>['joinType'=>'inner join']])->findAll($criteria);
            $in = [];
            foreach($tags as $tag) {
                if (empty($tag->goods)) {
                    continue;
                }
                $in[] = $tag->goods->goods;
            }
            $in = array_unique($in);
            
            if (!empty($in)) {
                $criteria = new CDbCriteria();
                $criteria->condition = 't.id in ('.implode(", ", $in).')';
                $criteria->group = 't.id';
                
                $criteria->order = "t.updated desc";
                $goods = Goods::model()->cache(60)->with(["brand_data"])->findAll($criteria);
                $goodsList = [];
                foreach ($goods as $product) {
                    $goodsList[] = urlencode($product->brand_data->name." ".$product->name);
                }
                $goodsList = array_chunk($goodsList, 5);
                foreach ($goodsList as $items) {
                    echo PHP_EOL;
                    ?>
                    <br /><iframe width="500" height="290" scrolling="no" src="http://www.google.com/trends/fetchComponent?hl=<?php echo $lang?>&q=<?php echo implode(",", $items)?>&cmpt=q&content=1&cid=TIMESERIES_GRAPH_0&export=5&w=500&h=330&date=today+12-m"></iframe>
                    <?php
                }
            }
        }
    }
    
    public function Reviews($tags, $lang, $limit = 10)
    {
        Yii::app()->language = $lang;
        $tags = explode(",", $tags);
        $tags = array_map(function($value){return trim($value);}, $tags);
        if (!empty($tags)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('name', $tags);
            $criteria->compare('disabled', 0);
            $criteria->select = "id, name";
            $tags = Tags::model()->cache(60)->with(['goods'=>['joinType'=>'inner join']])->findAll($criteria);
            $in = [];
            foreach($tags as $tag) {
                if (empty($tag->goods)) {
                    continue;
                }
                $in[] = $tag->goods->goods;
            }

            if (!empty($in)) {
                $criteria = new CDbCriteria();
                $criteria->condition = "t.id in (".implode(", ", $in).")";
                $criteria->order = "field (t.id, ".implode(", ", $in).")";
                $products = Goods::model()->cache(60)->with(['brand_data', 'type_data'])->findAll($criteria);
                
                $productsList = [];
              
                foreach ($products as &$product) {
                    $criteria = new CDbCriteria();
                    $criteria->condition = 't.lang = :lang and t.goods = :id';
                    $criteria->params = ['lang'=>$lang, 'id'=>$product->id];
                    $criteria->limit = ceil($limit / count($products));
                    $criteria->order = "t.created desc";
                    $list = Reviews::model()->cache(60)->findAll($criteria);
                    if (empty($list)) {
                        continue;
                    }
                    $product->revs = $list;
                    $productsList[$product->id] = $product;
                    
                }
                ob_start();
                extract(['products'=>$productsList]);
                require Yii::app()->basePath."/views/export/reviews.php";
                echo ob_get_clean();
            }
        }
    }
}