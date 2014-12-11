<?php

class CharacteristicsLinks
{

    public $characteristics = array();

    public function __construct($characteristics)
    {
        $this->characteristics = $characteristics;
    }

    public function getLinks($attributes)
    {
        $os = Os::model()->findAll();
        $os = CHtml::listData($os, "link", "name");

        //$processors = Processors::model()->findAll();
        //$processors = CHtml::listData($processors, "link", "name");

        
        $gpu = Gpu::model()->findAll();
        $gpu = CHtml::listData($gpu, "link", "name");
        
        foreach ($this->characteristics as $catalog) {
            foreach ($catalog as $characteristic) {
                if (empty($characteristic['id'])) {
                    $characteristic = $catalog;
                }
                switch ($characteristic['id']) {
                    case 5 : // cpu cores
                        $cores = $characteristic['value'];

                        if ($cores == 0)
                            break;

                        if ($cores == 1) {
                            $attributes['cores'] = '1';
                            break;
                        }

                        if ($cores == 2) {
                            $attributes['cores'] = '2';
                            break;
                        }

                        $attributes['cores'] = '3plus';
                        break;
                        
                    case 6 : // cpu freq
                        $freq = round($characteristic['raw'] / 1000 / 1000 / 1000, 2);

                        if (!$freq) {
                            $attributes['cpufreq'] = 0;
                            break;
                        }

                        if ($freq <= 1) {
                            $attributes['cpufreq'] = 1;
                            break;
                        }

                        if ($freq <= 2) {

                            $attributes['cpufreq'] = 2;
                            break;
                        }

                        $attributes['cpufreq'] = '2plus';


                        break;
                        /**
                    case 7 : // cpu model
                        foreach ($processors as $link => $processor) {

                            if (false !== strripos($characteristic['value'], $processor)) {
                                echo ".";
                                $attributes['processor'] = $link;
                                break;
                            }
                        }
                        break;
                         * 
                         */
                    case 8 : // ram
                        $ram = round($characteristic['raw'] / 1024 / 1024, 2);
                        if (!$ram) {
                            $attributes['ram'] = 0;
                            break;
                        }

                        if ($ram <= 512) {
                            $attributes['ram'] = 512;
                            break;
                        }

                        if ($ram <= 1024) {
                            $attributes['ram'] = '512-1024';
                            break;
                        }

                        if ($ram <= 2048) {
                            $attributes['ram'] = '1024-2048';
                            break;
                        }

                        if ($ram <= 4096) {
                            $attributes['ram'] = '2048-4096';
                            break;
                        }

                        $attributes['ram'] = "4096plus";

                        break;
                    case 13 : // screensize
                        $screensize = $characteristic['value'];
                        if ($screensize == 0)
                            break;

                        if ($screensize < 5) {
                            $attributes['screensize'] = '0-5';
                            break;
                        }

                        if ($screensize < 7) {
                            $attributes['screensize'] = '5-7';
                            break;
                        }

                        if ($screensize < 10) {
                            $attributes['screensize'] = '7-10';
                            break;
                        }
                        $attributes['screensize'] = '10plus';
                        break;
                    case 14 : // OS
                        foreach ($os as $link => $osItem) {
                            if (false !== strripos($characteristic['value'], $osItem)) {
                                $attributes['os'] = $link;
                                break;
                            }
                        }
                        break;
                    case 31 : // gpu model
                        foreach ($gpu as $link => $processor) {

                            if (false !== strripos($characteristic['value'], $processor)) {
                                echo ".";
                                $attributes['processor'] = $link;
                                break;
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return $attributes;
    }

    public function getCharacteristics($type)
    {
        $os = Os::model()->findAll();
        $os = CHtml::listData($os, "link", "name");
        //$processors = Processors::model()->findAll();
        //$processors = CHtml::listData($processors, "link", "name");
        
        foreach ($this->characteristics as &$catalog) {
            foreach ($catalog as &$characteristic) {
                
                if (empty($characteristic['id'])) {
                    $characteristic = &$catalog;
                }
                
                switch ($characteristic['id']) {
                    case 5 : // cpu cores
                        if (preg_match("/^<a href/isu", $characteristic['value'])) {
                            break;
                        }
                        if ($characteristic['value'] == 1) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "cores" => 1,
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }

                        if ($characteristic['value'] == 2) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "cores" => 2,
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }


                        if ($characteristic['value'] > 2) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "cores" => '3plus',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }
                        break;
                    case 6 : // cpu freq
                        if (preg_match("/^<a href/isu", $characteristic['value'])) {
                            break;
                        }
                        $freq = round($characteristic['raw'] / 1000 / 1000 / 1000, 2);
                        
                        if ($freq <= 1) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "cpufreq" => '1',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }

                        if ($freq <= 2) {

                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "cpufreq" => '2',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }

                        $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                    "type" => $type,
                                    "language" => Language::getCurrentZone(),
                                    "cpufreq" => '2plus',
                                )) . '">' . $characteristic['value'] . "</a>";
                        break;
                        /**
                    case 7 : // cpu model
                        foreach ($processors as $link => $processor) {

                            if (false !== strripos($characteristic['value'], $processor)) {
                                $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                    "type" => $type,
                                    "language" => Language::getCurrentZone(),
                                    "processor" => $link,
                                )) . '">' . $characteristic['value'] . "</a>";
                                break;
                            }
                        }
                        break;
                         * 
                         */
                    case 8 : // ram
                        if (preg_match("/^<a href/isu", $characteristic['value'])) {
                            break;
                        }
                        $ram = round($characteristic['raw'] / 1024 / 1024, 2);


                        if ($ram <= 512) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "ram" => '512',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }

                        if ($ram <= 1024) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "ram" => '512-1024',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }

                        if ($ram <= 2048) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "ram" => '1024-2048',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }

                        if ($ram <= 4096) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "ram" => '2048-4096',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }

                        $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                    "type" => $type,
                                    "language" => Language::getCurrentZone(),
                                    "ram" => '4096plus',
                                )) . '">' . $characteristic['value'] . "</a>";
                        break;
                    case 13 : // screensize
                        if (preg_match("/^<a href/isu", $characteristic['value'])) {
                            break;
                        }
                        $screensize = $characteristic['value'];
                        if ($screensize == 0)
                            break;

                        if ($screensize < 5) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "screensize" => '0-5',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }

                        if ($screensize < 7) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "screensize" => '5-7',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }

                        if ($screensize < 10) {
                            $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                        "type" => $type,
                                        "language" => Language::getCurrentZone(),
                                        "screensize" => '7-10',
                                    )) . '">' . $characteristic['value'] . "</a>";
                            break;
                        }
                        $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                    "type" => $type,
                                    "language" => Language::getCurrentZone(),
                                    "screensize" => '10plus',
                                )) . '">' . $characteristic['value'] . "</a>";
                        break;
                    case 14 : // OS
                        if (preg_match("/^<a href/isu", $characteristic['value'])) {
                            break;
                        }
                        foreach ($os as $link => $osItem) {
                            if (false !== strripos($characteristic['value'], $osItem)) {
                                $characteristic['value'] = '<a href="' . Yii::app()->createUrl("site/type", array(
                                            "type" => $type,
                                            "language" => Language::getCurrentZone(),
                                            "os" => $link,
                                        )) . '">' . $characteristic['value'] . "</a>";
                                break;
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return $this->characteristics;
    }

}
