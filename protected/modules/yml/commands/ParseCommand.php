<?php
class ParseCommand extends CConsoleCommand
{
    protected $sourceId;
    
    public function beforeAction($action, $params)
    {
        Yii::import("application.modules.yml.components.YmlParser");
        Yii::import("application.modules.yml.models.*");
        return parent::beforeAction($action, $params);
    }

    public function actionIndex()
    {
        $criteria = new CDbCriteria();
        //$criteria->condition = "status = 1";
        
        $sources = YmlSources::model()->findAll($criteria);
        foreach ($sources as &$source) {
            echo "Parsing {$source->name}...".PHP_EOL;
            $this->sourceId = $source->id;
            $ymlFile = "/inktomia/db/analogindex/yml/catalogs/{$this->sourceId}.yml";
            $parser = new YmlParser($ymlFile);

            $parser->registerEvent("yml_catalog.shop.categories.category", function($elem, $items){
                $model = (YmlCatalog::model()->countByAttributes(["source"=>$this->sourceId, "catalog_id"=>$elem->attrs['id']])) 
                        ? YmlCatalog::model()->findByAttributes(["source"=>$this->sourceId, "catalog_id"=>$elem->attrs['id']]) :
                        new YmlCatalog();
                $model->source = $this->sourceId;
                $model->catalog_id = $elem->attrs['id'];
                $model->parent_id = $elem->attrs['parentId'];
                $model->name = $elem->data;
                $model->save();
                
            }, false);


            $parser->registerEvent("yml_catalog.shop.offers.offer", function($elem, $items){
                if (!empty($items['categoryId'])) {
                    if ($catalog = YmlCatalog::model()->findByAttributes([
                        "source"=>$this->sourceId,
                        "catalog_id"=>$items['categoryId']->data,
                        "enabled"=>true
                    ]) && !empty($elem->attrs['id'])) {
                        $model = (YmlItems::model()->countByAttributes(["source"=>$this->sourceId, "offer_id"=>$elem->attrs['id'], 'price'=>!empty($items['price']->data) ? (float) $items['price']->data : 0])) 
                            ? YmlItems::model()->findByAttributes(["source"=>$this->sourceId, "offer_id"=>$elem->attrs['id'], 'price'=>!empty($items['price']->data) ? (float) $items['price']->data : 0]) :
                            new YmlItems();
                        $model->source = $this->sourceId;
                        $model->offer_id = $elem->attrs['id'];
                        $model->available = !empty($elem->attrs['available']) ? (bool) $elem->attrs['available'] : false;
                        $model->ppc = !empty($elem->attrs['ppc']) ? (float) $elem->attrs['ppc'] : 0;
                        $model->category_id = $items['categoryId']->data;
                        $model->url = !empty($items['url']->data) ? $items['url']->data : '';
                        $model->price = !empty($items['price']->data) ? (float) $items['price']->data : 0;
                        $model->currency = !empty($items['currencyId']->data) ? $items['currencyId']->data : '';
                        $model->picture = !empty($items['picture']->data) ? $items['picture']->data : '';
                        $model->name = !empty($items['name']->data) ? $items['name']->data : ''; 
                        $model->description = !empty($items['description']->data) ? $items['description']->data : ''; 
                        $model->prefix = !empty($items['typePrefix']->data) ? $items['typePrefix']->data : '';
                        $model->vendor = !empty($items['vendor']->data) ? $items['vendor']->data : ''; 
                        $model->model = !empty($items['model']->data) ? $items['model']->data : ''; 
                        $model->created = new CDbExpression("NOW()");
                        
                        if (empty($model->name) && !empty($items['vendor']->data) && !empty($items['model']->data)) {
                            $model->name = $items['vendor']->data." ".$items['model']->data;
                        }
                        
                        if (empty($model->name)) {
                            var_dump([$elem,$items]);
                        } else {
                            echo (($model->isNewRecord) ? "Added " : "Updated").": ".$model->name.PHP_EOL;
                            $model->save();
                        }
                    }
                }
            }, true);

            $parser->run();
            unset($parser);
            $source->status_time = new CDbExpression("NOW()");
            $source->status_message = "Файл успешно импортирован.";
            $source->status = 2;
            $source->save();
        }
    }
    
    public function actionList()
    {
        $listFile = "/inktomia/db/analogindex/yml/lists/1.yml";
        
        $parser = new YmlParser($listFile);
        $parser->registerEvent("list.adv", function($elem, $items){
            
            $model = (YmlSources::model()->countByAttributes(["url"=>$items['url']->data])) 
                    ? YmlSources::model()->findByAttributes(["url"=>$items['url']->data]) :
                    new YmlSources();
            $model->last_updated = $items['last_update']->data;
            $model->url = $items['url']->data;
            $model->name = $items['name']->data;
            $model->save();
            
        }, true);
        $parser->run();
    }
}