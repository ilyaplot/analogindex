<?php
class SiteController extends Controller
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }
    
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('index', 'goods', 'language'),
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array("brand"),
                'users'=>array('?'),
            )
        );
    }
    
    
    public function actionIndex()
    {
        $this->setPageTitle("Analogindex");
        $this->render("index");
    }
    
    public function actionGoods($language, $type, $brand, $link)
    {
        $type = GoodsTypes::model()->findByAttributes(array("link"=>$type));
        if (!$type)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));       
        
        $brand = Brands::model()->findByAttributes(array("link"=>$brand));
        if (!$brand)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        $criteria = new CDbCriteria();
        $criteria->condition = "t.link = :link and t.brand = :brand and t.type = :type";
        $criteria->params = array("link"=>$link, "brand"=>$brand->id, "type"=>$type->id);
        $product = Goods::model()->with(array(
            "rating",
            //"images",
            "synonims"=>array(
                "on"=>"synonims.visibled = 1"
            ),
            "primary_image",
            "reviews"=>array(
                "select"=>"reviews.link, reviews.id, reviews.preview, reviews.title, reviews.created",
                "group"=>"reviews.id",
            )
        ))->find($criteria);
        
        if (!$product)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));


        $this->setPageTitle($brand->name." ".$product->name);
        $this->render("goods", array(
            "type"=>$type,
            "brand"=>$brand,
            "product"=>$product,
        ));
    }
    
    public function actionLanguage($language)
    {
        $zones = Language::$zones;
        $url = Yii::app()->request->urlReferrer;
        $url = preg_replace("~http://(.*)analogindex.(\w+)/(.*)~", "http://analogindex.{$language}/$3", $url);
        Yii::app()->request->redirect($url);
    }

    public function actionDownload()
    {
        $id = intval(isset($_GET['id'])? $_GET['id'] : 0 );
        $filename = isset($_GET['filename']) ? trim($_GET['filename']) : "file";
        $filesModel = new FilesModel();
        $size = isset($_GET['size']) ? intval($_GET['size']) : null;
        $filesModel->send($id,$filename,$size);
    }

    public function actionBrand($link, $language, $type=null)
    {
        $brand = Brands::model()->cache(60*60*24)->findByAttributes(array("link"=>$link));
        if (!$brand)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        if ($type !== null)
        {
            $type = GoodsTypes::model()->cache(60*60*24)->findByAttributes(array("link"=>$type));
            if (!$type)
                throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        }
        $view = Yii::app()->request->getParam("view");
        $views = array(
            1=>array(
                "limit"=>18,
                "template"=>"_brand_catalog_1",
                "id"=>1,
            ),
            2=>array(
                "id"=>2,
                "limit"=>5,
                "template"=>"_brand_catalog_2"
            ),
        );
        $view = isset($views[$view]) ? $views[$view] : $views[1];
        $criteria = new CDbCriteria();
        $criteria->compare("brand", $brand->id);
        if (isset($type->id))
        {
            $criteria->compare("type", $type->id);
        }
        $goodsCount = Goods::model()->cache(60*60*48)->count($criteria);
        $criteria->limit = $view['limit'];
        $criteria->order = "t.name asc";
        $pages = new CPagination($goodsCount);
        $pages->setPageSize($view['limit']);
        $pages->applyLimit($criteria);
        $goods = Goods::model()->cache(60*60*24)->with(array(
            'type_data'=>array(
                "joinType"=>"inner join",
            )
        ))->findAll($criteria);
        $this->render("brand", array(
            "brand"=>$brand, 
            "goods"=>$goods, 
            "pages"=>$pages, 
            "view"=>$view,
            "type_selected"=>isset($type->link) ? $type : null,
        ));
    }
    
    public function actionReview($link, $id)
    {
        if (!Reviews::model()->countByAttributes(array(
            "link"=>$link,
            "id"=>$id,
        )))
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        
        $review = Reviews::model()->cache(60*60)->findByPk($id);
        $criteria = new CDbCriteria();
        $criteria->compare("t.id", $review->goods);
        $criteria->group = "t.id, rating.value";
        $criteria->order = "rating.value desc";

        $product = Goods::model()->cache(60*60)->with(array(
            "brand_data",
            "type_data",
            "primary_image",
            "rating"
        ))->find($criteria);
        
        $this->render("review", array(
            "review"=>$review,
            "product"=>$product,
        ));
    }
}

