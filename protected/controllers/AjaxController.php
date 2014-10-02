<?php
class AjaxController extends CController
{
    
    public function beforeAction($action) {
        Yii::app()->setLanguage(Yii::app()->user->getState("language"));
        return parent::beforeAction($action);
    }
    
    public function filters() {
        return array(
            'ajaxOnly',
            'accessControl',
        );
    }
    
    public function accessRules() {
        return array(
            array(
                'allow',
                'actions'=>array("RatingGoods"),
                'roles'=>array(Users::ROLE_USER),
            ),
            array('deny',
                'users'=>array('*'),
            ),
            array('deny',
                'roles'=>array(Users::ROLE_BANNED),
            ),
        );
    }
    
    public function actionRatingGoods($goods)
    {
        $ratingAjax=Yii::app()->request->getParam('rate', 0);
        if (!$ratingAjax)
        {
            echo -1;
            return;
        }
        if (!RatingsGoods::model()->countByAttributes(array("goods"=>$goods, "user"=>Yii::app()->user->id)))
        {
            $rating = new RatingsGoods("vote");
            $rating->user = Yii::app()->user->id;
            $rating->value = $ratingAjax;
            $rating->goods = $goods;
            if ($rating->validate())
            {
                $rating->save();
                echo Yii::t("models","Ваша оценка").": ".$rating->value;
            } else {
                echo -1;
            }
        } else {
            $rating = RatingsGoods::model()->findByAttributes(array("goods"=>$goods, "user"=>Yii::app()->user->id));
            echo Yii::t("models","Ваша оценка").": ".$rating->value;
        }
    }
}