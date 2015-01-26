<?php
class SourcesIrecommend extends CActiveRecord
{
    /**
     * Папка для хранения файлов
     * @var string
     */
    public $path = "/inktomia/db/analogindex/sources/irecommend/";
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{sources_irecommend}}";
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

    public function rules() {
        return array(
            array("url", "url", 'allowEmpty'=>false),
            array("url", "required"),
            array("url", "unique"),
        );
    }
    
    /**
     * 
     * @param type $type phones|tablets
     */
    public function getLastUrl($type)
    {
        $criteria = new CDbCriteria();
        $criteria->order = "created desc";
        $criteria->select = "url";
        $criteria->condition = "type = :type";
        $criteria->params = ['type'=>$type];
        $item = self::model()->find($criteria);
        return !empty($item->url) ? $item->url : null;
    }
    
    public function beforeSave()
    {
        $this->updated = new CDbExpression("NOW()");
        if ($this->isNewRecord) {
            $this->created = new CDbExpression("NOW()");
        }
        return parent::beforeSave();
    }
}
