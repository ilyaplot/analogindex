<?php
class GsmArenaParser
{    
    public $content;
    public $curl;
    public $helper;
    
    public function __construct() {
        $this->curl = new CurlLayer();
        $this->helper = new GsmArena();
    }

    public function getSourceName()
    {
        return "gsmarena";
    }

    /**
     * Список производителей
     * @todo Проверки на выполнение
     * @return type
     */
    public function parseManufacturers()
    {
        $result = $this->curl->getContent("http://m.gsmarena.com/makers.php3");
        $pattern = "~<li><a href=\"(?P<link>[\.\-\w\d&]+)\"><img src=\"(?P<logo>[:/\.\w\d]+)\"><strong>(?P<name>[\w\d\s&]+)</strong></a></li>~u";
        preg_match_all($pattern, $result['content'], $matches, PREG_SET_ORDER);
        return $matches;
    }
    
    /**
     * Возвращает количество страниц из пагинатора
     * @param type $link
     * @return type
     */
    public function getPaginstionMax($link)
    {
        $result = $this->curl->getContent($link);
        $pattern = "~<a href=\"(?P<link>[\.\-\w\d&]+)p(?P<max>\w+)\.php\" id=\"last-button\">Last</a>~u";
        preg_match($pattern, $result['content'], $matches);
        return isset($matches['max']) ? array(intval($matches['max']), $matches['link']) : false;
    }
    
    public function getPDALinks($link, $manufacturer = null)
    {
        $return = array();
        $result = $this->curl->getContent($link);
        $pattern = "~<a href=\"(?P<link>[\.\-\w\d&]+)\"><img src=\"(?P<img>[:/\-\.\d\w\&]+)\"><strong>(?P<name>[\w\d\s&\(\)\-\.]+)</strong></a>~u";
        preg_match_all($pattern, $result['content'], $matches, PREG_SET_ORDER);
        foreach ($matches as $item)
        {
            $return[] = array(
                'link'=>$item['link'],
                'name'=>$item['name'],
            );
        }
        return $return;
    }
    
    public function parseMain($item, $manufacturer)
    {
        if (!isset($item['images']))
            $item['images'] = array();
        $patternImagesPage = "~<a class=\"left grid_8\" href=(?P<link>[\w\d\-\.&]{2,}\.php)><img~u";
        $patternImages = "~<p align=center><img src=\"(?P<link>[\w\d\-\./:]{1,})\" border=0 alt=\"[\w\d\s&\-\(\)\.]{1,}\"></p>~u";
        echo $item['link'].PHP_EOL;
        $content = $this->curl->getContent($item['link']);
        if ($content['code'] !== 200)
            throw new Exception ("Error code {$content['code']} in {$item['link']}.");
        $content = $content['content'];
        // Есть ссылка на картинки
        if (preg_match($patternImagesPage, $content, $matches))
        {
            $this->curl->wait();
            $link = "http://m.gsmarena.com/".$matches['link'];
            $imgContent = $this->curl->getContent($link);
            if ($imgContent['code'] !== 200)
                throw new Exception ("Error code {$imgContent['code']} in {$link}.");
            $imgContent = $imgContent['content'];
            if (!preg_match_all($patternImages, $imgContent, $matches, PREG_SET_ORDER))
            {
                throw new Exception("Не могу спарсить изображения {$link}");
            }
            foreach ($matches as $image)
            {
                $image = $image['link'];
                $link = basename(str_replace("://", "/", $image));
                $exp = explode(".", $link);
                $ext = end($exp);
                $filename = $this->curl->downloadFile($image, $ext);
                $item['images'][$image] = $filename;
            }
        }
        
        $return = array(
            'manufacturer'=>$manufacturer,
            'name'=>$item['name'],
            'images'=>$item['images'],
            'characteristics'=>  $this->prepeareCharacteristics($this->parseCharacteristics($content)),
        );
        return $return;
    }

    public function prepeareCharacteristics($characteristics)
    {
        $result = array();
        $merge = array(
            'Телефон'=>false,
        );
        foreach($characteristics as $key=>&$characteristic)
        {
            
            $characteristic['category'] = ucfirst(trim($characteristic['category']));
            $characteristic['name'] = ucfirst(trim($characteristic['name']));
            if (!$tmp = $this->helper->prepeare(
                    $characteristic['category']."_".$characteristic['name'], 
                    $characteristic['value']
            ))
            {
                unset($characteristic);
                unset($characteristics[$key]);
            } elseif (isset($tmp[2])) {
                list($characteristic['name'], $characteristic['value']) = $tmp[0][0];
                $characteristics[] = array(
                    'name'=>$tmp[0][1][0],
                    'value'=>$tmp[0][1][1],
                );
            } else {
                if ($tmp[0] == "Телефон")
                {
                    if ($merge['Телефон'] === false)
                    {
                        $characteristic['name'] = $tmp[0];
                        $characteristic['value'] = $tmp[1];
                        $merge[$tmp[0]] = $key;
                        var_dump($merge);
                    } else {
                        $characteristics[$merge[$tmp[0]]]['value'] = array_merge($tmp[1], $characteristics[$merge[$tmp[0]]]['value']);
                        unset($characteristic);
                        unset($characteristics[$key]);
                    }
                } else {
                    list($characteristic['name'], $characteristic['value']) = $tmp;
                }
            }
            
        }
        return $characteristics;
            
    }
    
    public function parseCharacteristics($content)
    {
        $content = str_replace("\n", " ", $content);
        $content = preg_replace("~\s{2,}~u", " ", $content);
        $content = str_replace("<table cellspacing=\"0\">", "\n<table cellspacing=\"0\">", $content);
        $patternCategory = "~<table cellspacing=\"0\">[\s\t\r\n]{1,}<tbody><tr><th scope=\"col\" colspan=\"2\">(?P<category>[\w\d\s\-\.;&]+)</th></tr>(?P<body>.{1,})</tbody></table>~iu";
        $patternCharacteristic = "~<td class=\"ttl\">(?P<name>.*)</td>.*<td class=\"nfo\">(?P<value>.*)</td>~u";
        $items = array();
        if (preg_match_all($patternCategory, $content, $matches, PREG_SET_ORDER))
        {
            foreach ($matches as &$table)
            {
                $table['body'] = preg_replace("~</tr>\s*<tr>~", "</tr>\n<tr>", $table['body']);
                $table['body'] = preg_replace("~\r~", " ", $table['body']);
                if (preg_match_all($patternCharacteristic, $table['body'], $chmatches, PREG_SET_ORDER))
                {
                    foreach ($chmatches as &$item)
                    {
                        if ($item['name'] == '&nbsp;')
                            $item['name'] = $table['category'];
                        $table['category'] = trim($table['category']);
                        $items[] = array(
                            'category' => $table['category'],
                            'name' => trim(strip_tags($item['name'])),
                            'value' => trim($item['value']),
                        );
                    }
                } else {
                    echo "Не могу спрасить {$table['body']}".PHP_EOL;
                    continue;
                }
            }
        } else {
            echo "Не могу спарсить таблицу категорий {$content}".PHP_EOL;
        }
        return $items;
    }
    
    private function _chOs($str)
    {
        return $str;
    }
    
    private function _chStd($str)
    {
        return $str;
    }
}