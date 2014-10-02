<?php
class DevdbPdaParser extends DevdbParser
{
    public $characteristics = array(
        'Операционная система' => '_chOs', //Windows Phone 8.1
        'Емкость аккум' => '_chAcc',       //1830 (. (мА·ч)])
        'Габариты' => '_chSize',           //66.7 x 129.5 x 9.2  ( (мм)])
        'Вес' =>'_chDtc',                  //134 ( (г)])
        'Тактовая частота' => '_chFreq',   //1200 ( (МГц)])
        'Оперативная память' => '_chRam',  //512 ( (Мб)])
        'Встроенная память' => '_chSdMem',   //8 ( (Гб)])
        'ROM' => '_chSdMem',
        'Wi-Fi' => '_chWifi',              //802.11b,g,n
        'Разрешение экрана' => '_chScreen',//480 x 854 ( (px)])
        'Камера сзади' => '_chFrontCam',      //5 ( (Мп)])
        'Камера спереди' => '_chRearCam',
        'Телефон' => '_chPhone',        // GSM 1800, GSM 1900, GSM 850, GSM 900, UMTS 1900, UMTS 2100, UMTS 850
        'Другое' => '_chOther',         // G-Sensor, Датчик освещенности, Датчик приближения, Цифровой компас
        'Кнопки' => '_chList',
        'Карты памяти' => '_chList',
        'Разъемы' => '_chList',
        'Другие названия'=> '_chList',
    );
    
    public function parseList()
    {
        
        if (!preg_match("~<div class=\"top_nav_active\"><a href=\"/pda/\" title=\"Коммуникаторы и КПК\">Коммуникаторы</a></div>~", $this->content))
            return false;
    }
    
    public function parseMain()
    {
        $this->d['manufacturer'] = $this->parseManufacturer();
        $this->d['name'] = $this->parseName();
        $this->d['model'] = $this->parseModel();
        $this->d['characteristics'] = $this->parseCharacteristics();
        //echo count($this->d['characteristics']);
        return $this->d;
    }
    
    /**
     * Наименование товара
     * @return boolean
     */
    public function parseName()
    {
        $name = "~Модель: </td><td\sclass=[\"]{0,1}b[\"]{0,1}\swidth=\"65.\">(?P<name>[\d\w\s\-'&\+\(\)]{1,})\s</td>~u";
        if (!preg_match($name, $this->content, $matches))
            return false;
        return $matches['name'];
    }
    
    /**
     * Производитель
     * @return boolean
     */
    public function parseManufacturer()
    {
        $manufacturer = "~<span\sclass=\"c_brand\"><a\shref=\"http://devdb.ru/pda/\w+\">(?P<manufacturer>[\d\w\s\-'&\(\)]{1,})</a></span>~u";
        //echo $this->content;
        if (!preg_match($manufacturer, $this->content, $matches))
            return false;
        return $matches['manufacturer'];
        
    }
    
    /**
     * Код модели
     * @return boolean
     */
    public function parseModel()
    {
        $model = "~Другие\sназвания:\s</td><td\sclass=[\"]{0,1}b[\"]{0,1}\swidth=\"65%\">(?P<model>[\d\w\s\-'&\+]{1,})\s</td>~u";
        //echo $this->content;
        if (!preg_match($model, $this->content, $matches))
            return false;
        return $matches['model'];
    }
    
    public function parseCharacteristics()
    {
        $list = array();
        $characteristics = "~<tr\sclass=\"ct_[1|2]{1}\"><td\swidth=\"35%\">(?P<name>[·\d\w\s\-\(\)\.,]{1,}):(<br /><span class=\"annotation\">[\w\s,\.\d\(\)/]{1,}</span>|)\s</td><td\sclass=[\"]{0,1}b[\"]{0,1}\swidth=\"65%\">(?P<value>[\d\w\s\-\(\)\.,]{1,})\s</td></tr>~u";
        if (!preg_match_all($characteristics, $this->content, $matches, PREG_SET_ORDER))
            return false;
        foreach($matches as $item)
        {
            foreach ($this->characteristics as $pattern => $function)
            {
                if (preg_match("~{$pattern}~u", $item['name']))
                    $item = $this->$function($item);
            }
            /**
             * @todo Категория!
             */
            $list[$item['name']] = array(
                'value'=>$item['value'],
                'name'=>$item['name'],
                'category'=>'Общая',
            );
        }
        unset(
                $list['Производитель'], 
                $list['Модель']
        );
        return $list;
    }
    
