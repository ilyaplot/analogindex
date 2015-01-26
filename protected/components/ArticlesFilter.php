<?php
class ArticlesFilter
{
    const LINKS_LIMIT = 2;
    
    public static $tags = null;
    
    protected $_model;

    public function __construct()
    {
        if (self::$tags === null) {
            $criteria = new CDbCriteria();
            $criteria->condition = "t.disabled = 0";
            $criteria->order = "field(t.type, 'product','brand','os','word'), length(t.name) desc";
            self::$tags = Tags::model()->findAll($criteria);
            unset($criteria);
        }
    }
    
    public function filter($article)
    {
        $class = get_class($article);
        
        if ($class != 'Articles') { 
            throw new CException("Класс объекта '{$class}'. Для фильтрации необходим Articles.", 0);
        }
        unset ($class);
        
        $this->_model = $article;
        $this->_model->content = $this->_model->source_content;
        $this->_model->updated = new CDbExpression("NOW()");
        $this->_products = [];
        $this->_brands = [];
        $this->_images = [];
        
        $this->_setDescription();
        $this->_downloadImages();
        $this->_runRules();
        
        $links = $this->tagList();
        $this->_model->linkTags($links['tags']);
        $this->_model->linkBrands($links['brands']);
        $this->_model->linkProducts($links['products']);
       
        $this->_model->has_filtered = 1;
                
        return $this->_model;
    }
    
