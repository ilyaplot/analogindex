<?php
class GoodsArticles extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{goods_articles}}";
    }
    
    public function rules()
    {
        return [
            ['goods, article', 'required'],
            ['goods, article', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['goods', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'goods', 
                'className'=>'GoodsArticles', 
                'criteria'=>['condition'=>'article = :article', 'params'=>['article'=>  $this->article]]
            ]
        ];
    }
    
    public function filter($id = null)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            $query = "select gn.id from {{goods}} g "
                . "inner join {{brands}} b on g.brand = b.id "
                . "inner join {{goods_articles}} gn on gn.goods = g.id "
                . "inner join {{articles}} n on n.id = gn.article "
                . "where n.title not like concat('%', b.name, '%', g.name, '%') ";
            
            if (!empty($id)) {
                $query .= "and n.id = ".$id;
            }


            $connection = $this->getDbConnection();
            $ids = $connection->createCommand($query)->queryAll();
            
            $idsClist = array_chunk($ids, 100);
            
            foreach ($idsClist as $ids) {
                $in = [];
                foreach ($ids as $id) {
                    $in[] = $id['id'];
                }
                if (!empty($in)) {
                    $query = "update {{goods_articles}} set disabled = 1 where id in (".implode(", ",$in).")";
                    $connection->createCommand($query)->execute();
                }
            }
        } catch (Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
        $transaction->commit();
    }
    
    public function relations()
    {
        return [
            'article_data'=>[self::BELONGS_TO, 'Articles', 'article'],
        ];
    }
    
    public function getCount($product, $type = null)
    {
        $condition = 't.disabled = 0 and t.goods = :product and article_data.lang = :lang';
        $params = ['product'=>$product, 'lang'=>Yii::app()->language];
        if ($type != null) {
            $condition.=' and article_data.type = :type';
            $params['type'] = $type;
        }
        return self::model()->cache(60*60)->with(['article_data'=>['joinType'=>'inner join']])->count($condition, $params);
    }

}
