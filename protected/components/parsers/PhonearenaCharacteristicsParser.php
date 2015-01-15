<?php

class PhonearenaCharacteristicsParser extends CharacteristicsParser
{

    public function getRules()
    {

        return [
            //Вес
            3 => [
                "Design:::Weight::::[\d\.]+ oz  \((?P<weight>[\d\.]+) g\)" => [
                    "function" => function($matches, $lang) {
                        return doubleval($matches['weight']);
                    }
                ],
            ],
            // Габариты
            4 => [
                "Design:::Dimensions::::[\d\.]+ x [\d\.]+ x [\d\.]+ inches  \((?P<h>[\d\.]+) x (?P<w>[\d\.]+) x (?P<l>[\d\.]+) mm\)" => [
                    "function" => function($matches, $lang) {
                        return [
                            doubleval($matches['h']), 
                            doubleval($matches['w']),
                            doubleval($matches['l']),
                        ];
                    },
                ]
            ],
            // Количество ядер процессора
            5 => [
                "Hardware:::Processor::::Quad core.*" => [
                    "function" => function($matches, $lang) {
                        return 4;
                    },
                ],
                "Hardware:::Processor::::Dual core.*" => [
                    "function" => function($matches, $lang) {
                        return 2;
                    },
                ],
                "Hardware:::Processor::::Single core.*" => [
                    "function" => function($matches, $lang) {
                        return 1;
                    },
                ],
            ],
            // Частота процессора
            6 => [
                "Hardware:::Processor::::.*[^\d](?P<freq>[\d\.]+ MHz)" => [
                    "function" => function($matches, $lang) {
                        return doubleval($matches['freq'] * 1000 * 1000);
                    },
                ],
                "Hardware:::Processor::::.*[^\d](?P<freq>[\d\.]+ GHz)" => [
                    "function" => function($matches, $lang) {
                        return doubleval($matches['freq'] * 1000 * 1000 * 1000);
                    },
                ],
            ],
            // Модель процессора 
            7 => [
                "Hardware:::System chip::::(?P<chipset>.*)" => [
                    "function" => function($matches, $lang) {
                        return trim($matches['chipset']);
                    }
                ]
            ],
            // GPU
            31 => [
                "Hardware:::Graphics processor::::(?P<gpu>.*)" => [
                    "function" => function($matches, $lang) {
                        return trim($matches['gpu']);
                    }
                ]
            ],
            // Оперативка
            8 => [
                "Hardware:::System memory::::(?P<memory>\d+) GB RAM" => [
                    "function" => function($matches, $lang) {
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ],
                "Hardware:::System memory::::(?P<memory>\d+) MB RAM" => [
                    "function" => function($matches, $lang) {
                        return doubleval($matches['memory'] * 1024 * 1024);
                    }
                ]
            ],
            // Внутренняя память
            9 => [
                "Hardware:::Built-in storage::::(?P<memory>[\d\.]+) GB" => [
                    "function" => function($matches, $lang) {
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ],
                "Hardware:::Built-in storage::::(?P<memory>[\d\.]+) MB" => [
                    "function" => function($matches, $lang) {
                        return doubleval($matches['memory'] * 1024 * 1024);
                    }
                ]
            ],
            // Максимальный объем флешки
            10 => [
                "Hardware:::Storage expansion::::.*up to (?P<memory>[\d\.]+) GB" => [
                    "function" => function($matches, $lang) {
                        return doubleval($matches['memory'] * 1024 * 1024 * 1024);
                    }
                ]
            ],
            // Тип экрана
            11 => [
                "Display:::Technology::::(?P<screen>.*)" => [
                    "function" => function($matches, $lang) {
                        return trim($matches['screen']);
                    }
                ]
            ],
            // Разрешение экрана
            12 => [
                "Display:::Resolution::::(?P<width>\d+) x  (?P<height>\d+) pixels" => [
                    "function" => function($matches, $lang) {
                        return [
                            intval($matches['width']),
                            intval($matches['height']),
                        ];
                    }
                ]
            ],
            // Диагональ экрана
            13 => [
                "Display:::Physical size::::(?P<size>[\d\.]+) inches" => [
                    "function" => function($matches, $lang) {
                        return doubleval($matches['size']);
                    }
                ]
            ],
            // OS
            14 => [
                "Design:::OS::::(?P<os>.*)" => [
                    "function" => function($matches, $lang) {
                        return trim($matches['os']);
                    }
                ],
            ],
            // 2g (GSM)
            32 => [
                "Technology:::GSM::::(?P<standarts>[\d, /]+) MHz" => [
                    "function" => function($matches, $lang) {
                        $standarts = explode(", ", $matches['standarts']);
                        foreach ($standarts as &$standart) {
                            $standart = "GSM ".trim($standart);
                            if (empty($standart))
                                unset($standart);
                        }
                        return $standarts;
                    },
                    "nobreak" => true,
                    "merge" => true,
                    "unique" => true,
                    "sort" => true,
                ],
                "Technology:::CDMA::::(?P<standarts>[\d, /]+) MHz" => [
                    "function" => function($matches, $lang) {
                        $standarts = explode(", ", $matches['standarts']);
                        foreach ($standarts as &$standart) {
                            $standart = "CDMA ".trim($standart);
                            if (empty($standart))
                                unset($standart);
                        }
                        return $standarts;
                    },
                    "nobreak" => true,
                    "merge" => true,
                    "unique" => true,
                    "sort" => true,
                ]
            ],
            // 3g
            33 => [
                "Technology:::UMTS::::(?P<standarts>[\d, /]+) MHz" => [
                    "function" => function($matches, $lang) {
                        $standarts = explode(", ", $matches['standarts']);
                        foreach ($standarts as &$standart) {
                            $standart = "UMTS ".trim($standart);
                            if (empty($standart))
                                unset($standart);
                        }
                        return $standarts;
                    },
                ],
            ],
            // 4g FDD LTE
            34 => [
                "Technology:::FDD LTE::::(?P<standarts>[\w\(\), /]+) MHz" => [
                    "function" => function($matches, $lang) {
                        $standarts = explode(", ", preg_replace("/ \(band [\d]+\)/isu", '', $matches['standarts']));
                        foreach ($standarts as &$standart) {
                            $standart = "LTE ".trim($standart);
                            if (empty($standart))
                                unset($standart);
                        }
                        return $standarts;
                    },
                ],
            ],
            // Цвета
            36 => [
                "Design:::Colors::::(?P<colors>.*)" => [
                    "function" => function($matches, $lang) {
                        $dbColors = [];
                        $colors = explode(",", $matches['colors']);
                        $colors = array_map(function($item) {return trim($item);}, $colors);
                        foreach ($colors as $color) {
                            $criteria = new CDbCriteria();
                            $criteria->select = "id";
                            $criteria->condition = "en like :color";
                            $criteria->params = ["color"=>$color];
                            $criteria->limit = 1;
                            $dbColor = Colors::model()->find($criteria);
                            if (!empty($dbColor->id)) {
                                $dbColors[] = $dbColor->id;
                            }
                        }
                        $dbColors = array_unique($dbColors);
                       
                        return $dbColors;
                    }
                ]
            ]
        ];
    }
}