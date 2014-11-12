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
    
    public function actionSearch($query) 
    {

        $searchCriteria = new stdClass();
        $search = Yii::app()->search;



        $pages = new CPagination(10000000000000);
        $pages->pageSize = 10000000000000;
        

        $searchCriteria->paginator = $pages;
       
        $searchCriteria->from = 'yml_index';
        try {
            $query = $search->escape($query);
            $searchCriteria->query = "{$query}";
            $pages->applyLimit($searchCriteria);
            $search->setMatchMode(SPH_MATCH_EXTENDED2);
            $resIterator = $search->search($searchCriteria); // interator result
        } catch (Exception $ex) {
            // Не пашет sphinx;
        }
        $items = [];
        if (!empty($resIterator) && $resIterator->getTotal()) {
            $pages->setItemCount($resIterator->getTotalFound());
            $criteria = new CDbCriteria();
            $criteria->addInCondition("id", $resIterator->getIdList());
            $items = YmlItems::model()->findAll($criteria);
        }

        $this->render("search", ["items" => $items, "pages" => $pages]);
    }
}