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
            ]
        ];
    }
}