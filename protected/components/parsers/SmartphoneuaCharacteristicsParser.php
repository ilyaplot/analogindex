<?php

class SmartphoneuaCharacteristicsParser extends CharacteristicsParser
{

    public function getRules()
    {

        return array(
            //Старт продаж
            1 => array(
                "Технические характеристики::::Год выпуска: (?P<year>\d+)" => array(
                    "function" => function($matches, $lang) {
                        Yii::app()->language = $lang;
                        return Yii::app()->dateFormatter->format("yyy", "{$matches['year']}-01-01 00:00:00");
                    },
                ),
                "Общие характеристики::::Год выпуска: (?P<year>\d+)" => array(
                    "function" => function($matches, $lang) {
                        Yii::app()->language = $lang;
                        return Yii::app()->dateFormatter->format("yyy", "{$matches['year']}-01-01 00:00:00");
                    },
                ),
            ),
            // Вес в граммах
            3 => array(
                "Корпус::::Вес, г: (?P<weight>[\d\.]+)" => array(
                    "function" => function($matches, $lang) {
                        return doubleval($matches['weight']);
                    }
                ),
            ),
            // Размеры (в д ш)
            4 => array(
                "Корпус::::Высота, мм: (?P<h>[\d\.]+)" => array(
                    "function" => function($matches, $lang) {
                        return array(
                            doubleval($matches['h']),
                        );
                    },
                    "nobreak" => true,
                    "merge" => true,
                ),
                "Корпус::::Длина, мм: (?P<h>[\d\.]+)" => array(
                    "function" => function($matches, $lang) {
                        return array(
                            doubleval($matches['h']),
                        );
                    },
                    "nobreak" => true,
                    "merge" => true,
                ),
                "Корпус::::Ширина, мм: (?P<w>[\d\.]+)" => array(
                    "function" => function($matches, $lang) {
                        return array(
                            doubleval($matches['w']),
                        );
                    },
                    "nobreak" => true,
                    "merge" => true,
                ),
                "Корпус::::Толщина, мм: (?P<l>[\d\.]+)" => array(
                    "function" => function($matches, $lang) {
                        return array(
                            doubleval($matches['l']),
                        );
                    },
                    "merge" => true,
                    "nobreak" => true,
                ),
            ),
            // Количество ядер 
            5 => array(
                "Технические характеристики::::Количество ядер: (?P<cores>\d+)" => array(
                    "function" => function($matches, $lang) {
                        return $matches['cores'];
                    }
                ),
                "Общие характеристики::::Процессор: (?P<cores>\d+)-ядерный" => array(
                    "function" => function($matches, $lang) {
                        return $matches['cores'];
                    }
                ),
            ),
            // Частота процессора в Гц
            6 => array(
                "Технические характеристики::::Процессор:.*[^\d\.,]{1,}(?P<freq>[\d,\.]+) ГГц" => array(
                    "function" => function($matches, $lang) {
                        $matches['freq'] = str_replace(",", ".", $matches['freq']);
                        return doubleval($matches['freq'] * 1000 * 1000 * 1000);
                    }
                ),
                "Общие характеристики::::Процессор:.*[^\d\.,]{1,}(?P<freq>[\d,\.]+) ГГц" => array(
                    "function" => function($matches, $lang) {
                        $matches['freq'] = str_replace(",", ".", $matches['freq']);
                        return doubleval($matches['freq'] * 1000 * 1000 * 1000);
                    }
                ),
                "Технические характеристики::::Процессор:.*[^\d\.,]{1,}(?P<freq>[\d,\.]+) MГц" => array(
                    "function" => function($matches, $lang) {
                        $matches['freq'] = str_replace(",", ".", $matches['freq']);
                        return doubleval($matches['freq'] * 1000 * 1000);
                    }
                ),
                "Общие характеристики::::Процессор:.*[^\d\.,]{1,}(?P<freq>[\d,\.]+) МГц" => array(
                    "function" => function($matches, $lang) {
                        $matches['freq'] = str_replace(",", ".", $matches['freq']);
                        return doubleval($matches['freq'] * 1000 * 1000);
                    }
                ),
            ),
            // Модель процессора (чипсет)
            7 => array(
                //Процессор: 4-ядерный Mediatek MT6582, 1.3 ГГц
                "Общие характеристики::::Процессор: [\d]-ядерный (?P<chipset>.*), [\d,\.][Г|М]Гц" => array(
                    "function" => function($matches, $lang) {
                        return trim($matches['chipset']);
                    }
                ),
                "Технические характеристики::::Процессор: [\d]-ядерный (?P<chipset>.*), [\d,\.][Г|М]Гц" => array(
                    "function" => function($matches, $lang) {
                        return trim($matches['chipset']);
                    }
                ),
            ),
            // Оперативка в байтах
            8 => array(
                "Общие характеристики::::Память:.* (?P<memory>[\d\.,]+) ГБ ОЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
                "Общие характеристики::::Память:.* (?P<memory>[\d\.,]+) МБ ОЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024);
                    }
                ),
                "Общие характеристики::::Память: (?P<memory>[\d\.,]+) ГБ DDR[\d] ОЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) ГБ ОЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) МБ ОЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) Гб DDR[\d] ОЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
            ),
            // внутренняя память в байтах
            9 => array(
                "Общие характеристики::::Память: .*ОЗУ, (?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
                "Общие характеристики::::Память: .*ОЗУ, (?P<memory>[\d\.,]+) М[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024);
                    }
                ),
                "Технические характеристики::::Память: .* ОЗУ, (?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
                "Технические характеристики::::Память: .* ОЗУ, (?P<memory>[\d\.,]+) М[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024);
                    }
                ),
                "Общие характеристики::::Память: (?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
                "Общие характеристики::::Память: (?P<memory>[\d\.,]+) М[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
                "Технические характеристики::::Память: (?P<memory>[\d\.,]+) М[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024);
                    }
                ),
                "Технические характеристики::::Память: .*/(?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
                "Общие характеристики::::Память: .*/(?P<memory>[\d\.,]+) Г[Бб]+ ПЗУ" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
            ),
            // Внешняя память в байтах
            10 => array(
                "Общие характеристики::::Тип карт памяти: .*максимальный объем (?P<memory>[\d\.]+) Г[бБ]+" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
                "Технические характеристики::::Тип карт памяти: .*до (?P<memory>[\d\.]+) Г[бБ]+" => array(
                    "function" => function($matches, $lang) {
                        $matches['memory'] = str_replace(",", ".", $matches['memory']);
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ),
            ),
            11 => array(
                "Основной дисплей::::Тип дисплея:(?P<display>.*)" => array(
                    "function" => function($matches, $lang) {
                        return trim($matches["display"]);
                    }
                ),
                "Дисплей::::Тип дисплея:(?P<display>.*)" => array(
                    "function" => function($matches, $lang) {
                        return trim($matches["display"]);
                    }
                )
            ),
            12 => array(
                "Дисплей::::Разрешение: (?P<width>[\d]+)\*(?P<height>[\d]+)" => array(
                    "function" => function($matches, $lang) {
                        return array(
                            intval($matches['width']),
                            intval($matches['height']),
                        );
                    }
                ),
                "Основной дисплей::::Разрешение д.: (?P<width>[\d]+)\*(?P<height>[\d]+)" => array(
                    "function" => function($matches, $lang) {
                        return array(
                            intval($matches['width']),
                            intval($matches['height']),
                        );
                    }
                ),
            ),
            13 => array(
                "Основной дисплей::::Размер, д.: (?P<size>[\d\.,]+)" => array(
                    "function" => function($matches, $lang) {
                        $matches['size'] = str_replace(",", ".", $matches['size']);
                        return doubleval($matches['size']);
                    }
                ),
                "Дисплей::::Размер, д.: (?P<size>[\d\.,]+)" => array(
                    "function" => function($matches, $lang) {
                        $matches['size'] = str_replace(",", ".", $matches['size']);
                        return doubleval($matches['size']);
                    }
                ),
            ),
            14 => array(
                "Операционная система::::Версия: (?P<os>.*)" => array(
                    "function" => function($matches, $lang) {
                        return trim($matches['os']);
                    }
                ),
            ),
            18 => array(
                "Передача данных::::Версия WiFi: 802.11 (?P<standarts>[\w/]+)" => array(
                    "function" => function($matches, $lang) {
                        $standarts = array();
                        $std = explode("/", trim($matches['standarts']));
                        foreach ($std as $s) {
                            if (!$s)
                                continue;
                            $standarts[] = "802.11 " . trim($s);
                        }
                        return $standarts;
                    }
                ),
                "Передача данных::::Wi-Fi: 802.11 (?P<standarts>[\w/]+)" => array(
                    "function" => function($matches, $lang) {
                        $standarts = array();
                        $std = explode("/", trim($matches['standarts']));
                        foreach ($std as $s) {
                            if (!$s)
                                continue;
                            $standarts[] = "802.11 " . trim($s);
                        }
                        return $standarts;
                    }
                ),
            ),
            19 => array(
                "Передача данных::::Версия Bluetooth: (?P<bluetooth>.*)" => array(
                    "function" => function($matches, $lang) {
                        return trim("Bluetooth " . $matches['bluetooth']);
                    }
                ),
                "Передача данных::::Bluetooth: (?P<bluetooth>.*)" => array(
                    "function" => function($matches, $lang) {
                        return trim($matches['bluetooth']);
                    }
                )
            ),
        );
    }
}
