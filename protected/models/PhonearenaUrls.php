<?php

class PhonearenaUrls extends CActiveRecord
{
    public $fullurl;
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "phonearena_urls";
    }
    
    public function getDownloadList()
    {
        $criteria = new CDbCriteria();
        $criteria->select = "t.id, concat('http://www.phonearena.com', t.url) as fullurl, t.url";
        $criteria->condition = "url regexp '^/[a-z]+/[^/]+_id[0-9]+[/fullspecs]{0,10}$' and downloaded = 0";
        return self::model()->findAll($criteria);
    }
    
    public function getParseList()
    {
        $criteria = new CDbCriteria();
        $criteria->select = "t.id, concat('http://www.phonearena.com', t.url) as fullurl, t.content, t.photos, t.url";
        $criteria->condition = "t.downloaded = 1 and t.parsed = 0";
        return self::model()->findAll($criteria);
    }

    public function setParsed()
    {
        $this->parsed = 1;
        $this->save();
    }
    
    public function setContent($content)
    {
        $this->content = $content;
        $this->downloaded = 1;
        $this->save();
    }
    
    public function setPhotos($content)
    {
        $this->photos = $content;
        $this->save();
    }
}


