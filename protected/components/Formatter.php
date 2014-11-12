<?php

/**
 * Наследует форматтер от Yii и расширяет его
 */
class Formatter extends CFormatter
{

    /**
     * Формат частоты процессора
     * @var type 
     */
    public $freqFormat = array(
        'base' => 1000,
        'decimals' => 2,
        'decimalSeparator' => null,
    );

    /**
     * Формат частоты процессора
     * @var type 
     */
    public $weightFormat = array(
        'base' => 1000,
        'decimals' => 2,
        'decimalSeparator' => null,
    );

    /**
     * Пустой формат
     * @param type $value
     * @return type
     */
    public function formatNone($value)
    {
        return $value;
    }

    public function formatArrayComma($values)
    {
        $values = @json_decode($values, true);
        if (!$values)
            return false;
        return implode(", ", $values);
    }

    /**
     * Формат частоты процессора
     * @param type $value
     * @return type
     */
    public function formatFreq($value)
    {
        $sourceLanguage = Yii::app()->sourceLanguage;
        Yii::app()->sourceLanguage = 'en';
        $base = $this->freqFormat['base'];
        for ($i = 0; $base <= $value && $i < 5; $i++)
            $value = $value / $base;
        $value = round($value, $this->freqFormat['decimals']);
        $formattedValue = isset($this->freqFormat['decimalSeparator']) ? str_replace('.', $this->freqFormat['decimalSeparator'], $value) : $value;
        $params = array($value, '{n}' => $formattedValue);
        switch ($i) {
            case 0:
                $return = Yii::t('formatter', '{n} Hz', $params);
                break;
            case 1:
                $return = Yii::t('formatter', '{n} KHz', $params);
                break;
            case 2:
                $return = Yii::t('formatter', '{n} MHz', $params);
                break;
            case 3:
                $return = Yii::t('formatter', '{n} GHz', $params);
                break;
            default :
                $return = Yii::t('formatter', '{n} THz', $params);
        }
        Yii::app()->sourceLanguage = $sourceLanguage;
        return $return;
    }

    /**
     * Формат веса
     * @param type $value
     * @return type
     */
    public function formatWeight($value)
    {
        $sourceLanguage = Yii::app()->sourceLanguage;
        Yii::app()->sourceLanguage = 'en';
        $base = $this->weightFormat['base'];
        for ($i = 0; $base <= $value && $i < 5; $i++)
            $value = $value / $base;
        $value = round($value, $this->weightFormat['decimals']);
        $formattedValue = isset($this->weightFormat['decimalSeparator']) ? str_replace('.', $this->weightFormat['decimalSeparator'], $value) : $value;
        $params = array($value, '{n}' => $formattedValue);
        switch ($i) {
            case 0:
                $return = Yii::t('formatter', '{n} g', $params);
                break;
            case 1:
                $return = Yii::t('formatter', '{n} kg', $params);
                break;
            case 2:
                $return = Yii::t('formatter', '{n} t', $params);
                break;
            default :
                $return = Yii::t('formatter', '{n} kt', $params);
        }
        Yii::app()->sourceLanguage = $sourceLanguage;
        return $return;
    }

    /**
     * Формат размера трех сторон
     * @param type $values
     * @return type
     */
    public function formatDimensions($values)
    {
        $values = @json_decode($values, true);
        if (!$values)
            return null;
        $values = array_map(array('self', 'formatDimension'), $values);
        return implode(" , ", $values);
    }

    /**
     * Формат размера экрана
     * @param type $values
     * @return type
     */
    public function formatScreenResolution($values)
    {
        $values = @json_decode($values, true);
        if (!$values)
            return null;
        $sourceLanguage = Yii::app()->sourceLanguage;
        Yii::app()->sourceLanguage = 'en';
        $return = Yii::t('formatter', '{n} px', array(implode(" x ", $values), '{n}' => implode(" x ", $values)));
        Yii::app()->sourceLanguage = $sourceLanguage;
        return $return;
    }

    public function formatDimension($value)
    {
        $sourceLanguage = Yii::app()->sourceLanguage;
        Yii::app()->sourceLanguage = 'en';
        $return = Yii::t('formatter', '{n} mm', array($value, '{n}' => $value));
        Yii::app()->sourceLanguage = $sourceLanguage;
        return $return;
    }

    public function formatBatteryTime($value)
    {
        return $value;
    }

    public function formatCameraMegapixels($value)
    {
        return $value;
    }
    
    public function formatColors($values) 
    {
        $language = Yii::app()->language;
        $values = json_decode($values);
        $criteria = new CDbCriteria();
        $criteria->order = "{$language} asc";
        $criteria->condition = "id in (".implode(", ", $values).")";
        $colors = Colors::model()->cache(60*60*48)->findAll($criteria);
        $result = [];
        foreach ($colors as $color) {
            $result[]= "<span class=\"color-label\" style=\"background-color: {$color->code};\">{$color->$language}</span> ";
        }
        $result = implode(", ", $result);
        return $result;
    }

}
