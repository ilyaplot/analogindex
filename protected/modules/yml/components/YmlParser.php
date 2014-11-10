<?php
/**
 * @example 
 * // Создаем экземпляр парсера
 * $parser = new YmlParser("/tmp/list.yml");
 * // Вешаем обработчик на элемент прайса
 * // Параметры функции: 
 ** 1 - Имя элемента через точку от родителя
 ** 2 - Функция, которая должна выполняться при срабатывании ивента
 ** 3 - Аттрибут collect. Если true, собирает вложенне элементы, иначе не собирает
 * $parser->registerEvent("list.adv", function($elem, $items){var_dump([$elem, $items]);}, true);
 * // Запускаем парсер 
 * $parser->run();
 */
class YmlParser
{

    /**
     * Файл для парсинга
     * @var str
     */
    protected $filename;

    /**
     * Переменная для накомпления данных элемента
     * @var str
     */
    protected $lastData;

    /**
     * Глубина вложенности
     * @var int 
     */
    protected $depth = 0;

    /**
     * Текущие ключи
     * @var array
     */
    protected $keys = [];

    /**
     * Атрибуты текущего элемента
     * @var array 
     */
    protected $attrs = [];

    /**
     * Временный массив вложеных элементов
     * @var array 
     */
    protected $collections = [];


    /**
     * Массив событий
     * @var type 
     */
    protected $events = [];

    /**
     * 
     * @param str $filename
     */
    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            echo "File not found" . PHP_EOL;
            return;
        }

        $this->filename = $filename;
    }

    public function registerEvent($name, $function, $collect = false)
    {
        $this->events[$name] = [
            "collect" => $collect,
            "function" => $function,
            "elem" => (object) [],
            "items" => [],
        ];
    }

    public function run()
    {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler($parser, array($this, "startElement"), array($this, "endElement"));
        xml_set_character_data_handler($parser, array($this, "dataElement"));

        if (!($fp = fopen($this->filename, "r"))) {
            throw new CException("File {$this->filename} not readable.");
            return false;
        }

        while ($data = fgets($fp)) {
            if (!xml_parse($parser, $data, feof($fp))) {
                echo "Xml parsing error: ";
                echo xml_error_string(xml_get_error_code($parser));
                echo " at line " . xml_get_current_line_number($parser);
                break;
            }
        }

        xml_parser_free($parser);
    }

    /**
     * Выполняется при открытии элемента
     * @param type $parser
     * @param type $name
     * @param type $attrs
     */
    public function startElement($parser, $name, $attrs)
    {
        $this->depth++;
        $this->attrs[$this->depth] = $attrs;
        $this->keys[$this->depth] = $name;
    }

    /**
     * Пишет данные текущего элемента в переменную
     * @param type $parser
     * @param type $data
     */
    public function dataElement($parser, $data)
    {
        if (!empty($data)) {
            $this->lastData .= $data;
        }
    }

    /**
     * Выполняется при закрытии элемента
     * @param type $parser
     * @param type $name
     */
    public function endElement($parser, $name)
    {
        // Форматирование yml добавляет к началу данных перевод строки и пробелы
        $this->lastData = preg_replace("/^\n\s*(.*)/isu", "$1", $this->lastData);
        
        $elem = (object) [
            "name" => $name,
            "data" => $this->lastData,
            "attrs" => $this->attrs[$this->depth],
        ];
        
        $eventKey = implode(".", $this->keys);
        
        foreach ($this->events as $key=>&$event) {
            $pattern = preg_quote($key);
            
            if ($event['collect'] && preg_match("/^{$key}\.[^\.]{1,}/isu", $eventKey)) {
                //echo "/^{$key}\.[^\.]{1,}/isu"."-".$eventKey.PHP_EOL;
                $event['items'][$name] = $elem;
            }
        }
        
        if (isset($this->events[$eventKey])) {

            $this->events[$eventKey]['elem'] = $elem;
            $function = $this->events[$eventKey]['function'];
            $function($elem, $this->events[$eventKey]['items']);
            
            $this->events[$eventKey]['elem'] = (object)[];
            $this->events[$eventKey]['items'] = [];
        }

        unset($this->keys[$this->depth], $this->attrs[$this->depth]);
        $this->depth--;
        $this->lastData = '';
    }

}
