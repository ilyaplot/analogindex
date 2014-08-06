<?php
class PdadbParser
{
    public $content;
    public $curl;
    
    
    public function __construct() 
    {
        $this->curl = new CurlLayer();
    }
    
    public function search($name)
    {
        $pattern = "~<td align=\"center\" valign=\"top\"><a href=\"(?P<link>.*)\"><img border=\"0\" src=\".*\" alt=\"(?P<name>.*)\" width=\"\d+\" height=\"\d+\" hspace=\"0\" vspace=\"0\"></a>~u";
        $search = $this->curl->getContent("http://pdadb.net/index.php?m=search&quick=1", array('exp'=>$name));
        if ($search['code'] == 200 && preg_match($pattern, $search['content'], $matches))
        {
           
            if ($matches['name'] == strtolower($name))
                return "http://pdadb.net/".$matches['link'];
            
        }
        
        return false;
    }
    
    public function getImages($link)
    {
        $pattern = "~target=\"_blank\" href=\".*\"><img border=\"0\" src=\"(?P<image>.*)\" alt=\".*\" width=\"\d*\" height=\"\d*\" hspace=\"0\" vspace=\"0\"></a><br>~u";
        $imgs = $this->curl->getContent($link);
        if (preg_match_all($pattern, $imgs['content'], $matches, PREG_SET_ORDER))
        {
            $result = array();
            foreach($matches as $image)
            {
                $result[] = "http://pdadb.net/".$image['image'];
            }
            return $result;
        }
        return false;
    }
}