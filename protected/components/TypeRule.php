<?php
class TypeRule extends CBaseUrlRule
{
    public function createUrl($manager, $route, $params, $ampersand) 
    {
        $params['language'] = !empty($params['language']) ? $params['language'] : Language::getCurrentZone();
        if (empty($params['type']))
            return false;
        
        $url = "//analogindex.{$params['language']}/type/{$params['type']}";
        
        return $url.".html";
    }
    
    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo) {
        // url подходит под правило type
        if (preg_match("~type/(?P<type>[\d\w\-_]+)/(?P<url>[/\-\.\w\d]+)~", $pathInfo, $matches))
        {
            $_GET['type']=$matches['type'];
            
            $url = explode("/", $matches['url']);
            foreach ($this->getAssocParams($url) as $key=>$value)
            {
                $_GET[$key] = $value;
            }
            return "site/type";
        }
        
        if (preg_match("~type/(?P<type>[\d\w\-_]+)~", $pathInfo, $matches))
        {
            $_GET['type']=$matches['type'];
            return "site/type";
        }
        return false;  // не применяем данное правило
    }
    
    /**
     * Получаем ассо
     * @param type $params
     */
    protected function getAssocParams($params)
    {
        $assoc = array();
        for($i=0; $i<count($params); $i+=2) 
        {
            if(!isset($params[$i+1]))
                continue;
            $assoc[$params[$i]] = $params[$i+1];
        }
        return $assoc;
    }
}