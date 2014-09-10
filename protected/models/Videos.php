<?php
/**
 * Видео для товаров
 */
class Videos extends CActiveRecord
{
    private $_templates = array(
        1=>'<iframe width="540" height="315" src="//www.youtube.com/embed/%s?rel=0" frameborder="0" allowfullscreen></iframe>',
    );
    
    const TYPE_YOUTUBE = 1;
    
    public $types = array(
        1=>"Youtube",
    );
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{videos}}";
    }
    
    public function relations() 
    {
        return array(
            "rating"=>array(self::HAS_ONE, "RatingsVideos", "video", 
                "select"=>"AVG(rating.value) as value",
            )
        );
    }
    
    public function attributeLabels()
    {
        return array();
    }
    
    public function getTemplate($type = null, $link = null)
    {
        if ($link == null)
            $link = $this->link;
        
        if ($type == null)
            $type = $this->type;
        return sprintf($this->_templates[$type], $link);
    }
    
    public function getYoutube($limit, $query, $brand, $name)
    {
        $videos = array();
        require_once Yii::app()->basePath.'/extensions/google-api-php-client/src/Google_Client.php';
        require_once Yii::app()->basePath.'/extensions/google-api-php-client/src/contrib/Google_YouTubeService.php';
        $client = new Google_Client();
        $client->setDeveloperKey("AIzaSyCm5k_ScE8R_WiSyEBOc3xWGM9oXFg2RRI");
        $youtube = new Google_YoutubeService($client);
        try
        {
            $searchResponse = $youtube->search->listSearch('id', array(
                'q' => sprintf($query, $brand, $name),
                'maxResults' => $limit,
                'regionCode' => (Yii::app()->language == 'ru') ? 'ru' : 'us',
            ));
        } catch (Exception $ex) {
            return array();
        }

        if (isset($searchResponse['items']) && !empty($searchResponse['items']))
        {
            foreach ($searchResponse['items'] as $video)
            {
                if (isset($video['id']['videoId']))
                    $videos[] = $video['id']['videoId'];
            }
        }
        return $videos;
    }
}

