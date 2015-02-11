<?php

/**
 * Изображения товаров
 */
class ArticlesImagesCopy extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "new_articles_images";
    }

    public function relations()
    {
        return array(
            "image_data" => array(self::BELONGS_TO, "Images", "image"),
        );
    }

    public function attributeLabels()
    {
        return array(
            "disabled" => Yii::t("model", "Не отображать"),
            "goods" => Yii::t("model", "Товар"),
            "image" => Yii::t("model", "Картинка"),
            "priority" => Yii::t("model", "Порядок сортировки"),
        );
    }

    public function rules()
    {
        return [
            ['article', 'unique', 'allowEmpty'=>false,
                'criteria'=>['condition'=>'image = :image','params'=>['image'=>  $this->image]]
            ]
        ];
    }
}
