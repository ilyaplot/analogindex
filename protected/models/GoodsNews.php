<?php
class GoodsNews extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{goods_news}}";
    }
    
    public function rules()
    {
        return [
            ['goods, news', 'required'],
            ['goods, news', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['goods', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'goods', 
                'className'=>'GoodsNews', 
                'criteria'=>['condition'=>'news = :news', 'params'=>['news'=>  $this->news]]
            ]
        ];
    }
    
    public function filter()
    {
        $query = "select gn.id from {{goods}} g "
                . "inner join {{brands}} b on g.brand = b.id "
                . "inner join {{goods_news}} gn on gn.goods = g.id "
                . "inner join {{news}} n on n.id = gn.news "
                . "where n.title not like concat('%', b.name, '%', g.name, '%')";
        $connection = $this->getDbConnection();
        $ids = $connection->createCommand($query)->queryAll();
        $in = [];
        foreach ($ids as $id) {
            $in[] = $id['id'];
        }
        if (!empty($in)) {
            $query = "update {{goods_news}} set disabled = 1 where id in (".implode(", ",$in).")";
            $connection->createCommand($query)->execute();
        }
    }

}
