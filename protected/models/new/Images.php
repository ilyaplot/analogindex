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
    const SIZE_BRAND = 6;
    
    /**
     * Размеры для ресайза
     * @var array
     */
    public static $sizes = array(
        2=>array(510, 510),
        3=>array(91, 91),
        4=>array(30, 37),
        //5=>array(400, 400, false),
        6=>array(131, 131),
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
                "joinType"=>'inner join',
            ),
            "size1_data"=>array(self::BELONGS_TO, "Files", "size1", 
                "joinType"=>'inner join',
            ),
            "size2_data"=>array(self::BELONGS_TO, "Files", "size2", 
                "joinType"=>'inner join',
            ),
            "size3_data"=>array(self::BELONGS_TO, "Files", "size3", 
                "joinType"=>'inner join',
            ),
            "size4_data"=>array(self::BELONGS_TO, "Files", "size4", 
                "joinType"=>'inner join',
            ),
            "size5_data"=>array(self::BELONGS_TO, "Files", "size5", 
                "joinType"=>'inner join',
            ),
            "size6_data"=>array(self::BELONGS_TO, "Files", "size6", 
                "joinType"=>'inner join',
            ),
            "size7_data"=>array(self::BELONGS_TO, "Files", "size7", 
                "joinType"=>'inner join',
            ),
            "size8_data"=>array(self::BELONGS_TO, "Files", "size8", 
                "joinType"=>'inner join',
            ),
            "size9_data"=>array(self::BELONGS_TO, "Files", "size9", 
                "joinType"=>'inner join',
            ),
            "size10_data"=>array(self::BELONGS_TO, "Files", "size10", 
                "joinType"=>'inner join',
            ),
            
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "file"=>Yii::t("model", "Файл"),
            "size"=>Yii::t("model", "Типовой размер"),
            "width"=>Yii::t("model", "Ширина"),
            "height"=>Yii::t("model", "Высота"),
        );
    }
}