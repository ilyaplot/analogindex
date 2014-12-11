<?php

class Controller extends CController
{

    public $pageDescription;
    public $pageKeywords;

    protected $keywordsArray = array();
    protected $descriptionArray = array();
    
    public function beforeAction($action)
    {

        Yii::app()->setLanguage(Language::getCurrentLang());

        if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
            header('X-UA-Compatible: IE=edge,chrome=1');

        $this->setPageTitle("Analogindex");

        $cs = Yii::app()->getClientScript();

        $cs->registerCoreScript('jquery');


        return parent::beforeAction($action);
    }
    
    
    public function addKeywords($keywords)
    {
        if (empty($keywords) || !is_array($keywords))
            return;
        
        $this->keywordsArray = array_merge($this->keywordsArray, $keywords);
        $this->keywordsArray = array_unique($this->keywordsArray);
        $this->pageKeywords = mb_substr(implode(", ", $this->keywordsArray), 0 ,250, 'UTF-8');
    }
    
    public function addKeyword($keyword)
    {
        if (empty($keyword))
            return;
        
        $this->keywordsArray[] = trim(strip_tags($keyword));
        $this->keywordsArray = array_unique($this->keywordsArray);
        $this->pageKeywords = implode(", ", $this->keywordsArray);
        $this->pageKeywords = mb_substr(preg_replace("/[^\w \.,!\?]/isu", '', $this->pageKeywords), 0 ,250, 'UTF-8');
    }

    
    public function addDescription($decription)
    {
        if (empty($decription))
            return;
        
        $this->descriptionArray[] = trim(strip_tags($decription));
        $this->descriptionArray = array_unique($this->descriptionArray);
        $this->pageDescription = preg_replace("/[^\w \.,!\?]/isu", '', mb_substr(implode(" ", $this->descriptionArray), 0 ,250, 'UTF-8'));
    }
}
