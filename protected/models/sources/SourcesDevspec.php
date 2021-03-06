<?php
class SourcesDevspec extends CActiveRecord
{
    /**
     * Папка для хранения файлов
     * @var string
     */
    public $path = "/inktomia/db/analogindex/sources/devspec/";
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{sources_devspec}}";
    }
    
    public function getFolder()
    {
        return ceil($this->getPrimaryKey()/10000);
    }
    
    public function getFilename()
    {
        $folder = $this->path.$this->getFolder()."/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }
        return $folder.md5($this->id).".file";
    }
    
    public function getContent()
    {
        if ($this->downloaded = 0) {
            return false;
        }
        return file_get_contents($this->getFilename());
    }

    public function writeContent($content) {
        return file_put_contents($this->getFilename(), $content);
    }


    public function rules() {
        return array(
            array("url", "url", 'allowEmpty'=>false),
            array("url, lang", "required"),
            array("url", "unique"),
        );
    }
    
    /**
     * 
     * @param type $type phones|tablets
     */
    public function getLastUrl()
    {
        $criteria = new CDbCriteria();
        $criteria->order = "id desc";
        $criteria->select = "url";
        $item = self::model()->find($criteria);
        return !empty($item->url) ? $item->url : null;
    }
    

    public function checkExists($url)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "url = :url";
        $criteria->params = ['url'=>$url];
        return self::model()->count($criteria);
    }
    
    
    
    public function beforeSave()
    {
        $this->updated = new CDbExpression("NOW()");
        if ($this->isNewRecord) {
            $this->created = new CDbExpression("NOW()");
        } else {
            $this->size = @filesize($this->getFilename());
        }
        return parent::beforeSave();
    }
}
