<?php
class ReviewsController extends Controller
{
    public function actionIndex($goods, $link, $id)
    {
        if (!Reviews::model()->countByAttributes(array(
                    "link" => $link,
                    "id" => $id,
                )))
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));

        $review = Reviews::model()->cache(60 * 60)->findByPk($id);
        $criteria = new CDbCriteria();
        $criteria->compare("t.id", $review->goods);
        $criteria->group = "t.id, rating.value";
        $criteria->order = "rating.value desc";

        $product = Goods::model()->cache(60 * 60)->with(array(
                    "brand_data",
                    "type_data",
                    "primary_image",
                    "rating"
                ))->find($criteria);

        $ratingDisabled = 1;
        if (!Yii::app()->user->isGuest &&
                !Yii::app()->user->getState("readonly") &&
                !RatingsGoods::model()->countByAttributes(array("goods" => $product->id, "user" => Yii::app()->user->id))) {
            $ratingDisabled = 0;
        }

        $this->pageDescription = $review->getDescription();
        $keywords = array();
        if (!empty($product->synonims)) {
            foreach ($product->synonims as $synonim) {
                $keywords[] = $synonim->name;
            }
        }
        $keywords = array_merge($keywords, array(
            $product->name,
            $product->brand_data->name,
            $product->type_data->name->name,
        ));
        $this->pageKeywords = implode(", ", $keywords);
        $this->pageTitle = $product->brand_data->name." ".$product->name.": " .$review->title;
        $export = new Export();
        $this->render("review", array(
            "review" => $review,
            'export' => $export,
            "product" => $product,
            "ratingDisabled" => $ratingDisabled,
        ));
    }
    
    public function actionList($brand, $product)
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
        $criteria->condition = "t.lang = :lang and t.goods = :goods";
        $criteria->params = ['lang'=>Yii::app()->language, 'goods'=>$product->id];
        $reviews = Reviews::model()->findAll($criteria);
        
        $this->render("list", ["reviews"=>$reviews, "product"=>$product]);
    }
}