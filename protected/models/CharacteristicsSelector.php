<?php

class CharacteristicsSelector extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{characteristics_selector}}";
    }

    public function rules()
    {
        return array(
            array('id, type, brand', 'required'),
            array('brand', 'length', 'allowEmpty' => false, 'min' => 2, 'max' => 100),
            array('os', 'length', 'allowEmpty' => true, 'min' => 2, 'max' => 100),
            array('screensize', 'length', 'allowEmpty' => true, 'max' => 5),
            array('cores', 'length', 'allowEmpty' => true, 'max' => 6),
            array('cpufreq', 'length', 'allowEmpty' => true, 'max' => 6),
            array('ram', 'length', 'allowEmpty' => true, 'max' => 10),
            array('processor, gpu', 'length', 'allowEmpty' => true, 'max' => 100),
        );
    }

}
