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
        $criteria->condition = "status = 1";
        
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
                //var_dump([$elem,$items]);
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