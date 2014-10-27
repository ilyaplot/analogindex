<?php

/**
 * Централизованное форматирование характеристик, расстановка ссылок
 * 
 */
class CharacteristicItem
{

    public $id;
    public $catalog;
    public $name;
    public $description;
    public $raw;
    public $link;
    public $linkValue;
    
    protected $formatter;

    /**
     * @todo Допилить параметры компонента
     * @var type 
     */
    protected $params = array(
        "createLinks" => false, // Преобразовывать значения в ссылки по фильтрам
    );

    /**
     * Список операционных систем
     * @var CActiveRecord 
     */
    public static $os;

    /**
     * Список моделей процессоров
     * @var CActiveRecord 
     */
    public static $cpu;

    /**
     * Объект модели продукта
     * @var CActiveRecord
     */
    protected $product;

    /**
     * 
     * @param int $id  PK характеристикик
     * @param string $catalog Название категории
     * @param string $name Наименование характеристики
     * @param string $formatter Функция форматирования значения
     * @param string $description Описание характеристики
     * @param string $raw Исходное значение из базы
     */
    public function __construct($id, $catalog, $name, $formatter, $description, $link, $linkValue, $raw, $product = null, $params = array())
    {
        $this->id = $id;
        $this->catalog = $catalog;
        $this->name = $name;
        $this->linkValue = $linkValue;
        // Если не существует форматтера, создаем заглушку
        if (method_exists(Yii::app()->format, $formatter)) {
            $this->formatter = $formatter;
        } else {
            $this->formatter = "formatNone";
        }

        $this->description = $description;
        $this->link = $link;
        $this->raw = $raw;
        $this->product = $product;
        
        $this->params['createLinks'] = !empty($params['createLinks']) ? $params['createLinks'] : false;
    }

    /**
     * Возвращает отображаемое значение
     * @param boolean $link Преобразовывать в ссылку
     * @return string
     * 
     * @todo Дописать генерацию ссылок
     */
    public function getValue($link = null, $language = null)
    {
        if (!$language !== null) {
            Yii::app()->sourceLanguage = 'ru';
            Yii::app()->setLanguage($language);
        }
        $formatter = $this->formatter;
        $value = Yii::app()->format->$formatter($this->raw);

        Yii::app()->setLanguage(Yii::app()->sourceLanguage);

        if ($this->params['createLinks'] == true && $link === null)
            $link = true;
        
        if ($link)
        {
            return $this->createLink($value, $language);
        }

        return $value;
    }

    /**
     * Возвращает html ссылку по значению характеристики
     * @param string $language
     * 
     * @todo Допилить определение значения ссылки
     */
    public function createLink($value, $language = null)
    {
        if (!$this->linkValue || $this->linkValue == 'any')
            return $value;
        
        
        if (!$language)
            $language = Yii::app()->language;

        $language = Language::getZoneForLang($language);

        
        
        if (!empty($this->product)) {
            
            // Возвращаем результат в виде ссылки, если это возможно
            return CHtml::link($value, Yii::app()->createUrl("site/type", array(
                "type" => $this->product->type_data->link,
                "language" => $language,
                $this->link => $this->linkValue,
            )));

        }

        return $value;
    }

}