    protected function _downloadImages()
    {
        $this->_model->broken_images = 0;
        
        $html = phpQuery::newDocumentHTML($this->_model->content);
        
        foreach (pq($html)->find("img") as $image) {
            $image = pq($image);
            $alt = $image->attr("alt");
            $alt_replaced = 0;
            
            if (empty($alt)) {
                $alt_replaced = 1;
                $alt = mb_substr($this->_model->title, 0, 255, 'UTF-8');
            }
            $url = $image->attr("src");
            // Если пустой url, удаляем изображение
            if (empty($url)) {
                $image->remove();
                $this->_model->broken_images = 1;
                continue;
            } else {
                // Относительный url
                if (preg_match("/^\/\w+.*/isu", $url)) {
                    $host = preg_replace("/^(http:\/\/[\w\.\-]+)\/.*/isu","$1", $this->_model->source_url);
                    if (empty($host)) {
                        $image->remove();
                        $this->_model->broken_images = 1;
                        continue;
                    }
                    $url = $host.$url;
                // Без http
                } elseif (preg_match("/^\/\/\w+.*/isu", $url)) {
                    $url = "http:".$url;
                }
                
                if (!$imageModel = ArticlesImages::model()->findByAttributes(['source_url'=>$url, 'article'=>$this->_model->id])) {
                    $imageModel = new ArticlesImages();
                    $imageModel->source_url = $url;
                    $tmpfname = tempnam("/tmp", "_analogindex_tmp");
                    if (!$file = fopen($tmpfname, 'w')) {
                        echo "Не могу открыть файл для записи {$tmpfname}".PHP_EOL;
                        $this->_model->broken_images = 1;
                        @unlink($tmpfname);
                        continue;
                    }
                    
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
                    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                    curl_setopt($ch, CURLOPT_REFERER, $this->_model->source_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
                    curl_setopt($ch, CURLOPT_FILE, $file);
                    
                    curl_exec($ch);
                    fclose($file);
                    
                    if (curl_errno($ch)) {
                        echo "Curl error #".curl_errno($ch)." " . curl_error($ch)." " .$url. PHP_EOL;
                        $image->remove();
                        $this->_model->broken_images = 1;
                        @unlink($tmpfname);
                        continue;
                    } 
                    curl_close($ch);
                    $imageModel->save();
                    if($imageModel->setFile($tmpfname)) {
                        $imageModel->name = Yii::app()->urlManager->translitUrl($this->_model->title).".".$imageModel->getExt();
                        $imageModel->article = $this->_model->id;
                        $imageModel->alt = htmlspecialchars(strip_tags($alt));
                        $imageModel->alt_replaced = $alt_replaced;
                        $imageModel->save();
                        @unlink($tmpfname);
                    } else {
                        $image->remove();
                        $this->_model->broken_images = 1;
                        @unlink($tmpfname);
                        continue;
                    }
                } elseif($imageModel->alt_replaced != $alt_replaced) {
                    $imageModel->alt_replaced = $alt_replaced;
                    $imageModel->save();
                }
                
                $url = Yii::app()->createAbsoluteUrl("files/newsimage", [
                    'language' => Language::getZoneForLang($this->_model->lang),
                    'id'=>$imageModel->id,
                    'name'=>$imageModel->name,
                ]);
                $alt = htmlspecialchars(strip_tags($alt));
                $image->replaceWith('<img src="'.$url.'" alt="'.$alt.'" />'); 
                
            }
        }
        $this->_model->content = (string) $html;
        unset($html);
        phpQuery::unloadDocuments();
    }

        /**
     * Расставляет ссылки на тэги и собирает массив успешно установленных
     * @return array
     */
    public function tagList()
    {
        $tags = [];
        $products = [];
        $brands = [];
        
        foreach (self::$tags as $tag) {
            $link = $tag->getLink($this->_model->type, $this->_model->lang); 
            
            if ($this->_hasTag($tag->name)) {
                $tags[] = $tag->id;
                
                if ($product = $tag->getProduct()) {
                    $products[] = $product;
                }
                
                if ($brand = $tag->getBrand()) {
                    $brands[] = $brand;
                }
            }
            
            if ($this->_writeLink($tag->name, $link)) {
                $tags[] = $tag->id;
            }
        }
        unset($tag, $link, $product, $brand);

        return [
            'tags'      => array_unique($tags),
            'brands'    => array_unique($brands),
            'products'  => array_unique($products),
        ];
    }
    

    protected function _writeLink($text, $link)
    {
        
        $text = preg_quote($text, "~");
        $pattern = "~(<[^aA][^>]*?>[^<\"]*?[^\w\d\-:])({$text})([^\w\d\-][^>\"]*?)~iu";
        $content = preg_replace($pattern, "$1{$link}$3", $this->_model->content, self::LINKS_LIMIT, $count);
        if ($count > 0) {
            $this->_model->content = $content;
            return true;
        }
        
        return false;
    }
    
    protected function _hasTag($text)
    {
        $text = preg_quote($text, "~");
        $pattern = "~\b{$text}\b~iu";
        
        if (preg_match($pattern, $this->_model->content)) {
            return true;
        }
        
        return false;
    }

        /**
     * Генерит превью для модели
     */
    protected function _setDescription()
    {   
        
        $words = explode(" ", trim(strip_tags($this->_model->content)));
        
        $this->_model->description = $words[0];
        
        $key = 0;
        do {
            if (isset($words[$key]))
                $key++;
            else 
                break;
            if (!isset($words[$key]) || mb_strlen($this->_model->description.$words[$key], 'UTF-8') > 330)
                break;
            $this->_model->description .= " ".$words[$key];
        } while (mb_strlen($this->_model->description, 'UTF-8') < 300);
        
        $this->_model->description = htmlspecialchars($this->_model->description);
        $this->_model->description = preg_replace("/[ ]+/i", " ", $this->_model->description);
        $this->_model->description = preg_replace("/(\n\r)+/i", "\n\r", $this->_model->description);
        $this->_model->description = preg_replace("/(\r\n)+/i", "\r\n", $this->_model->description);
        $this->_model->description = preg_replace("/(\n)+/i", "\n", $this->_model->description);
        $this->_model->description = mb_substr($this->_model->description, 0, 333, 'UTF-8');
    }
    
    protected function _runRules()
    {
        
        $html = phpQuery::newDocumentHtml($this->_model->content);
        
        foreach($this->rules() as $rule) {
            $function = $rule['function'];
            $html = $function($html);
        }
        
        $this->_model->content = (string) $html;
        
        unset($html);
        phpQuery::unloadDocuments();
    }

    public function _setType()
    {
        $patterns = [
            "/.*\bобзор\b.*/isu" => Articles::TYPE_REVIEW,
            "/.*\bотзыв\b.*/isu" => Articles::TYPE_OPINION,
        ];
        
        foreach ($patterns as $pattern=>$type) {
            if (preg_match($pattern, $this->_model->title) && $this->_model->type = Articles::TYPE_NEWS) {
                echo "ID: {$this->_model->id} сменен тип с {$this->_model->source_type} на {$type}".PHP_EOL;
                return $this->_model->setType($type);
            }
        }
        
        return false;
    }

    public function rules()
    {
        return [
            [   // Удаляет скрипты
                'function'=>function($html) 
                {
                    $html->find("script")->remove();
                    return $html;
                }
            ],
            [   // Удаляет ссылки
                'function'=>function($html)
                {
                    $source_domain = preg_replace("/(https?:\/\/[^\/]+\/).*/isu", "$1", $this->_model->source_url);
                    $blacklist = [
                        "/^http:\/\/dic.academic.ru\/.*/isu",
                        "/^\/.*/isu",
                        "/^https?:\/\/www.$/isu",
                        "/^http:\/\/\w+\.academic\.ru\/\w+\/[\w\%]+/isu",
                    ];
        
                    if (!empty($source_domain))
                        $blacklist[] = "/".preg_quote ($source_domain, '/').".*/isu";
                    
                    foreach ($html->find("a") as $a) {
                        foreach ($blacklist as $rule) {
                            if (preg_match($rule, pq($a)->attr("href"))) {
                                try {
                                    pq($a)->replaceWith(pq($a)->html());
                                } catch (Exception $e) {
                                    
                                }
                            }
                        }
                        
                        if (empty(pq($a)->html())) {
                            pq($a)->remove();
                        }
                    }
                    return $html;
                }
            ],
            [   // Замена битого html
                'function'=>function($html)
                {
                    return preg_replace("/\s[\w\-_\/\.]+\" target=\"_blank\"&gt;/isu", "", (string) $html);
                }
            ],
            [   // Замена битого html
                'function'=>function($html)
                {
                    return preg_replace("/\shref=\"http:\/\/dic\.academic\.ru\/dic\.nsf\/\w+\/\d+\"/isu", "", (string) $html);
                }
            ],
        ];
    }
    
    
}