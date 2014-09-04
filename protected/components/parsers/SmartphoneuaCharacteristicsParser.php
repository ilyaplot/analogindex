<?php
class SmartphoneuaCharacteristicsParser extends CharacteristicsParser
{
    public function getRules() {
        
        return array(
            //Старт продаж
            1=>array(
                "Технические характеристики::::Год выпуска: (?P<year>\d+)"=>array(
                    "function"=>function($matches, $lang){
                        Yii::app()->language = $lang;
                        return Yii::app()->dateFormatter->format("yyy", "{$matches['year']}-01-01 00:00:00");
                    },
                ),
                "Общие характеристики::::Год выпуска: (?P<year>\d+)"=>array(
                    "function"=>function($matches, $lang){
                        Yii::app()->language = $lang;
                        return Yii::app()->dateFormatter->format("yyy", "{$matches['year']}-01-01 00:00:00");
                    },
                ),
            ),
            // Вес в граммах
            3=>array(
                "Корпус::::Вес, г: (?P<weight>[\d\.]+)"=>array(
                    "function"=>function($matches, $lang){
                        return doubleval($matches['weight']);
                    }
                ),      
            ),  
            // Размеры (в д ш)
            4=>array(
                "Корпус::::Высота, мм: (?P<h>[\d\.]+)"=>array(
                    "function"=>function($matches, $lang){
                        return array(
                            doubleval($matches['h']),
                        );
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                ),
                "Корпус::::Длина, мм: (?P<h>[\d\.]+)"=>array(
                    "function"=>function($matches, $lang){
                        return array(
                            doubleval($matches['h']),
                        );
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                ),
                "Корпус::::Ширина, мм: (?P<w>[\d\.]+)"=>array(
                    "function"=>function($matches, $lang){
                        return array(
                            doubleval($matches['w']),
                        );
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                ),
                "Корпус::::Толщина, мм: (?P<l>[\d\.]+)"=>array(
                    "function"=>function($matches, $lang){
                        return array(
                            doubleval($matches['l']),
                        );
                    },
                    "merge"=>true,
                    "nobreak"=>true,
                ),
            ),
            
            // Количество ядер 
            5=>array(
                "Технические характеристики::::Количество ядер: (?P<cores>\d+)"=>array(
                    "function"=>function($matches, $lang) {
                        return $matches['cores'];
                    }
                ),
                "Общие характеристики::::Процессор: (?P<cores>\d+)-ядерный"=>array(
                    "function"=>function($matches, $lang) {
                        return $matches['cores'];
                    }
                ),
            ),
            
                        
            // Частота процессора в Гц
            6=>array(
                "Технические характеристики::::Процессор:.*[^\d\.,]{1,}(?P<freq>[\d,\.]+) ГГц"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['freq'] = str_replace(",", ".", $matches['freq']);
                        return doubleval($matches['freq']*1000*1000*1000);
                    }
                ),
                "Общие характеристики::::Процессор:.*[^\d\.,]{1,}(?P<freq>[\d,\.]+) ГГц"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['freq'] = str_replace(",", ".", $matches['freq']);
                        return doubleval($matches['freq']*1000*1000*1000);
                    }
                ),
                "Технические характеристики::::Процессор:.*[^\d\.,]{1,}(?P<freq>[\d,\.]+) MГц"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['freq'] = str_replace(",", ".", $matches['freq']);
                        return doubleval($matches['freq']*1000*1000);
                    }
                ),
                "Общие характеристики::::Процессор:.*[^\d\.,]{1,}(?P<freq>[\d,\.]+) МГц"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['freq'] = str_replace(",", ".", $matches['freq']);
                        return doubleval($matches['freq']*1000*1000*1000);
                    }
                ),
            ),
            // Модель процессора (чипсет)
            7=>array(
                //Процессор: 4-ядерный Mediatek MT6582, 1.3 ГГц
                "Общие характеристики::::Процессор: [\d]-ядерный (?P<chipset>.*), [\d,\.][Г|М]Гц"=>array(
                    "function"=>function($matches, $lang) {
                        return trim($matches['chipset']);
                    }
                ),
                "Технические характеристики::::Процессор: [\d]-ядерный (?P<chipset>.*), [\d,\.][Г|М]Гц"=>array(
                    "function"=>function($matches, $lang) {
                        return trim($matches['chipset']);
                    }
                ),
            ),
            
            // Оперативка в байтах
            8=>array(
                "Общие характеристики::::Память:.* (?P<memory>[\d\.,]+) ГБ ОЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Общие характеристики::::Память:.* (?P<memory>[\d\.,]+) МБ ОЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "Общие характеристики::::Память: (?P<memory>[\d\.,]+) ГБ DDR[\d] ОЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) ГБ ОЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) МБ ОЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) Гб DDR[\d] ОЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
            ),
            // внутренняя память в байтах
            9=>array(
                "Общие характеристики::::Память: .*ОЗУ, (?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Общие характеристики::::Память: .*ОЗУ, (?P<memory>[\d\.,]+) М[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "Технические характеристики::::Память: .* ОЗУ, (?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Технические характеристики::::Память: .* ОЗУ, (?P<memory>[\d\.,]+) М[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024);
                    }
                ), 
                "Общие характеристики::::Память: (?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Общие характеристики::::Память: (?P<memory>[\d\.,]+) М[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) М[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "Технические характеристики::::Память: .*/(?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Общие характеристики::::Память: .*/(?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
            ),
            // Внешняя память в байтах
            10=>array(
                "Общие характеристики::::Тип карт памяти: .*максимальный объем (?P<memory>[\d\.]+) Г[бБ]+"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Технические характеристики::::Тип карт памяти: .*до (?P<memory>[\d\.]+) Г[бБ]+"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
            ),
            11=>array(
                "Основной дисплей::::Тип дисплея:(?P<display>.*)"=>array(
                    "function"=>function($matches, $lang) {
                        return trim($matches["display"]);
                    }
                ),
                "Дисплей::::Тип дисплея:(?P<display>.*)"=>array(
                    "function"=>function($matches, $lang) {
                        return trim($matches["display"]);
                    }
                )
            ),
            12=>array(
                "Дисплей::::Разрешение: (?P<width>[\d]+)\*(?P<height>[\d]+)"=>array(
                    "function"=>function($matches, $lang){
                        return array(
                            intval($matches['width']),
                            intval($matches['height']),
                        );
                    }
                ),
                "Основной дисплей::::Разрешение д.: (?P<width>[\d]+)\*(?P<height>[\d]+)"=>array(
                    "function"=>function($matches, $lang){
                        return array(
                            intval($matches['width']),
                            intval($matches['height']),
                        );
                    }
                ),
            ),
            13=>array(
                "Основной дисплей::::Размер, д.: (?P<size>[\d\.,]+)"=>array(
                    "function"=>function($matches, $lang){
                        $matches['size'] = str_replace(",", ".", $matches['size']);
                        return doubleval($matches['size']);
                    }
                ),
                "Дисплей::::Размер, д.: (?P<size>[\d\.,]+)"=>array(
                    "function"=>function($matches, $lang){
                        $matches['size'] = str_replace(",", ".", $matches['size']);
                        return doubleval($matches['size']);
                    }
                ),
            ),
            14=>array(
                "Операционная система::::Версия: (?P<os>.*)"=>array(
                    "function"=>function($matches, $lang){
                        return trim($matches['os']);
                    }
                ),
            ),
            
            15=>array(
                "Стандарт и частотный диапазон::::GSM:"=>array(
                    "function"=>function($matches, $lang){
                        return array("GSM 850");
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                    "unique"=>true,
                ),
                "Стандарт и частотный диапазон::::GSM (?P<std>[\d]+)"=>array(
                    "function"=>function($matches, $lang){
                        return array("GSM ".trim($matches['std']));
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                    "unique"=>true,
                ),
                "Стандарт и частотный диапазон::::3G:"=>array(
                    "function"=>function($matches, $lang){
                        return array("HSPDA 850");
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                    "unique"=>true,
                ),
                "Стандарт и частотный диапазон::::WCDMA"=>array(
                    "function"=>function($matches, $lang){
                        return array("HSPDA 850");
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                    "unique"=>true,
                ),
                "Стандарт и частотный диапазон::::WCDMA - версия: (?P<std>[\d/]+)"=>array(
                    "function"=>function($matches, $lang){
                        $result = array();
                        $std = explode("/",$matches['std']);
                        foreach ($std as $s)
                        {
                            $s = trim($s);
                            if (!empty($s))
                                $result[] = "HSPDA {$s}";
                        }
                        return $result;
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                    "unique"=>true,
                ),
            ),
            18=>array(
                "Передача данных::::Версия WiFi: 802.11 (?P<standarts>[\w/]+)"=>array(
                    "function"=>function($matches, $lang){
                        $standarts = array();
                        $std = explode("/", trim($matches['standarts']));
                        foreach ($std as $s)
                        {
                            if (!$s)
                                continue;
                            $standarts[] = "802.11 ".trim($s);
                        }
                        return $standarts;
                    }
                ),
                "Передача данных::::Wi-Fi: 802.11 (?P<standarts>[\w/]+)"=>array(
                    "function"=>function($matches, $lang){
                        $standarts = array();
                        $std = explode("/", trim($matches['standarts']));
                        foreach ($std as $s)
                        {
                            if (!$s)
                                continue;
                            $standarts[] = "802.11 ".trim($s);
                        }
                        return $standarts;
                    }
                ),
            ),
            19=>array(
                "Передача данных::::Версия Bluetooth: (?P<bluetooth>.*)"=>array(
                    "function"=>function($matches, $lang){
                        return trim("Bluetooth ".$matches['bluetooth']);
                    }
                ),
                "Передача данных::::Bluetooth: (?P<bluetooth>.*)"=>array(
                    "function"=>function($matches, $lang){
                        return trim($matches['bluetooth']);
                    }
                )
            ),
            /**
            20=>array(
                "Data.Infrared port::::(?P<irda>.*)"=>array(
                    "function"=>function($matches, $lang){
                        Yii::app()->language = $lang;
                        return Yii::t("goods", "Да");
                    }
                )
            ),
            21=>array(
                "Features.GPS::::(?P<gps>.*)"=>array(
                    "function"=>function($matches, $lang){
                        $replaces = array(
                            "Yes"=>"Да",
                            "with"=>"c",
                            "GLONASS"=>"ГЛОНАСС",
                        );
                        if ($lang == 'ru')
                        {
                            foreach ($replaces as $from=>$to)
                                $matches['gps'] = str_replace($from, $to, $matches['gps']);
                        }
                        return trim($matches['gps']);
                    }
                ),
            ),
            22=>array(
                "Battery. ::::.*\s(?P<power>[\d\.]+)\smAh"=>array(
                    "function"=>function($matches, $lang){
                        Yii::app()->language = $lang;
                        return $matches['power']." ".Yii::t("goods", "mAh");
                    }
                ),
            ),
            23=>array(
                "Battery. ::::(?P<type>.*)\s(?P<power>[\d\.]+)\smAh"=>array(
                    "function"=>function($matches, $lang){
                        
                        return trim($matches['type']);
                    }
                ),
            ),
            24=>array(
                "Battery.Stand-by::::Up to (?P<time>[\d\.]+) h"=>array(
                    "function"=>function($matches, $lang){
                        
                        return doubleval($matches['time']);
                    }
                )
            ),
            25=>array(
                "Battery.Talk time::::Up to (?P<time>[\d\.]+) h"=>array(
                    "function"=>function($matches, $lang){
                        
                        return doubleval($matches['time']);
                    }
                )
            ),
            26=>array(
                "Camera.Primary::::.* (?P<w>[\d]+) х (?P<h>[\d]+) pixels"=>array(
                    "function"=>function($matches, $lang){
                        return array(
                            intval($matches['w']),
                            intval($matches['h']),
                        );
                    }
                ),
            ),
            27=>array(
                "Camera.Primary::::(?P<megapixels>[\d\.]+) MP"=>array(
                    "function"=>function($matches, $lang){
                        return doubleval($matches['megapixels']);
                    }
                ),
            ),
            28=>array(
                "Camera.Primary::::.*flash.*"=>array(
                    "function"=>function($matches, $lang){
                        Yii::app()->language = $lang;
                        return Yii::t("goods", "Да");
                    }
                ),
            ),          
            29=>array(
                "Camera.Secondary::::(?P<megapixels>[\d\.]+) MP"=>array(
                    "function"=>function($matches, $lang){
                        return doubleval($matches['megapixels']);
                    }
                ),
            ),
            30=>array(
                "Features.Sensors::::(?P<sensors>.*)"=>array(
                    "function"=>function ($matches, $lang)
                    {
                        $sensors = array();
                        $std = explode(",", trim($matches['sensors']));
                        foreach ($std as $s)
                        {
                            if (!$s)
                                continue;
                            $sensors[] = trim($s);
                        }
                        return $sensors;
                    }
                )
            )
                             * 
                             */
        );
    }   
}