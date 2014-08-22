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
            ),
            // Вес в граммах
            3=>array(
                "Корпус::::Вес, г: (?P<weight>[\d\.]+)"=>array(
                    "function"=>function($matches, $lang){
                        return doubleval($matches['weight']);
                    }
                )
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
                )
            ),
            
            // Количество ядер 
            5=>array(
                "Технические характеристики::::Количество ядер: (?P<cores>\d+)"=>array(
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
                "Технические характеристики::::Процессор:.*[^\d\.,]{1,}(?P<freq>[\d,\.]+) MГц"=>array(
                    "function"=>function($matches, $lang) {
                        $matches['freq'] = str_replace(",", ".", $matches['freq']);
                        return doubleval($matches['freq']*1000*1000);
                    }
                ),
            ),
            /**

            // Модель процессора (чипсет)
            7=>array(
                "Features.Chipset::::(?P<chipset>.*)"=>array(
                    "function"=>function($matches, $lang) {
                        return trim($matches['chipset']);
                    }
                )
            ),
            // Оперативка в байтах
            8=>array(
                "Memory.Internal::::.*, (?P<memory>[\d\.]+)\sGB\sRAM.*"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Memory.Internal::::.*, (?P<memory>[\d\.]+)\sMB\sRAM.*"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "Memory.Internal::::.*, (?P<memory>[\d\.]+)\sKB\sRAM.*"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024);
                    }
                ),
                "Memory.Internal::::.*[^\d](?P<memory>[\d\.]+)\sGB\sRAM.*"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Memory.Internal::::.*[^\d](?P<memory>[\d\.]+)\sMB\sRAM.*"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "Memory.Internal::::.*[^\d](?P<memory>[\d\.]+)\sKB\sRAM.*"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024);
                    }
                )  
            ),
            // внутренняя память в байтах
            9=>array(
                "Memory.Internal::::[^\d]*(?P<memory>[\d\.]+)\sGB.*[^RAM]"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Memory.Internal::::[^\d]*(?P<memory>[\d\.]+)\sMB.*[^RAM]"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "Memory.Internal::::[^\d]*(?P<memory>[\d\.]+)\sKB.*[^RAM]"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024);
                    }
                ),
                "^Memory.Internal::::(?P<memory>[\d\.]+)\sGB$"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "^Memory.Internal::::(?P<memory>[\d\.]+)\sMB$"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "^Memory.Internal::::(?P<memory>[\d\.]+)\sKB$"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024);
                    }
                ),
            ),
            // Внутренняя память в байтах
            10=>array(
                "Memory.Card slot::::[^\d]*(?P<memory>[\d\.]+)\sGB.*"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024*1024);
                    }
                ),
                "Memory.Card slot::::[^\d]*(?P<memory>[\d\.]+)\sMB.*"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024*1024);
                    }
                ),
                "Memory.Card slot::::[^\d]*(?P<memory>[\d\.]+)\sKB.*"=>array(
                    "function"=>function($matches, $lang) {
                        return doubleval($matches['memory']*1024);
                    }
                )
            ),
            11=>array(
                "Display.Type::::(?P<display>.*)"=>array(
                    "function"=>function($matches, $lang) {
                        $replaces = array(
                            "capacitive"=>"емкостной",
                            "touchscreen"=>"сенсорный",
                            "resistive"=>"резистивный",
                            "colors"=>"цветов",
                            "Monochrome"=>"монохромный",
                            "graphic"=>"графический",
                        );
                        if ($lang == 'ru')
                        {
                            foreach ($replaces as $from=>$to)
                                $matches['display'] = str_replace($from, $to, $matches['display']);
                        }
                        return trim($matches["display"]);
                    }
                )
            ),
            12=>array(
                "Display.Size::::(?P<width>[\d\.]+) x (?P<height>[\d\.]+) pixels"=>array(
                    "function"=>function($matches, $lang){
                        return array(
                            doubleval($matches['width']),
                            doubleval($matches['height']),
                        );
                    }
                ),
            ),
            13=>array(
                "Display.Size::::.*\s(?P<size>[\d\.]+) inches.*"=>array(
                    "function"=>function($matches, $lang){
                        return doubleval($matches['size']);
                    }
                ),
            ),
            14=>array(
                "Features.OS::::(?P<os>.*)"=>array(
                    "function"=>function($matches, $lang){
                        return trim($matches['os']);
                    }
                ),
            ),
            15=>array(
                //"General. ::::HSDPA 850 / 1900 / 2100 - D850"
                "General.[\d]G Network::::(?P<name>[^\s]*) (?P<standarts>[\d\s/]+)"=>array(
                    "function"=>function($matches, $lang){
                        $name = trim($matches['name']);
                        $standarts = explode(" / ", $matches['standarts']);
                        $return = array();
                        foreach($standarts as $std)
                        {
                            if (!trim($std))
                                continue;
                            $return[] = trim($name)." ".trim($std);
                        }
                        return $return;
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                ),
                "General. ::::(?P<name>[^\s]*) (?P<standarts>[\d\s/]+)"=>array(
                    "function"=>function($matches, $lang){
                        $name = trim($matches['name']);
                        $standarts = explode(" / ", $matches['standarts']);
                        $return = array();
                        foreach($standarts as $std)
                        {
                            if (!trim($std))
                                continue;
                            $return[] = trim($name)." ".trim($std);
                        }
                        return $return;
                    },
                    "nobreak"=>true,
                    "merge"=>true,
                )
            ),
            16=>array(
                "Display.Protection::::(?P<protection>.*)"=>array(
                    "function"=>function($matches, $lang){
                        return trim($matches['protection']);
                    }
                ),
            ),
            17=>array(
                "General.SIM::::(?P<sim>.*)"=>array(
                    "function"=>function($matches, $lang){
                        return trim($matches['sim']);
                    }
                ),
            ),
            18=>array(
                "Data.WLAN::::Wi-Fi 802\.11 (?P<standarts>[\w/]+),(?P<other>)"=>array(
                    "function"=>function($matches, $lang){
                        $standarts = array();
                        $std = explode("/", trim($matches['standarts']));
                        foreach ($std as $s)
                        {
                            if (!$s)
                                continue;
                            $standarts[] = "802.11 ".trim($s);
                        }
                        $etc = explode(",", trim($matches['other']));
                        foreach ($etc as $e)
                        {
                            if (!$e)
                                continue;
                            $standarts[] = trim($e);
                        }
                        return $standarts;
                    }
                ),
                "Data.WLAN::::Wi-Fi 802\.11 (?P<standarts>[\w/]+)"=>array(
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
                )
            ),
            19=>array(
                "Data.Bluetooth::::(?P<bluetooth>.*)"=>array(
                    "function"=>function($matches, $lang){
                        return trim($matches['bluetooth']);
                    }
                )
            ),
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