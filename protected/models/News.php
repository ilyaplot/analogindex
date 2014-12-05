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
            'tags'=>[self::HAS_MANY, 'NewsTags', 'news'],
        ];
    }
    
    public function filteredContent()
    {
        if (!empty($this->content_filtered)) {
            return $this->content_filtered;
        }
        
        
        $html = phpQuery::newDocumentHtml($this->content);
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
                if (preg_match($rule, pq($a)->attr("href")))
                    pq($a)->replaceWith(pq($a)->html());
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
}
