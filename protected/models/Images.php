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
        //5=>array(100, 100, false),
        6=>array(131, 131),
    );
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
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
                "joinType"=>'left join',
            ),
            "size2_data"=>array(self::BELONGS_TO, "Files", "size2", 
                "joinType"=>'left join',
            ),
            "size3_data"=>array(self::BELONGS_TO, "Files", "size3", 
                "joinType"=>'left join',
            ),
            "size4_data"=>array(self::BELONGS_TO, "Files", "size4", 
                "joinType"=>'left join',
            ),
            "size5_data"=>array(self::BELONGS_TO, "Files", "size5", 
                "joinType"=>'left join',
            ),
            "size6_data"=>array(self::BELONGS_TO, "Files", "size6", 
                "joinType"=>'left join',
            ),
            "size7_data"=>array(self::BELONGS_TO, "Files", "size7", 
                "joinType"=>'left join',
            ),
            "size8_data"=>array(self::BELONGS_TO, "Files", "size8", 
                "joinType"=>'left join',
            ),
            "size9_data"=>array(self::BELONGS_TO, "Files", "size9", 
                "joinType"=>'left join',
            ),
            "size10_data"=>array(self::BELONGS_TO, "Files", "size10", 
                "joinType"=>'left join',
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