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
            self::$tags = Tags::model()->with(['goods', 'brand'])->findAll($criteria);
            unset($criteria);
        }
    }

    public function filter($article)
    {
        $class = get_class($article);

        if ($class != 'Articles') {
            throw new CException("Класс объекта '{$class}'. Для фильтрации необходим Articles.", 0);
        }
        unset($class);

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

        $this->_model->title = preg_replace("/\[[\w\s]+\]/isu", "", $this->_model->title);

        $links = $this->tagList();
        echo "LIST END" . PHP_EOL;
        $this->_model->linkTags($links['tags']);
        echo "TAGS" . PHP_EOL;
        $this->_model->linkBrands($links['brands']);
        echo "BRANDS" . PHP_EOL;
        $this->_model->linkProducts($links['products']);
        echo "PRODUCTS" . PHP_EOL;
        $this->_model->has_filtered = 1;
        echo "END FILTERING" . PHP_EOL;

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
                    if (!empty($src)) {
                        pq($a)->replaceWith("<img src=\"{$src}\" alt=\"{$alt}\"");
                        continue;
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

            if (preg_match("/blank/isu", $url)) {
                echo "BLAMK IMAGE: " . $url . PHP_EOL;
                $url = '';
            }

            $lazySrc = !empty($image->attr("data-big-src")) ? $image->attr("data-big-src") : $image->attr("data-lazy-src");


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
                    $host = preg_replace("/^(http:\/\/[\w\.\-]+)\/.*/isu", "$1", $this->_model->source_url);
                    if (empty($host)) {
                        $image->remove();
                        $this->_model->broken_images = 1;
                        continue;
                    }
                    $url = $host . $url;
                    // Без http
                } elseif (preg_match("/^\/\/\w+.*/isu", $url)) {
                    $url = "http:" . $url;
                }

                if (!$imageModel = NImages::model()->findByAttributes(['source_url' => $url])) {


                    $tmpfname = tempnam("/tmp", "_analogindex_tmp");
                    @chmod($tmpfname, 0777);
                    if (!$file = fopen($tmpfname, 'w')) {
                        echo "Не могу открыть файл для записи {$tmpfname}" . PHP_EOL;
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
                    //curl_setopt($ch, CURLOPT_SSLVERSION, 3);
                    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
                    curl_setopt($ch, CURLOPT_FILE, $file);

                    curl_exec($ch);
                    fclose($file);

                    if (curl_errno($ch)) {
                        echo "Curl error #" . curl_errno($ch) . " " . curl_error($ch) . " " . $url . PHP_EOL;
                        $image->remove();
                        $this->_model->broken_images = 1;
                        @unlink($tmpfname);
                        continue;
                    }
                    curl_close($ch);


                    $model = new NImages();

                    if ($id = $model->create($tmpfname, 'article', $alt, $url, $alt)) {
                        if ($model->copyExist == true) {
                            unlink($tmpfname);
                        }

                        $gi = new ArticlesImagesCopy();
                        $gi->article = $this->_model->id;
                        $gi->image = $id;

                        if ($gi->validate()) {
                            echo "+" . PHP_EOL;
                            echo $model->getHtml('1024x1024') . PHP_EOL;
                            $gi->save();
                        }

                        $imageModel = NImages::model()->findByPk($id);
                        $imageModel->alt_replaced = $alt_replaced;
                        $imageModel->save();
                    } else {
                        $this->_model->broken_images = 1;
                        $image->remove();
                        @unlink($tmpfname);
                        continue;
                    }
                }
                $image->replaceWith($imageModel->getHtml(NImages::SIZE_ARTICLE_BIG, $this->_model->lang));
                @unlink($tmpfname);
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

            if (mb_strlen($tag->name) > 14) {
                continue;
            }

            if ($this->_hasTag($tag->name)) {
                $tags[] = $tag->id;

                if ($product = $tag->getProduct()) {
                    $products[] = $product;
                }

                if ($brand = $tag->getBrand()) {
                    $brands[] = $brand;
                }
            }



            $link = $tag->getLink($this->_model->type, $this->_model->lang);

            if ($this->_writeLink($tag->name, $link)) {
                $tags[] = $tag->id;
            }
        }

        unset($tag, $link, $product, $brand);

        return [
            'tags' => array_unique($tags),
            'brands' => array_unique($brands),
            'products' => array_unique($products),
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
            if (!isset($words[$key]) || mb_strlen($this->_model->description . $words[$key], 'UTF-8') > 330)
                break;
            $this->_model->description .= " " . $words[$key];
        } while (mb_strlen($this->_model->description, 'UTF-8') < 300);

        $this->_model->description = htmlspecialchars($this->_model->description);
        $this->_model->description = preg_replace("/[ ]+/i", " ", $this->_model->description);
        $this->_model->description = preg_replace("/(\n\r)+/i", "\n\r", $this->_model->description);
        $this->_model->description = preg_replace("/(\r\n)+/i", "\r\n", $this->_model->description);
        $this->_model->description = preg_replace("/(\n)+/i", "\n", $this->_model->description);
        $this->_model->description = mb_substr($this->_model->description, 0, 333, 'UTF-8');
        echo "END DESCRIPTION" . PHP_EOL;
    }

    protected function _runRules()
    {

        $html = phpQuery::newDocumentHtml($this->_model->content);

        foreach ($this->rules() as $key => $rule) {
            echo "Filter #{$key}" . PHP_EOL;
            $function = $rule['function'];
            $html = $function($html);
        }

        $this->_model->content = (string) $html;

        unset($html);
        phpQuery::unloadDocuments();
        echo "END RULES" . PHP_EOL;
    }

    public function _setType()
    {
        $patterns = [
            "/отзыв/isu" => Articles::TYPE_OPINION,
            "/review/isu" => Articles::TYPE_REVIEW,
            "/обзор/isu" => Articles::TYPE_REVIEW,
            "/how you/isu" => Articles::TYPE_HOWTO,
            "/lern now/isu" => Articles::TYPE_HOWTO,
            "/enabl(ing|e) (hidden )?features/isu" => Articles::TYPE_HOWTO,
            "/джейлбрейк/isu" => Articles::TYPE_HOWTO,
            "/jailbreak/isu" => Articles::TYPE_HOWTO,
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
            "/установ(ка|ить)+/isu" => Articles::TYPE_HOWTO,
            "/настро(ить|йка)+/isu" => Articles::TYPE_HOWTO,
        ];

        foreach ($patterns as $pattern => $type) {
            if (preg_match($pattern, $this->_model->title) && $this->_model->type == Articles::TYPE_NEWS) {
                echo "ID: {$this->_model->id} сменен тип с {$this->_model->source_type} на {$type}" . PHP_EOL;
                return $this->_model->setType($type);
            }
        }

        return false;
    }

    public function rules()
    {
        return [

            [   // Удаляет скрипты
                'function' => function($html) {
                    $html->find("script, noscript")->remove();
                    $html->find("style")->remove();
                    $html->find("#articleHeader")->remove();
                    $html->find("p.jetpack-slideshow-noscript")->remove();
                    return $html;
                }
            ],
            [ // sharing
                'function' => function($html) {
                    foreach ($html->find('div[style="text-align:left;"]') as $div) {
                        if (preg_match("/Enter Giveaway by sharing/isu", pq($div)->text())) {
                            pq($div)->remove();
                        }
                    }
                    return $html;
                }
            ],
            [   // Удаляет ссылки
                'function' => function($html) {

                    $source_domain = preg_replace("/https?:\/\/([^\/]+\/).*/isu", "$1", $this->_model->source_url);
                    $source_domain = preg_replace("/www\./isu", '', $source_domain);

                    $blacklist = [
                        "/^http:\/\/dic.academic.ru\/.*/isu",
                        "/^\/.*/isu",
                        "/^https?:\/\/www.$/isu",
                        "/^http:\/\/\w+\.academic\.ru\/\w+\/[\w\%]+/isu",
                    ];

                    if (!empty($source_domain))
                        $blacklist[] = "/" . preg_quote($source_domain, '/') . ".*/isu";

                    foreach ($html->find("a") as $a) {
                        foreach ($blacklist as $rule) {
                            if (preg_match($rule, pq($a)->attr("href"))) {
                                if (empty(pq($a)->html())) {
                                    pq($a)->remove();
                                    continue;
                                }

                                try {
                                    $ah = pq($a)->html();
                                    pq($a)->replaceWith($ah);

                                    echo "Replaced: {$rule} : {$ah}" . PHP_EOL;
                                } catch (Exception $e) {
                                    echo $e->getMessage() . PHP_EOL;
                                }
                            }
                        }
                    }
                    return $html;
                }
                    ],
                    [   // Замена битого html
                        'function' => function($html) {
                            return preg_replace("/\s[\w\-_\/\.]+\" target=\"_blank\"&gt;/isu", "", (string) $html);
                        }
                    ],
                    [   // Замена битого html
                        'function' => function($html) {
                            return preg_replace("/\shref=\"http:\/\/dic\.academic\.ru\/dic\.nsf\/\w+\/\d+\"/isu", "", (string) $html);
                        }
                    ],
                    [   // &amp;http://www.androidauthority.com/best-ces-2014-awards-335034/#8217; - удаляем
                        'function' => function($html) {
                            $link = preg_quote($this->_model->source_url, '/');
                            return preg_replace("/[^\"](&amp;{$link}#\d+;)[^\"]/isu", '', (string) $html);
                        }
                    ],
                    [
                        'function' => function($html) {
                            return preg_replace("/" . preg_quote("(adsbygoogle = window.adsbygoogle || []).push({});") . "/isu", '', $html);
                        }
                    ],
                    [   // Ссылка на оригинал открытым текстом - удаляем
                        'function' => function($html) {
                            $link = preg_quote($this->_model->source_url, '/');
                            $link2 = preg_replace("/^h/isu", "p\/p", $link);
                            $link2 = preg_replace("~\\\/$~isu", "[\/|p\>]", $link2);
                            $html = preg_replace("/[^\"]({$link})[^\"]/isu", '', (string) $html);
                            return preg_replace("/[^\"]({$link2})[^\"]/isu", '', (string) $html);
                        }
                    ],
                    [ // Удаляет параметры style, class и id
                        'function' => function($html) {
                            $html = phpQuery::newDocumentHTML($html);
                            foreach (pq($html)->find('*') as $tag) {
                                pq($tag)->removeAttr('style');
                                pq($tag)->removeAttr('class');
                                pq($tag)->removeAttr('id');
                                pq($tag)->removeAttr('itemprop');
                                pq($tag)->removeAttr('itemscope');
                                pq($tag)->removeAttr('itemtype');
                                pq($tag)->removeAttr('width');
                                pq($tag)->removeAttr('height');
                                pq($tag)->removeAttr('border');
                                pq($tag)->removeAttr('data-gallery');
                            }
                            return (string) $html;
                        }
                    ],
                    [
                        'function' => function($html) {
                            $html = phpQuery::newDocumentHTML($html);
                            foreach (pq($html)->find('embed, iframe') as $embed) {

                                $src = pq($embed)->attr("src");
                                echo "{$src}" . PHP_EOL;
                                $pattern = "/(https:|http:)?\/\/www\.youtube(\-nocookie)?\.com\/(embed|v)\/(?P<code>[\w\-\_]+)(\?(list|hl)=.*)?/isu";
                                if (preg_match($pattern, $src, $matches)) {
                                    $criteria = new CDbCriteria();
                                    $criteria->condition = "t.article = :article and t.link = :link";
                                    $criteria->params = array('article' => $this->_model->id, 'link' => $matches['code']);

                                    if (!$video = Videos::model()->find($criteria)) {
                                        $video = new Videos();
                                        $video->article = $this->_model->id;
                                        $video->link = $matches['code'];
                                        $video->lang = $this->_model->lang;
                                        $video->save();
                                    }

                                    $snipet = $video->getYoutubeSnippet($video->link, true);

                                    if (!$snipet || $snipet->duration == '0000-00-00 00:00:00' || empty($snipet->title)) {
                                        $video->delete();
                                        pq($embed)->remove();
                                        continue;
                                    }

                                    if (!empty($video->duration)) {
                                        $replace = '
                                            <div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
                                                <div style="display: none;">
                                                    <a itemprop="url" rel="nofollow" href="http://www.youtube.com/watch?v=' . $matches['code'] . '"></a>
                                                    <span itemprop="name">' . $video->title . '</span>
                                                    <span itemprop="description">' . $video->description . '</span>
                                                    <meta itemprop="duration" content="' . $video->duration . '"/>
                                                    <meta itemprop="isFamilyFriendly" content="true"/>
                                                    <meta itemprop="uploadDate" content="' . $video->date_added . '"/>
                                                    <span itemprop="thumbnail" itemscope itemtype="http://schema.org/ImageObject">
                                                        <img itemprop="contentUrl" src="' . $video->thumbnail . '"/>
                                                        <meta itemprop="width" content="540"/>
                                                        <meta itemprop="height" content="315"/>
                                                    </span>
                                                </div>
                                                ' . $video->getTemplate() . '
                                            </div>';
                                    } else {
                                        $replace = $video->getTemplate();
                                    }
                                    pq($embed)->replaceWith($replace);
                                    echo $replace . PHP_EOL;
                                } else {
                                    pq($embed)->remove();
                                }
                            }
                            $html = (string) $html;
                            phpQuery::unloadDocuments();
                            $html = phpQuery::newDocumentHTML((string) $html);

                            foreach (pq($html)->find('img') as $img) {
                                pq($img)->replaceWith((string) pq($img) . "<br>");
                            }

                            $pattern = "/\/\*=999999\).+?\.push\(..\);/isu";

                            return preg_replace($pattern, '', (string) $html);
                        }
                            ]
                        ];
                    }

                }
                