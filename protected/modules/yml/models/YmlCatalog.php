<?php

class YmlCatalog extends CActiveRecord
{
    /**
     * Массив всех категорий category_id=>item
     * @var array
     */
    
    protected $treeItems = [];
    /**
     * Массив подчинения категорий parent_id=>[catalog_id,...]
     * @var array
     */
    protected $parentItemsKeys = [];
    
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{yml_catalog}}";
    }
    
    public function getChildren($parent)
    {
        $items = [];
        if (!empty($this->parentItemsKeys[$parent])) {
            foreach ($this->parentItemsKeys[$parent] as $key) {
                $items[$key] = $this->treeItems[$key];
            }
        }

        return $items;
    }
    
    public function getTree($source, $parent = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "source = :source";
        $criteria->params = ["source" => $source];
        $criteria->order = "parent_id asc, id asc";
        $this->treeItems = [];
        $treeItems = self::model()->findAll($criteria);
        foreach ($treeItems as $item) {
            /**
             * @todo Допилить!
             */
            $this->treeItems[$item->catalog_id] = $item;
            $this->parentItemsKeys[(int) $item->parent_id][] = $item->catalog_id;
        }
        unset ($treeItems);
        $items = $this->getTreeRecursive($parent);

        return $items;
    }
    
    protected function getTreeRecursive($parent)
    {
 
        $items = $this->getChildren($parent);
        
        $arrayItem = [];
        foreach ($items as &$item)
        {
            $arrayItem = [
                "text" => CHtml::checkBox("catalogs[{$item->id}]", (bool) $item->enabled, ["id"=>"catalog-{$item->id}"])
                        ." ".Chtml::label($item->name, "catalog-{$item->id}"),
                "expanded" => (bool) $item->enabled,
            ];
            
            $children = $this->getTreeRecursive($item->catalog_id);
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
