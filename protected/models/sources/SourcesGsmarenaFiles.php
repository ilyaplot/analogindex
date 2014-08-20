<?php
class SourcesGsmarenaFiles extends Files
{
    /**
     * Папка для хранения файлов
     * @var string
     */
    public $path = "/inktomia/db/analogindex/sources/gsmarena";
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{sources_gsmarena_files}}";
    }
}