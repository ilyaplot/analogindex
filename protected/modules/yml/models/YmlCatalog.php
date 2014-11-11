<?php

class YmlCatalog extends CActiveRecord
{
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{yml_catalog}}";
    }
    
    public function getChildren($parent, $source=null)
    {
        $condition = "parent_id = :parent";
        $params = ["parent"=>$parent];
        if ($source !== null) {
            $condition.=" and source = :source";
            $params['source'] = $source;
        }

        $criteria = new CDbCriteria();
        $criteria->condition = $condition;
        $criteria->params = $params;
        return self::model()->findAll($criteria);
    }
    
    public function getTree($source, $parent = 0)
    {
        $items = $this->getChildren($parent, $source);
        $arrayItem = [];
        foreach ($items as &$item)
        {
            $arrayItem = [
                "text" => CHtml::checkBox("catalogs[{$item->id}]", (bool) $item->enabled, ["id"=>"catalog-{$item->id}"])
                        ." ".Chtml::label($item->name, "catalog-{$item->id}"),
                "expanded" => (bool) $item->enabled,
            ];
            
            $children = $this->getTree($source, $item->catalog_id);
            
            if (!empty($children)) {
                $arrayItem['children'] = $children;
            }
            $item = $arrayItem;
        }
        return $items;
    }
    
    public function setChecked($source, $items) 
    {
        self::model()->updateAll(['enabled'=>0], "source = :source", ["source"=>$source]);
        $items = array_keys($items);
        if (!empty($items)) {
            self::model()->updateAll(["enabled"=>1], "source = :source and id in (".implode(", ", $items).")", ["source"=>$source]);
        }
    }
}
