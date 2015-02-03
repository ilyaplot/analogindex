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
        
        
        $this->_downloadImages();
        $this->_setType();
        $this->_runRules();
        $this->_setDescription();
        
        $this->_model->title = str_replace("[Из песочницы]", "", $this->_model->title);
        $this->_model->title = str_replace("[Песочница]", "", $this->_model->title);
        
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

        /**
         * Ищет все ссылки в документе.
         * Если в ссылке только один вложенны элемент, то проверяем, img ли это.
         * Если img, берем src из href ссылки, alt из img.
         * Заменяем a на img.
         */
        foreach (pq($html)->find("a") as $a) {
            if (pq($a)->children()->length() == 1) {
                if (pq($a)->find("img")->length() == 1) {
                    $src = pq($a)->attr("href");
                    $img = pq($a)->find("img");
                    $alt = pq($img)->attr('alt');
                    if (!empty($alt)) {
                        pq($a)->replaceWith("<img src=\"{$src}\" alt=\"{$alt}\"");
                    }
                }
            }
        }

        
        foreach (pq($html)->find("img") as $image) {
            $image = pq($image);
            $alt = $image->attr("alt");
            $alt_replaced = 0;
            
            if (empty($alt)) {
                $alt_replaced = 1;
                $alt = mb_substr($this->_model->title, 0, 255, 'UTF-8');
            }
            $url = $image->attr("src");
            $lazySrc = $image->attr("data-lazy-src");
            
            if (!empty($lazySrc)) {
                $url = $lazySrc;
            }
            
            if (preg_match("/^data:image/isu", $url)) {
                $image->remove();
            } else if (empty($url)) {
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
                        $imageModel->name = str_replace("x-ms-bmp", "bmp", $imageModel->name);
                        $imageModel->article = $this->_model->id;
                        $imageModel->alt = htmlspecialchars(strip_tags($alt));
                        $imageModel->alt_replaced = $alt_replaced;
                        $imageModel->save();
                        echo "+";
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
            
            "/отзыв/isu" => Articles::TYPE_OPINION,
            
            "/review/isu" => Articles::TYPE_REVIEW,
            "/обзор/isu" => Articles::TYPE_REVIEW,
            
            "/FAQ/isu" => Articles::TYPE_HOWTO,
            "/how ?to/isu" => Articles::TYPE_HOWTO,
            "/recovery/isu" => Articles::TYPE_HOWTO,
            "/download/isu" => Articles::TYPE_HOWTO,
            "/unlock/isu" => Articles::TYPE_HOWTO,
            "/root/isu" => Articles::TYPE_HOWTO,
            "/unroot/isu" => Articles::TYPE_HOWTO,
            "/downgrade/isu" => Articles::TYPE_HOWTO,
            "/upgrade/isu" => Articles::TYPE_HOWTO,
            "/firmware/isu" => Articles::TYPE_HOWTO,
            "/прошивк./isu" => Articles::TYPE_HOWTO,
            "/прошить/isu" => Articles::TYPE_HOWTO,
            "/скачать/isu" => Articles::TYPE_HOWTO,
            "/разобрать./isu" => Articles::TYPE_HOWTO,
            "/разборка/isu" => Articles::TYPE_HOWTO,
            "/tutorial/isu" => Articles::TYPE_HOWTO,
            "/install/isu" => Articles::TYPE_HOWTO,
            "/guide/isu" => Articles::TYPE_HOWTO,
            "/руководство/isu" => Articles::TYPE_HOWTO,
            "/manual/isu" => Articles::TYPE_HOWTO,
            "/set ?up/" => Articles::TYPE_HOWTO,
            "/install/isu" => Articles::TYPE_HOWTO,
            "/improve/isu" => Articles::TYPE_HOWTO,
            "/tweak/isu" => Articles::TYPE_HOWTO,
            "/customize/isu" => Articles::TYPE_HOWTO,
            "/установ[ка|ить]+/isu" => Articles::TYPE_HOWTO,
            "/настро[ить|йка]+/isu" => Articles::TYPE_HOWTO,
        ];
        
        foreach ($patterns as $pattern=>$type) {
            if (preg_match($pattern, $this->_model->title) && $this->_model->type == Articles::TYPE_NEWS) {
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
            [ // sharing
                'function'=>function($html)
                {
                    foreach ($html->find('div[style="text-align:left;"]') as $div) {
                        if(preg_match("/Enter Giveaway by sharing/isu", pq($div)->text())) {
                            pq($div)->remove();
                        }
                    }
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
        
                    //if (!empty($source_domain))
                    //    $blacklist[] = "/".preg_quote ($source_domain, '/').".*/isu";
                    
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
            [   // &amp;http://www.androidauthority.com/best-ces-2014-awards-335034/#8217; - удаляем
                'function'=>function($html)
                {
                    $link = preg_quote($this->_model->source_url, '/');
                    return preg_replace("/[^\"](&amp;{$link}#\d+;)[^\"]/isu", '', (string) $html);
                }
            ],
            [   // Ссылка на оригинал открытым текстом - удаляем
                'function'=>function($html)
                {
                    $link = preg_quote($this->_model->source_url, '/');
                    $link2 = preg_replace("/^h/isu" ,"p\/p", $link);
                    $link2 = preg_replace("~\\\/$~isu" ,"[\/|p\>]", $link2);
                    $html =  preg_replace("/[^\"]({$link})[^\"]/isu", '', (string) $html);
                    return preg_replace("/[^\"]({$link2})[^\"]/isu", '', (string) $html);
                }
            ],
        ];
    }
    
    
}