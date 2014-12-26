<?php
/**
 * Нет точного соответствия
 * select gn.id from ai_goods g inner join ai_brands b on g.brand = b.id inner join ai_goods_news gn on gn.goods = g.id inner join ai_news n on n.id = gn.news where n.title not like concat('%', b.name, '%', g.name, '%') order by n.created desc limit 100;
 * select count(g.id) from ai_goods g inner join ai_brands b on g.brand = b.id inner join ai_goods_news gn on gn.goods = g.id inner join ai_news n on n.id = gn.news;
 */
class News extends CActiveRecord
{

    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{news}}";
    }

    public function rules()
    {
        return [
            ['source_url, content', 'required'],
            ['source_url, content', 'type', 'type'=>'string', 'allowEmpty'=>false],
            ['content', 'length', 'min'=>10],
            ['source_url', 'length', 'min'=>10, 'max'=>500],
            ['source_url', 'unique', 'allowEmpty'=>false],
        ];
    }
    
    public function relations()
    {
        return [
            'brand'=>[self::HAS_ONE, "BrandsNews", 'news',
                'select'=>false,
                'joinType'=>'inner join',
            ],
            'product'=>[self::HAS_ONE, "GoodsNews", 'news',
                'select'=>false,
                'joinType'=>'inner join',
                'condition'=>'product.disabled = 0',
            ],
            "preview_image"=>[self::HAS_ONE, "NewsImages", "news", 
                'condition'=>'preview_image.has_preview = 1',
            ],
            'tags'=>[self::HAS_MANY, 'NewsTags', 'news'],
        ];
    }
    
    public function filterContent()
    {
        $html = phpQuery::newDocumentHtml($this->content);
        $html->find("script")->remove();
        $source_domain = preg_replace("/(http:\/\/[^\/]+\/).*/isu", "$1", $this->source_url);
        //$source_domain = preg_replace("/<p>\w+: <a href=\".*\">\w+<\/a>\..*&gt;(.*)<\/p>/isu", "$1", $source_domain);
        $blacklist = [
            "/^http:\/\/dic.academic.ru\/.*/isu",
            "/^\/.*/isu",
            "/^http:\/\/www.$/isu",
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
        }
        //3dnews.ru/news/634430" target="_blank"&gt;
        $html = preg_replace("/\s[\w\-_\/\.]+\" target=\"_blank\"&gt;/isu", "", (string) $html);
        // href="http://dic.academic.ru/dic.nsf/ruwiki/1425879"
        $html = preg_replace("/\shref=\"http:\/\/dic\.academic\.ru\/dic\.nsf\/\w+\/\d+\"/isu", "", (string) $html);
        //$this->content_filtered = (string) $html;
        //$this->save();
        return $html;
    }
    
    public function filteredContent()
    {
        if (!empty($this->content_filtered)) {
            return $this->content_filtered;
        }
        return $this->filterContent();
    }
    
    public function getWords($str, $length = 50)
    {
        $words = explode(" ", trim(strip_tags($str)));
        if (count($words) ==1 && empty($words[0])) {
            return [];
        }
        return implode (" ", array_slice($words, 0, $length));
    }
    
    public function getDescription()
    {
        if (!empty($this->preview)) {
            return $this->preview;
        }
        
        $content = $this->filteredContent();
        
        if (empty($content)) {
            return $this->preview;
        }
        
        $words = explode(" ", trim(strip_tags($content)));
        
        $description = $words[0];
        
        
        $key = 0;
        do {
            if (isset($words[$key]))
                $key++;
            else 
                break;
            if (!isset($words[$key]) || mb_strlen($description.$words[$key], 'UTF-8') > 250)
                break;
            $description.=" ".$words[$key];
        } while (mb_strlen($description, 'UTF-8') < 245);
        $description = htmlspecialchars($description);
        $description = mb_substr($description, 0, 250, 'UTF-8');
        $this->preview = $description;
        $this->save();
        return $description;
    }
    
    public function afterSave()
    {
        //$this->preview = $this->getDescription();
        return parent::beforeSave();
    }
    
}
