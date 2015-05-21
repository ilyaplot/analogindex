<?php

class Articles extends CActiveRecord
{

    public $source_type;

    const TYPE_NEWS = 'news'; // Новость
    const TYPE_REVIEW = 'review'; // Обзор
    const TYPE_OPINION = 'opinion'; // Отзыв
    const TYPE_HOWTO = 'howto'; // FAQ

    public $related = [
        'news'=>[],
        'review'=>[],
        'opinion'=>[],
        'howto'=>[],
    ];
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{articles}}";
    }

    public function rules()
    {
        return [
            ['source_content', 'length', 'min' => 50, 'allowEmpty' => false],
            ['title', 'length', 'min' => 5, 'allowEmpty' => false],
            ['source_url, type, lang', 'required'],
            ['source_url', 'unique', 'allowEmpty' => false],
                //['content', 'filter', 'filter'=>[$obj=new CHtmlPurifier(), 'purify']],
        ];
    }

    public function linkTags($tags)
    {
        $this->has_tags = 0;
        //$transaction = $this->getDbConnection()->beginTransaction();
        try {
            $this->getDbConnection()->createCommand("delete from {{articles_tags}} where article = {$this->id}")->execute();
            foreach ($tags as $tag) {
                $model = new ArticlesTags();
                $model->article = $this->id;
                $model->tag = $tag;
                if ($model->validate()) {
                    $model->save();
                    $this->has_tags = 0;
                }
            }
        } catch (Exception $ex) {
            //$transaction->rollback();
            throw $ex;
        }
        //$transaction->commit();
    }

    public function linkBrands($brands)
    {
        //$transaction = $this->getDbConnection()->beginTransaction();
        try {
            $this->getDbConnection()->createCommand("delete from {{brands_articles}} where article = {$this->id}")->execute();
            foreach ($brands as $brand) {
                $brandsArticle = new BrandsArticles();
                $brandsArticle->article = $this->id;
                $brandsArticle->brand = $brand;
                if ($brandsArticle->validate()) {
                    $brandsArticle->save();
                }
            }
        } catch (Exception $ex) {
            //$transaction->rollback();
            throw $ex;
        }
        //$transaction->commit();
    }

    public function linkProducts($products)
    {
        //$transaction = $this->getDbConnection()->beginTransaction();
        try {
            if ($this->type != 'opinion') {
                $this->getDbConnection()->setActive(false);
                $this->getDbConnection()->setActive(true);
                $this->getDbConnection()->createCommand("delete from {{goods_articles}} where article = {$this->id}")->execute();
            }
            foreach ($products as $product) {
                $productsArticle = new GoodsArticles();
                $productsArticle->article = $this->id;
                $productsArticle->goods = $product;
                if ($productsArticle->validate()) {
                    $productsArticle->save();
                }
            }
        } catch (Exception $ex) {
            //$transaction->rollback();
            throw $ex;
        }
        //$transaction->commit();
    }

    /**
     * @todo Написать добавление редиректа
     * @param type $type
     * @return boolean
     */
    public function setType($type)
    {
        if (!in_array($type, [
                    self::TYPE_NEWS,
                    self::TYPE_OPINION,
                    self::TYPE_REVIEW,
                    self::TYPE_HOWTO,
                ])) {
            return false;
        }

        $this->type = $type;
        return true;
    }

    public function getUrl()
    {

        return Yii::app()->createAbsoluteUrl("articles/index", [
                    'type' => $this->type,
                    'link' => $this->link,
                    'id' => $this->id,
                    'language' => Language::getZoneForLang($this->lang),
        ]);
    }

    public function beforeDelete()
    {
        //$transaction = $this->getDbConnection()->beginTransaction();

        try {
            // Удаление 
            $this->getDbConnection()->createCommand("delete from {{articles_tags}} where article = {$this->id}")->execute();
        } catch (Exception $ex) {
            //$transaction->rollback();
            throw $ex;
        }
        //$transaction->commit();

        $images = ArticlesImages::model()->findByAttributes(['article' => $this->id]);
        foreach ($images as $image) {
            $image->delete();
        }

        return parent::beforeDelete();
    }

    public function afterFind()
    {
        $this->source_type = $this->type;
        
        return parent::afterFind();
    }
    
    public function fillRelated()
    {
        foreach ($this->related as $type=>$container) {
            $this->related[$type] = $this->getRelatedArticles($type, $this->lang, 5);
        }
    }

    public function getRelatedArticles($type, $lang, $limit)
    {
        $tags = [];
        foreach ($this->tags as $tag) {
            $tags[] = $tag->tag_data->id;
        }
        $tags = array_unique($tags);
        asort($tags);
        $key = md5("_related_articles_".implode(",",$tags)."_{$lang}_{$limit}");
        
        if (!$related = Yii::app()->cache->get($key)) {

            $connection = $this->getDbConnection();
            $query = "select 
                a.id
            from 
                ai_articles a
            where 
                a.id in (select article from ai_articles_tags where tag in (".implode(",", $tags)."))
                and a.lang = :lang
                and a.type = :type
            group by a.id
            order by a.created desc
            limit 5";
            
            $params = [
                'lang' => $lang,
                'type' => $type
            ];
            
            $relatedRows = $connection->createCommand($query)->queryAll(true, $params);
            $relatedIds = [];
            foreach ($relatedRows as $row) {
                $relatedIds[] = $row['id'];
            }
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $relatedIds);
            $criteria->select = "id, title, description, link, created, type, lang";
            $criteria->limit = 5;
            $criteria->order = 'created desc';
            $related = self::model()->findAll($criteria);
            
            Yii::app()->cache->set($key, $related, 60 * 60 * 24);
        }
        return $related;
    }

    public function relations()
    {
        return [
            'brand' => [self::HAS_ONE, "BrandsArticles", 'article',
                'select' => false,
                'joinType' => 'inner join',
            ],
            'product' => [self::HAS_ONE, "GoodsArticles", 'article',
                'select' => false,
                'joinType' => 'inner join',
                'condition' => 'product.disabled = 0',
            ],
            "preview_image" => [self::HAS_ONE, "ArticlesImagesCopy", "article",],
            'tags' => [self::HAS_MANY, 'ArticlesTags', 'article'],
        ];
    }

    public function afterSave()
    {
        GoodsArticles::model()->filter($this->id);
        return parent::afterSave();
    }
    /*
    public function getRelatedArticles($type)
    {
        if (!$result = Yii::app()->cache->get(md5("related_{$type}_{$this->id}"))) {
            $tagsArray = [];
            foreach ($this->tags as $tag) {
                if (empty($tag->tag_data)) {
                    continue;
                }
                $tagsArray[$tag->tag_data->id] = $tag->tag_data->id;
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition('tag_data.id', $tagsArray);
            $criteria->compare("articles_data.lang", $this->lang);
            $criteria->compare("articles_data.type", $type);
            $criteria->order = "field(tag_data.type, 'product', 'brand', 'os', 'word'), articles_data.created desc";
            $criteria->limit = 10;
            $criteria->group = "articles_data.id";
            $criteria->select = "articles_data.id as id";
            $data = ArticlesTags::model()->cache(60 * 60)->with(['articles_data', 'tag_data'])->findAll($criteria);
            $ids = [];
            foreach ($data as $tag) {
                $ids[$tag->id] = $tag->id;
            }

            $criteria = new CDbCriteria();
            $criteria->select = "t.type, t.link, t.id, t.lang, t.title, t.created, t.description";
            $criteria->addInCondition('id', $ids);
            $criteria->order = 't.created';
            $criteria->limit = 10;
            $result = self::model()->findAll($criteria);
            Yii::app()->cache->set(md5("related_{$type}_{$this->id}"), $result);
        }
        return $result;
    }
     * 
     */

}
