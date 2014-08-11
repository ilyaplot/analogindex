<?php
/**
 * Изображения
 */
class Images extends CActiveRecord
{
    /**
     * Константы для ресайза
     */
    const SIZE_BIG = 1;
    const SIZE_PREVIEW = 2;
    const SIZE_LIST = 3;
    const SIZE_WIDGET = 4;
    const SIZE_SEARCH = 5;
    
    /**
     * Размеры для ресайза
     * @var array
     */
    public static $sizes = array(
        // SIZE_CONST=>array(width, height, absolute)
        2=>array(510, 0, false),
        3=>array(91, 91, true),
        4=>array(30, 37, true),
        //5=>array(400, 400, false),
        //6=>array(500, 500, true),
    );
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function getDbConnection() {
        return Yii::app()->newdb;
    }
    
    public function tableName()
    {
        return "{{images}}";
    }
    
    public function relations()
    {
        return array(
            "file_data"=>array(self::BELONGS_TO, "Files", "file", 
                "joinType"=>'INNER JOIN',
            ),
            "resized"=>array(self::HAS_MANY, "ImagesResized", "image"),
            "resized_preview"=>array(self::HAS_ONE, "ImagesResized", "image",
                "on"=>"resized_preview.size = ".self::SIZE_PREVIEW,
            ),
            "resized_list"=>array(self::HAS_ONE, "ImagesResized", "image",
                "on"=>"resized_list.size = ".self::SIZE_LIST,
            ),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "file"=>Yii::t("model", "Файл"),
            "disabled"=>Yii::t("model", "Не отображать"),
            "size"=>Yii::t("model", "Типовой размер"),
            "width"=>Yii::t("model", "Ширина"),
            "height"=>Yii::t("model", "Высота"),
        );
    }
}