<?php

class ArticlesController extends Controller
{

    public function actionIndex($type, $link, $id, $debug = false)
    {

        if (!$debug) {
            $article = Articles::model()->cache(60 * 60)->findByAttributes(['id' => $id, 'link' => $link, 'lang' => Yii::app()->language]);
        } else {
            $article = Articles::model()->cache(60 * 60)->findByAttributes(['id' => $id]);
        }

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
                if (!empty($tag->tag_data->name) && $tag->tag_data->disabled == 0) {
                    $tag_ids[] = $tag->tag;
                    $this->addKeyword($tag->tag_data->name);
                }
            }

            $tag_ids = array_unique($tag_ids);

            if (!empty($tag_ids)) {
                $criteria = new CDbCriteria();
                $criteria->select = "goods";
                $criteria->addInCondition("tag", $tag_ids);
                $product_ids = GoodsTags::model()->cache(60 * 60)->findAll($criteria);
                foreach ($product_ids as $pid) {
                    $widget_in[] = $pid->goods;
                }
            }

            $export = new Export();
            $this->addDescription($article->description);
            $this->setPageTitle($article->title);

            $tagsArray = [];
            foreach ($article->tags as $tag) {
                if (empty($tag->tag_data)) {
                    continue;
                }
                $tagsArray[$tag->tag_data->name] = $tag->tag_data->name;
                $this->addKeyword($tag->tag_data->name);
            }
            $tags = implode(",", $tagsArray);

            if ($debug) {
                $this->layout = 'materialize';
            }

            $widgets = [
                'related_products' => [],
                'related_trends' => [],
                'related_videos' => [],
                'related_compare' => [],
            ];

            if (!empty($tagsArray)) {
                $criteria = new CDbCriteria();
                $criteria->addInCondition('name', $tagsArray);
                $criteria->compare("disabled", 0);
                $criteria->select = "id, name";
                $criteria->limit = 20;
                $tagsModel = Tags::model()->cache(60 * 60)->with(['goods' => ['joinType' => 'inner join']])->findAll($criteria);
                $in = [];

                foreach ($tagsModel as $tag) {
                    if (empty($tag->goods)) {
                        continue;
                    }
                    $in[] = $tag->goods->goods;
                }
                $in = array_unique($in);

                if (!empty($in)) {
                    $criteria = new CDbCriteria();
                    $criteria->condition = 't.id in (' . implode(", ", $in) . ')';
                    $criteria->group = 't.id';
                    $criteria->order = "t.updated desc";
                    $widgets['related_products'] = Goods::model()->cache(60 * 60)->with(["brand_data", 'type_data'])->findAll($criteria);
                    if (!empty($widgets['related_products'])) {
                        foreach ($widgets['related_products'] as $product) {
                            $widgets['related_trends'][] = htmlentities($product->brand_data->name . " " . $product->name);
                        }
                        $widgets['related_trends'] = array_chunk($widgets['related_trends'], 5);
                    }



                    $criteria = new CDbCriteria();
                    $criteria->condition = 'lang = :lang and t.goods in (' . implode(", ", $in) . ')';
                    $criteria->params = ['lang' => Yii::app()->language];
                    $criteria->limit = count($in);
                    $criteria->order = "t.priority";
                    $criteria->group = 't.goods, t.lang';
                    $widgets['related_videos'] = Videos::model()->cache(60 * 60)->findAll($criteria);


                    if (!empty($widgets['related_products'])) {
                        $chList = Goods::model()->cache(60 * 60)->with('brand_data')->getCharacteristicsList();
                        $chCompare = [];
                        $widgets['related_compare']['index'] = [];
                        $index = 0;
                        foreach ($widgets['related_products'] as &$product) {
                            $chCompare[$index][$product->id] = $product->getCharacteristicsCompare();

                            if (!empty($chCompare[$index][$product->id])) {
                                $characteristicsLinks = new CharacteristicsLinks($chCompare[$index][$product->id]);
                                $chCompare[$index][$product->id] = $characteristicsLinks->getCharacteristics($product->type_data->link);

                                $widgets['related_compare']['index'][$index][$product->id] = [
                                    'name' => "{$product->brand_data->name} {$product->name}",
                                    'model' => $product,
                                ];

                                if (count($chCompare[$index]) > 4) {
                                    $index++;
                                }
                            } else {
                                unset($chCompare[$index][$product->id], $widgets['related_compare']['index'][$index][$product->id], $product);
                            }
                        }

                        foreach ($chList as $catalog => $list) {
                            $row = [];
                            foreach ($list as $id => $ch) {
                                $exist = false;
                                foreach ($widgets['related_compare']['index'] as $index => $goodsNamesGroup) {
                                    $row = [];

                                    foreach ($goodsNamesGroup as $productId => $name) {
                                        $row[$productId] = !empty($chCompare[$index][$productId][$id]) ? $chCompare[$index][$productId][$id] : null;
                                        $key = $chList[$catalog][$id]['characteristic_name'];
                                        if (empty($key)) {
                                            $key = $chList[$catalog][$id]['catalog_name'];
                                        }

                                        $widgets['related_compare']['data'][$index][$key] = $row;
                                    }
                                    foreach ($widgets['related_compare']['data'][$index] as $characteristic => $values) {
                                        $empty = true;
                                        foreach ($values as $value) {
                                            if (is_array($value)) {
                                                $empty = false;
                                            }
                                        }
                                        if ($empty) {
                                            unset($widgets['related_compare']['data'][$index][$characteristic]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }


            $this->render((($debug) ? 'news_materialize' : $article->type), [
                'article' => $article,
                'widget_in' => $widget_in,
                'export' => $export,
                'type' => $article->type,
                'tags' => $tags,
                'widgets' => $widgets,
            ]);
        }
    }

    public function actionAll()
    {
        $criteria = new CDbCriteria();
        $criteria->order = "t.id desc";
        $criteria->limit = 50;

        $news = Articles::model()->findAll($criteria);

        $this->render("test_list", ["news" => $news]);
    }

    public function actionList($type, $brand, $product, $page = null)
    {
        $brand = Brands::model()->findByAttributes(array("link" => $brand));

        if (!$brand) {
            echo "no brand";
            //Yii::app()->request->redirect("/", true, 302);
            exit();
        }

        $criteria = new CDbCriteria();
        $criteria->condition = "t.link = :link and t.brand = :brand";
        $criteria->params = array("link" => $product, "brand" => $brand->id);
        $product = Goods::model()->cache(60 * 60)->find($criteria);

        if (!$product) {
            echo "no product";
            //Yii::app()->request->redirect("/", true, 302);
            exit();
        }

        $criteria = new CDbCriteria();
        $criteria->order = "t.created desc";
        $criteria->condition = "t.lang = :lang and t.type = :type";
        $criteria->params = ['lang' => Yii::app()->language, 'type' => $type];

        $newsCount = Articles::model()->cache(60 * 60)->with([
                    'product' => ['on' => 'product.goods = :goods', 'params' => ['goods' => $product->id]],
                ])->count($criteria);

        $pages = new CPagination($newsCount);
        $pages->setPageSize(15);
        $pages->applyLimit($criteria);

        $news = Articles::model()->cache(60 * 60)->with([
                    'product' => ['on' => 'product.goods = :goods', 'params' => ['goods' => $product->id]],
                        //'preview_image'
                ])->findAll($criteria);


        $this->render("list", ["news" => $news, "product" => $product, 'pages' => $pages, 'type_selected' => $type, 'brand' => $brand]);
    }

}
