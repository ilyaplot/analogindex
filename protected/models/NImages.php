<?php
class NImages extends CActiveRecord
{
    
    /**
     * Папка для хранения файлов
     * @var string
     */
    public $path = "/inktomia/db/analogindex/images";

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "new_images";
    }

    


    /*
     * id
     * name
     * mime_type
     * filesize
     * width
     * height
     * source_url
     * body_hash
     * alt
     * title
     */
}
