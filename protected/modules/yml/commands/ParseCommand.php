<?php
class ParseCommand extends CConsoleCommand
{

    public function beforeAction($action, $params)
    {
        Yii::import("application.modules.yml.components.YmlParser");
        Yii::import("application.modules.yml.models.*");
        return parent::beforeAction($action, $params);
    }

    public function actionIndex()
    {
        //yml_catalog.shop.categories
        //yml_catalog.shop.offers.offer
        //
        
        $ymlFile = "/inktomia/db/analogindex/yml/catalogs/1.yml";
        $parser = new YmlParser($ymlFile);
        

        $parser->registerEvent("yml_catalog.shop.categories.category", function($elem, $items){
            var_dump([$elem,$items]);
        }, false);
        
        
        $parser->registerEvent("yml_catalog.shop.offers.offer", function($elem, $items){
            var_dump([$elem,$items]);
           
        }, true);

        $parser->run();
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