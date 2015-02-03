<?php
/**
 * Производители
 */
class Brands extends CActiveRecord
{
    // Параметр для формы поиска
    public $checked = false;
    public $path = "/inktomia/db/analogindex/brands_images";
        
    public static function model($className = __CLASS__) 
    {
        return parent::model($className);
    }
    
    public function rules()
    {
        return [
            ['name, link', 'length', 'min'=>2],
            ['name, link', 'unique', 'allowEmpty'=>false, 'caseSensitive'=>false],
        ];
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->link = Yii::app()->urlManager->translitUrl(str_replace("+"," plus",$this->name));
        }
        return parent::beforeSave();
    }

    public function tableName() 
    {
        return "{{brands}}";
    }
    
    public function relations()
    {
        return array(
            "goods"=>array(self::HAS_MANY, "Goods", "brand"),
            "description"=>array(self::HAS_ONE, "BrandsDescriptions", "brand", 
                "on"=>"lang = '".Yii::app()->language. "'",
            ),
            "synonims"=>array(self::HAS_MANY, "BrandsSynonims", "brand"),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "name"=>Yii::t("model", "Наименование"),
            "link"=>Yii::t("model", "Ссылка"),
            "logo"=>Yii::t("model", "Логотип"),
        );
    }
    
    public function getTypes()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "goods.brand = :brand";
        $criteria->params = array("brand"=>$this->id);
        $criteria->group = "t.id asc";
        $criteria->order = "name.name asc";
        $goods = GoodsTypes::model()->with(array(
            "goods"=>array(
                "joinType"=>"INNER JOIN",
                "select"=>false,
            ),
            "name"=>array(
                "joinType"=>"INNER JOIN",
            )
        ))->findAll($criteria);
        return $goods;
    }
    
    
    public function afterDelete()
    {
        $this->deleteFile();
        return parent::afterDelete();
    }
    
    /**
     * Записывает файл
     * @param string $content
     * @return boolean
     */
    public function putFile($content)
    {
        if (!$this->getPrimaryKey())
            return false;
        $filename = $this->getFilename($this->getPrimaryKey());
        try {
            return file_put_contents($filename, $content);
        } catch (Exception $ex) {
            return false;
        }
    }
    
    /**
     * Перемещает файл в хранилище
     * @param string $filename
     * @return boolean
     */
    public function setFile($filename)
    {
        $to = $this->getFilename();
        
        if (!$to)
            return false;
        
        if (rename($filename, $to)) {
            if (!$this->isImage()) {
                return false;
            }
            $this->logo_size = $this->getFilesize();
            $this->logo_mime_type = $this->getMimeType();
            $size = getimagesize($this->getFilename());
            if ($size[0] < 30 || $size[1] < 30) {
                //$this->delete();
                
                return false;
            }
            
            $this->logo_width = $size[0];
            $this->logo_height = $size[1];
            $this->logo = 1;
            $this->save();
            return true;
        }
        
        return false;
        //return rename($filename, $to);
    }
    
    /**
     * Удаляет файл
     */
    public function deleteFile()
    {
        return @unlink($this->getFilename($this->getPrimaryKey()));
    }
    
    /**
     * Проверяет наличие файла
     * @return boolean
     */
    public function fileExists()
    {
        $filename = $this->getFilename();
        return file_exists($filename) && is_file($filename);
    }
    
    /**
     * Отдает содержимое файла
     * @return mixed
     */
    public function getContent()
    {
        try {
            return @file_get_contents($this->getFilename());
        } catch (Exception $ex) {
            return false;
        }
    }
    
    /**
     * Возвращает полный путь к файлу
     * @return string
     */
    public function getFilename()
    {
        if (!$this->getPrimaryKey())
            return false;
        return $this->getPath($this->getPrimaryKey()) . md5($this->getPrimaryKey()) . ".file";
    }
    
    /**
     * Возвращает директорию по id
     * @return string
     */
    public function getPath()
    {
        if (!$this->getPrimaryKey())
            return false;
        $path = $this->path . DIRECTORY_SEPARATOR . $this->getSubdirectory() . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }
    
    public function getSubdirectory()
    {
        return ceil($this->getPrimaryKey() / 10000);
    }

    /**
     * Возвращает размер файла
     * @return boolean
     */
    public function getFilesize()
    {
        if (!$this->getPrimaryKey())
            return false;
        if ($this->fileExists())
            return filesize($this->getFilename());
        else
            return false;
    }

    /**
     * Возвращает mime type файла
     * @param string $filename
     * @return string
     */
    public function getMimeType($filename = null)
    {
        if ($filename == null) {
            if (!empty($this->logo_mime_type))
                return $this->logo_mime_type;
            $filename = $this->getFilename();
        }
        return mime_content_type($filename);
    }
    
    public function getExt() {
        return preg_replace("/image\/(.*)/isu", "$1", $this->getMimeType());
    }
    
    public function isImage() {
        echo $this->getMimeType().PHP_EOL;
        return preg_match("/^image\/.*$/isu", $this->getMimeType());
    }
    
    public function getLogoUrl()
    {
        if (!$this->logo) {
            return false;
        }
        return Yii::app()->createAbsoluteUrl("files/brandsimage", [
            'language' => Language::getCurrentZone(),
            'id'=>  $this->id,
            'name'=>  $this->name.".".$this->getExt(),
        ]);
    }
    
}