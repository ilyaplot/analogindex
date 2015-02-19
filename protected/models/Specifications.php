<?php
class Specifications extends CActiveRecord
{

    /**
     * Возвращать значение как оно сохранено
     */
    const VALUE_MODE_RAW = 0;
    /**
     * Возвращать значение в отформатированном видеы
     */
    const VALUE_MODE_FORMATTED = 1;
    /**
     * Возвращать ссылку на спецификацию
     */
    const VALUE_MODE_URL = 2;
    /**
     * Значение должно возвращаться как модель
     */
    const VALUE_MODE_MODEL = 3;
    /**
     * Альтернативный режим. Зависит от модели спецификации
     */
    const VALUE_MODE_ADVANCED = 4;
    
    public $needUpdate = false;
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return [
            ['lang, name, category', 'required'],
            ['name', 'length', 'min'=>2, 'max'=>333, 'allowEmpty'=>false],
            ['description', 'length', 'min'=>10, 'max'=>1024, 'allowEmpty'=>true],
        ];
    }

    public function tableName()
    {
        return "{{specifications}}";
    }

    public function relations()
    {
        return [
            'category_data'=>[self::HAS_ONE, 'SpecificationsCategories', ['key'=>'category'],
                'on'=>'t.lang = category_data.lang',
                'joinType'=>'inner join',
            ],
        ];
    }
}
