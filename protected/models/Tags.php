<?php

/**
 * name - название тэга
 * type - тип тэга и первая часть ссылки
 * link - вторая часть ссылки
 * disabled - ключ неактивности тэга
 * 
 */
class Tags extends CActiveRecord
{

    const TYPE_BRAND = 'brand';
    const TYPE_PRODUCT = 'product';
    const TYPE_OS = 'os';
    
    const TYPE_WORD = 'word';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{tags}}";
    }

    public function rules()
    {
        return [
            ['name, type, link', 'required'],
            ['name', 'length', 'min' => 3, 'max' => 100, 'allowEmpty' => false],
            ['disabled', 'type', 'type' => 'integer', 'allowEmpty' => true],
            ['link', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'link', 
                'className'=>'Tags', 
                'criteria'=>['condition'=>'type = :type', 'params'=>['type'=>  $this->type]]
            ]
        ];
    }

    public function beforeValidate()
    {
        $urlManager = new UrlManager();
        if ($this->isNewRecord) {
            $this->link = $urlManager->translitUrl(trim($this->name));
            $this->link = str_replace("_", "-", $this->link);
        }
        return parent::beforeValidate();
    }

    public function relations()
    {
        return [
            'goods' => [self::HAS_ONE, 'GoodsTags', 'tag'],
            'brand' => [self::HAS_ONE, 'BrandsTags', 'tag'],
        ];
    }
    
    public function filter() {
        $query = "update {{tags}} set disabled = 1 where type='product' and length(name) < 6 and (name REGEXP '^[a-z]{1,2}\s{0,1}[0-9]{1,3}$' or name REGEXP '^[0-9]{1,4}[a-z]+$') and disabled = 0";
        return $this->getDbConnection()->createCommand($query)->execute();
    }
    
    /**
     * Строит URL для каждого типа ссылок
     * @param string $type
     * @param string $lang
     * @return string
     */
    public function getLink($type, $lang)
    {
        switch($this->type) {
            // Ссылка на бренд
            case 'brand' : 
                $link = CHtml::link($this->name, 
                    Yii::app()->createAbsoluteUrl("site/brand", [
                        'language' =>  Language::getZoneForLang($lang),
                        'link' =>  $this->link,
                    ]));
                break;
            // Ссылка на продукт (должна быть перед default!)
            case 'product' : 
                $product = !empty($this->goods->goods_data) ? $this->goods->goods_data : '';
                if  (!empty($product) && !empty($product->type_data->link) && !empty($product->brand_data->link)) {
                $link = CHtml::link($this->name, 
                    Yii::app()->createAbsoluteUrl("site/goods", [
                        'language' =>  Language::getZoneForLang($lang),
                        'type' => $product->type_data->link,
                        'brand' => $product->brand_data->link,
                        'link' => $product->link,
                    ]));
                    break;
                }
            // Ссылка на тэг
            default: 
                $link = CHtml::link($this->name, 
                    Yii::app()->createAbsoluteUrl("tag/{$type}", [
                        'language' =>  Language::getZoneForLang($lang),
                        'type' => $this->type,
                        'tag' => $this->link,
                    ]), ['rel'=>'tag']);
                break;
        }
        
        return $link;
    }
    
    public function getProduct()
    {
        return ($this->type == self::TYPE_PRODUCT && !empty($this->goods->goods)) ? (int) $this->goods->goods : false;
    }
    
    public function getBrand()
    {
        return ($this->type == self::TYPE_BRAND && !empty($this->brand->brand)) ? (int) $this->brand->brand : false;
    }
}
