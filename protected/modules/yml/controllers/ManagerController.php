<?php


class ManagerController extends CController
{
    public $layout = "yml";

    public function actionIndex($catalog = null)
    {
        set_time_limit(300);
        
        if (!$catalog) {
            $data = [];
        } else {
            if (Yii::app()->request->getPost("catalogs-save")) {
                YmlCatalog::model()->setChecked($catalog, (array) Yii::app()->request->getPost("catalogs"));
                YmlSources::model()->updateByPk($catalog, ["status"=>1]);
            }
            $data = YmlCatalog::model()->getTree($catalog);
        }
        
        $sources = YmlSources::model()->getList();
        
        $this->render("tree", ["data"=>$data, "sources"=>$sources, "catalog"=>$catalog]);
    }
}