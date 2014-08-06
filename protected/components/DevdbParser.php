<?php
class DevdbParser
{
    public $content;
    public $d = array(
        'manufacturer'=>'',     // Производитель
        'name'=>'',             // Наименование
        'model'=>'',            // Модель
        
    );
    
    public function __construct($content) {
        // Devdb в 1251 брр...
        $this->content = iconv('windows-1251', 'UTF-8', $content);
    }
    
    public function getSourceName()
    {
        return "devdb";
    }
}