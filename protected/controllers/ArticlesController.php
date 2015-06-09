<?php

class ArticlesController extends Controller
{

    public function actionIndex($type, $link, $id, $debug = false)
    {

        $article = Articles::model()->cache(60 * 60)->findByAttributes(['id' => $id, 'link' => $link, 'lang' => Yii::app()->language]);

        

        $relatedProducts = [];

        $widgets = [
            'related_products' => [],
            'related_trends' => [],
            'related_videos' => [],
            'related_compare' => [],
        ];

        if (!$article) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        } else {
            /**
             * Редирект на случай изменения типа материала
             */
            if ($article->type != $type) {
                $redirectUrl = Yii::app()->createAbsoluteUrl("articles/index", ['type' => $article->type, 'link' => $article->link, 'id' => $article->id, 'language' => Language::getCurrentZone()]);
                Yii::app()->request->redirect($redirectUrl, true, 302);
            }

            foreach ($article->tags as $tag) {
                if (!empty($tag->tag_data->name) && $tag->tag_data->disabled == 0) {

                    $this->addKeyword($tag->tag_data->name);

                    if ($tag->tag_data->type == 'product' && !empty($tag->tag_data->goods->goods)) {
                        $relatedProducts[] = $tag->tag_data->goods->goods;
                    }
                }
            }
            

            if (!empty($relatedProducts)) {
                // Упомянутые товары
                $criteria = new CDbCriteria();
                $criteria->addInCondition('t.id', $relatedProducts);
                $criteria->group = 't.id';
                $criteria->order = "t.updated asc";
                $widgets['related_products'] = Goods::model()->cache(60 * 60 * 24)->with(["brand_data", 'type_data', 'primary_video'])->findAll($criteria);
                
                // Google trends
                $relatedTrends = [];
                $relatedVideos = [];
                foreach ($widgets['related_products'] as $product) {
                    if (!empty($product->primary_video->link) && !in_array($product->primary_video->link, $relatedVideos)) {
                        $relatedVideos[] = $product->primary_video->link;
                        $widgets['related_videos'][] = $product->primary_video;
                    }
                    $relatedTrends[] = urlencode($product->fullname);
                }
                $relatedTrends = array_chunk($relatedTrends, 5);
                $widgets['related_trends'] = array_map(function($value){ 
                    $language = Yii::app()->language;
                    return "http://www.google.com/trends/fetchComponent?hl={$language}&q=".implode(",", $value)."&cmpt=q&content=1&cid=TIMESERIES_GRAPH_0&export=5&w=500&h=330&date=today+12-m";
                }, $relatedTrends);
                unset ($relatedTrends);
            }
            
            $this->addDescription($article->description);
            $this->setPageTitle($article->title);

            Yii::app()->sourceLanguage = (Yii::app()->language == 'en') ? 'ru' : 'en';
            $this->breadcrumbs = [
                [
                    'url' => 'http://analogindex.' . Language::getCurrentZone() . '/',
                    'title' => Yii::t('main', 'Главная'),
                ],
                [
                    'title' => Yii::t('articles', $article->type . '-many'),
                ],
            ];
            Yii::app()->sourceLanguage = (Yii::app()->language == 'en') ? 'ru' : 'en';

            
            $article->fillRelated();

            $this->layout = 'materialize';
            
            $this->render('article', [
                'article' => $article,
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
