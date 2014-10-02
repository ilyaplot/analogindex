<?php
/**
 * Обзоры
 */
class Reviews extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{reviews}}";
    }
    
    public function relations()
    {
        return array(
            "rating"=>array(self::HAS_ONE, "RatingsReviews", "review", 
                "select"=>"AVG(rating.value) as value",
            ),
            "goods_data"=>array(self::BELONGS_TO, "Goods", "goods"),
            "images"=>array(self::HAS_MANY, "ReviewsImages", "review"),
            "comments"=>array(self::HAS_MANY, "CommentsReviews", "review"),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "goods"=>Yii::t("model", "Товар"),
            "link"=>Yii::t("model", "Ссылка"),
            "lang"=>Yii::t("model", "Код языка"),
            "author"=>Yii::t("model", "Автор"),
            "title"=>Yii::t("model", "Заголовок"),
            "content"=>Yii::t("model", "Текст"),
            "priority"=>Yii::t("model", "Порядок сортировки"),
            "source"=>Yii::t("model", "Ссылка на оригинал"),
            "disabled"=>Yii::t("model", "Не показывать"),
        );
    }
    
    public function beforeSave() {
        $this->preview = $this->getWords($this->content);
        return parent::beforeSave();
    }
    
    public function getWords($str, $length = 50)
    {
        $words = explode(" ", trim(strip_tags($str)));
        return implode (" ", array_slice($words, 0, $length));
    }
    
    public function getDescription()
    {
        $words = explode(" ", trim(strip_tags($this->content)));
        $description = $words[0];
        $key = 0;
        do {
            if (isset($words[$key]))
                $key++;
            else 
                break;
            if (mb_strlen($description.$words[$key]) > 250)
                break;
            $description.=" ".$words[$key];
        } while (mb_strlen($description) < 245);
        $description = htmlspecialchars($description);
        return mb_substr($description, 0, 250);
    }
}