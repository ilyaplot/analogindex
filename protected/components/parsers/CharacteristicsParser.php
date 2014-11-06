<?php

/**
 * Класс для парсинга характеристик
 */
class CharacteristicsParser
{

    /**
     * Правила парсинга
     * @var array
     */
    protected $_rules = array();

    /**
     * Специально подготовленные строки для парсинга
     * @var array
     */
    protected $_lines = array();

    /**
     * Языки для перевода характеристик
     * @var array
     */
    protected $_langs = array(
        'ru', 'en'
    );

    public function __construct($lines)
    {
        $this->_lines = $lines;
        $this->_rules = $this->getRules();
    }

    public function getRules()
    {
        return array();
    }

    public function run()
    {
        $result = array();
        // Перебираем строки для парсинга
        foreach ($this->_lines as $line) {
            // Если правило уже сработало, не обрабатываем следующие регулярки для этого правила
            $success = false;
            // Перебираем правила
            foreach ($this->_rules as $id => $patterns) {
                // Если прошлое правило было завершено, обнуляем переменную
                if ($success) {
                    $success = false;
                }
                foreach ($patterns as $pattern => $params) {

                    // Если завершение, не перебираем языки
                    if ($success && !isset($params['nobreak'])) {
                        //echo "CONTINUE {$pattern}".PHP_EOL;
                        continue;
                    }
                    // Перебор языков характеристик
                    foreach ($this->_langs as $lang) {
                        // Выполняем регулярку
                        if (preg_match("~{$pattern}~iu", $line, $matches)) {
                            //echo "PATTERN RUN {$pattern} {$lang}".PHP_EOL;
                            // Значение возвращает функция из правила
                            $value = $params['function']($matches, $lang);

                            // Если значение еще не было получено
                            if (!isset($result[$id . $lang])) {
                                // Создаем структуру характеристики для добавления
                                $resultItem[$id . $lang] = array(
                                    'id' => $id,
                                    'lang' => $lang,
                                    'values' => $value,
                                );
                            } else {
                                // Если резултат массив и нужно соединять результаты
                                if (is_array($resultItem[$id . $lang]['values']) && isset($params['merge'])) {
                                    $resultItem[$id . $lang]['values'] = array_merge($resultItem[$id . $lang]['values'], $value);
                                    if (isset($params['unique']))
                                        $resultItem[$id . $lang]['values'] = array_unique($resultItem[$id . $lang]['values'], SORT_STRING);
                                    
                                    if (isset($params['sort']))
                                        sort($resultItem[$id . $lang]['values'], SORT_NATURAL);
                                }
                            }
                            // Если получили результат, добавляем его к общему массиву
                            if ($resultItem[$id . $lang]) {
                                $result = array_merge($result, $resultItem);
                                if (!isset($params['nobreak']))
                                    $success = true;
                            }
                        }
                    }
                }
            }
        }
        // Отдаем массви результатов
        return $result;
    }

}