    private function _chOs($item)
    {
        return $item;
    }
    
    private function _chAcc($item)
    {
        $item['name'] = str_replace("·", "#", $item['name']);
        $pattern = "~\((?<measure>\w+)\)~";
        $list = array(
            'мА#ч'=>1,
            'А#ч'=>1000,
        );
        if (preg_match($pattern, $item['name'], $matches))
        {
            if (!isset($list[$matches['measure']]))
            {
                return $item;
            }
            
            $item['value'] = $item['value']*$list[$matches['measure']];
            
        }
        $item['name'] = 'Емкость аккумулятора';
        return $item;
    }
    
    /**
     * @todo Дописать на регулярке
     * @param type $item
     * @return string
     */
    private function _chSize($item)
    {
        //66.7 x 129.5 x 9.2  ( (мм)])
        $item['value'] = explode(" x ", $item['value']);
            if (count($item['value']) < 2)
                $item['value'] = explode(" х ", $item['value'][0]);
        $item['name'] = 'Габариты';
        return $item;
    }
    
    private function _chDtc($item)
    {
        $item['name'] = 'Вес';
        return $item;
    }
    
    private function _chFreq($item)
    {
        $pattern = "~\((?<freq>.*)\)~";
        $list = array(
            'ГГц'=>1,
            'МГц'=>0.001,
        );
        if (preg_match($pattern, $item['name'], $matches))
        {
            if (!isset($list[$matches['freq']]))
            {
                echo "freq fail";
                return $item;
            }
            
            $item = array(
                'name'=>'Тактовая частота',
                'value'=>$item['value']*$list[$matches['freq']],
            );
            
        }
        return $item;
    }
    
    private function _chRam($item)
    {
        $item = $this->_chMem($item);
        $item['name'] = 'Оперативная память';
        return $item;
    }

    private function _chSdMem($item)
    {
        $item = $this->_chMem($item);
        $item['name'] = 'Встроенная память';
        return $item;
    }
    
    private function _chMem($item)
    {
        $pattern = "~\((?<measure>.*)\)~";
        $list = array(
            'Мб'=>1,
            'Гб'=>1024,
        );
        if (preg_match($pattern, $item['name'], $matches))
        {
            if (!isset($list[$matches['measure']]))
            {
                return $item;
            }
            
            $item['value'] = $item['value']*$list[$matches['measure']];
            
        }
        return $item;
    }
    
    private function _chWifi($item)
    {
        $item['value'] = preg_replace("~802\.11~", "", $item['value']);
        $item['value'] = explode(",", $item['value']);
        
        return $item;
    }
    
    private function _chScreen($item)
    {
        $item['value'] = explode(" x ", $item['value']);
        $item['name'] = 'Разрешение экрана';
        return $item;
    }
    
    private function _chFrontCam($item)
    {
        $item['name'] = 'Камера сзади';
        return $item;
    }
    
    private function _chRearCam($item)
    {
        $item['name'] = 'Камера спереди';
        return $item;
    }


    private function _chCam($item)
    {
        $item['name'] = 'Камера сзади';
        return $item;
    }
    
    private function _chPhone($item)
    {
        return $this->_chList($item);
    }
    
    private function _chOther($item)
    {
        return $this->_chList($item);
    }
    
    private function _chList($item)
    {
        $value = explode(", ", $item['value']);
        if (count($value) > 1)
            $item['value'] = $value;
        return $item;
    }
}