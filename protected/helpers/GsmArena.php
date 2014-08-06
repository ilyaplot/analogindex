<?php
class GsmArena
{
    public $functions = array(
        'display_size'=>'display_size',
        'body_dimensions'=>'body_dimensions',
        'general_2g network'=>'_2g_network',
        'general_3g network'=>'_2g_network',
        'general_4g network'=>'_2g_network',
        'general_announced'=>'general_announced',
        'body_weight'=>'body_weight',
        'features_os'=>'os',
        'features_chipset'=>'chipset',
        'camera_primary'=>'camera_primary',
        'camera_secondary'=>'camera_secondary',
    );
    
    public function prepeare($name, $value)
    {
        echo $name." - ".$value.PHP_EOL;
        if (isset($this->functions[strtolower($name)]))
            return call_user_func(array($this, $this->functions[strtolower($name)]), $value);
        return false;
    }
    

    public function display_size($value)
    {
        $name = "Разрешение экрана";
        //128 x 160 pixels, 1.8 inches (~114 ppi pixel density)
        
        $value = trim($value);
        $pattern = "~(?P<w>\d+)\sx\s(?P<h>\d+)~u";
        if (preg_match($pattern, $value, $matches))
        {
            return array($name, array($matches['w'], $matches['h']));
        }
        echo $value.PHP_EOL;
        return false;
    }
    
    public function body_dimensions($value)
    {
        $name = "Габариты";
        //109 x 56 x 18 mm (4.29 x 2.20 x 0.71 in)
        
        $value = trim($value);
        $pattern = "~(?P<x>[\d\.]+)\sx\s(?P<y>[\d\.]+)\sx\s(?P<z>[\d\.]+)\smm~u";
        if (preg_match($pattern, $value, $matches))
        {
            return array($name, array($matches['x'], $matches['y'], $matches['z']));
        }
        echo $value.PHP_EOL;
        return false;
    }
    
    public function _2g_network($value)
    {
        $name = "Телефон";
        $value = trim($value);
        
        if ($value == 'N/A')
        {
            return false;
        }
        
        if (preg_match("~/~u", $value))
        {
            $value = explode("/", $value);
            $type = '';
            if (preg_match("~(?P<type>\w+)\s~", trim($value[0]), $matches))
            {
                $type = $matches['type'];
            }
            foreach ($value as $key=>&$item)
            {
                if ($key == 0)
                {
                    $item = trim($item);
                    continue;
                }
                $item = $type." ".trim($item);
            }
        }
        
        if (!is_array($value))
            $value = array($value);
        
        return array($name,$value);
    }
    
    public function general_announced($value)
    {
        return array("Год выпуска", $value);
    }
    
    public function body_weight($value)
    {
        $name = 'Вес';
        if (preg_match("~(?P<weight>[\d\.]+)\sg~u", $value, $matches))
        {
            $value = $matches['weight'];
        }
        return array($name, $value);
    }
    
    public function os($value)
    {
        return array("Операционная система", $value);
    }
    
    public function chipset($value)
    {
        return array("Тип процессора", $value);
    }
    
    public function camera_primary($value)
    {
        //13 MP, 4160 x 3120 pixels, autofocus, LED ring flash
        $pattern = "~(?P<pixels>[\d\.]+)\sMP~";
        if (preg_match($pattern, $value, $matches))
        {
            return array('Камера сзади', $matches['pixels']*1000);
        }
        return false;
    }
    
    public function camera_secondary($value)
    {
        //Yes, 2 MP, 1080p@30fps
        $pattern = "~Yes, (?P<pixels>[\d\.]+)\sMP~u";
        if (preg_match($pattern, $value, $matches))
        {
            return array('Камера спереди', $matches['pixels']);
        }
        return false;
    }
    
    public function wifi($value)
    {
        //Data_WLAN - Wi-Fi 802.11 b/g/n
        $pattern = "~Wi-Fi 802\.11 (?P<modes>[\w/]+)~u";
        if (preg_match($pattern, $value, $matches))
        {
            $modes = explode("/", $matches['modes']);
            foreach ($modes as &$mode)
            {
                if (!strlen($mode))
                    unset($mode);
                $mode = strtolower($mode);
            }
            return array('Wi-Fi', $modes);
        }
        return false;
    }
    
    public function memory($value)
    {
        //Memory_Internal - 64 MB storage, 16 MB RAM
        $pattern = "~(?P<storage>\d+)\s(?P<multiple>\w+)\sstorare,\s(?P<ram>\d+)\s(?P<rammultiple>\w+)\sRAM~u";
        if (preg_match($pattern, $value, $matches))
        {
            $storage = 0;
            $ram = 0;
            switch($matches['multiple'])
            {
                case "GB":
                    $storage = $matches['storage']*1024;
                break;
                case "MB":
                    $storage = $matches['storage'];
                break;
            }
            
            switch($matches['multiple'])
            {
                case "GB":
                    $ram = $matches['ram']*1024;
                break;
                case "MB":
                    $ram = $matches['ram'];
                break;
            }
            return array(
                array(
                    array('Встроенная память', $storage),
                    array('Оперативная память', $ram),
                ),
                null,
                true
             );
        }
        return false;
    }
}