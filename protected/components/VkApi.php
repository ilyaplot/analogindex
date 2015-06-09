<?php
class VkApi extends CComponent
{
    protected $_error;
    
    protected $_clientID;
    protected $_clientSecret;
    protected $_accessToken;

    
    public function getError()
    {
        return !empty($this->_error) ? $this->_error : false;
    }
    
    public function __construct($clientId, $clientSecret, $accessToken = null)
    {
        $this->_clientID = $clientId;
        $this->_clientSecret = $clientSecret;
        $this->_accessToken = $accessToken;
	}
    
    public function authorize($scope = 'offline,notify,friends,photos,audio,video,wall')
    {
        echo PHP_EOL.'http://api.vkontakte.ru/oauth/authorize?'.  http_build_query(array(
            'client_id' => $this->_clientID,
            'client_secret' => $this->_clientSecret,
            'scope' => $scope,
            'redirect_uri' => 'https://oauth.vk.com/blank.html',
            'display' => 'page',
            'v' => '5.33',
            'response_type' => 'token'
        )).PHP_EOL;
        //8b17eb5b67e4534cf64cc7ea70a8b488621d1bc38d48db89b77c9c9fa49499a9606d8aee6d34d72feb5d0 //67186202
    }
    
    
    public function run($command, $params = array(), $throwException = true)
    {
        
        $this->_error = null;
        $url = 'https://api.vkontakte.ru/method/'.$command;
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new CException("Url '{$url}' is not valid.");
        }
        
        $params['access_token'] = $this->_accessToken;
        $params['version'] = '5.33';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_USERAGENT, 'vkPhpSdk-0.2.1');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        if(!empty($params) && is_array($params)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, null, '&'));
            curl_setopt($ch, CURLOPT_POST, true);
        }
        
        $result = curl_exec($ch);

        if ($result === false) {
            throw new CException('Curl error #'.curl_errno($ch).': '.curl_error($ch));
        }
        
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        $result = json_decode($result);
        
        if (isset($result->error)) {
            $this->_error = $result->error;
            $this->_error->params = (object) $params;
            
            if ($throwException) {
                var_dump($result);
                throw new CException('Vk Api error #'.$this->_error->error_code.': '.$this->_error->error_msg);
            }
            
            return false;
        }
        
        return (object) $result->response;
    }
    
    
    public function upload($url, $params = array())
    {
        sleep(1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        //curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return json_decode($response);
    }
    
}