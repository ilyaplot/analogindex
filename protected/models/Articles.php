<?php
class Articles extends CActiveRecord
{
    public $source_type;
    
    const TYPE_NEWS = 'news'; // Новость
    const TYPE_REVIEW = 'review'; // Обзор
    const TYPE_OPINION = 'opinion'; // Отзыв
    const TYPE_HOWTO = 'howto'; // FAQ

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
            ['source_content', 'length', 'min'=>50, 'allowEmpty'=>false],
            ['title', 'length', 'min'=>5, 'allowEmpty'=>false],
            ['source_url, type, lang', 'required'],
            ['source_url', 'unique', 'allowEmpty'=>false],
            ['content', 'filter', 'filter'=>[$obj=new CHtmlPurifier(), 'purify']],
        ];
    }
    
    public function linkTags($tags)
    {
        $this->has_tags = 0;
        $transaction = $this->getDbConnection()->beginTransaction();
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
            $transaction->rollback();
            throw $ex;
        }
        $transaction->commit();
    }
    
    public function linkBrands($brands)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            $this->getDbConnection()->createCommand("delete from {{brands_articles}} where article = {$this->id}")->execute();
            foreach($brands as $brand) {
                $brandsArticle = new BrandsArticles();
                $brandsArticle->article = $this->id;
                $brandsArticle->brand = $brand;
                if ($brandsArticle->validate()) {
                    $brandsArticle->save();
                }
            }
        } catch (Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
        $transaction->commit();
    }
    
    public function linkProducts($products)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            if ($this->type != 'opinion') {
                $this->getDbConnection()->createCommand("delete from {{goods_articles}} where article = {$this->id}")->execute();
            }
            foreach($products as $product) {
                $productsArticle = new GoodsArticles();
                $productsArticle->article = $this->id;
                $productsArticle->goods = $product;
                if ($productsArticle->validate()) {
                    $productsArticle->save();
                }
            }
        } catch (Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
        $transaction->commit();
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
        return "/".$this->type."/".$this->link."_".$this->id.".html";
    }


    public function beforeDelete()
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        
        try {
            // Удаление 
            $this->getDbConnection()->createCommand("delete from {{articles_tags}} where article = {$this->id}")->execute();
            
        } catch (Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
        $transaction->commit();
        
        $images = ArticlesImages::model()->findByAttributes(['article'=>$this->id]);
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
    
    public function relations()
    {
        return [
            'brand'=>[self::HAS_ONE, "BrandsArticles", 'article',
                'select'=>false,
                'joinType'=>'inner join',
            ],
            'product'=>[self::HAS_ONE, "GoodsArticles", 'article',
                'select'=>false,
                'joinType'=>'inner join',
                'condition'=>'product.disabled = 0',
            ],
            "preview_image"=>[self::HAS_ONE, "ArticlesImages", "article", 
                'condition'=>'preview_image.has_preview = 1',
            ],
            'tags'=>[self::HAS_MANY, 'ArticlesTags', 'article'],
        ];
    }
}
