<?php
class AdminController extends Controller
{
    public $layout = "admin";
    
    public function filters()
    {
        return array(
            'accessControl',
        );
    }
    
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions'=>array('login'),
                'users'=>array('?'),
            ),
            array(
                'allow',
                'users'=>array('@'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
            
        );
    }
    
    public function actionLogin()
    {
        if (isset($_POST['username']) && isset($_POST['password']))
        {
            $identity=new UserIdentity($_POST['username'],$_POST['password']);
            if($identity->authenticate())
            {
                Yii::app()->user->login($identity);
                Yii::app()->request->redirect(Yii::app()->createUrl("admin/index"));
            }
            else
                echo $identity->errorMessage;
        }
        $this->render('loginForm');
    }
    
    public function actionLogout()
    {
        Yii::app()->user->logout();
        Yii::app()->request->redirect(Yii::app()->createUrl("admin/login"));
    }
    
    public function actionIndex()
    {
        $this->render("index");
    }
    
    public function actionGoods($type = null, $brand=null, $search=null)
    {
        $filters = array(
            "view"=>array(),
            "controller"=>array(
                'language'=>Yii::app()->language,
            ),
        );
        $goodsCriteria = new CDbCriteria();
        $goodsCriteria->order = "brand_data.name asc, t.name asc";
        
        if ($search !== null)
        {
            $search = trim($search);
            $search = htmlspecialchars($search);
            if (!empty($search))
            {
                $filters['controller']['search'] = $search;
                $filters['view']['Поиск'] = $search;
            }
        }
        
        $typesCriteria = new CDbCriteria();
        $typesCriteria->order = "name.name asc";
        
        $types = GoodsTypes::model()->with(array("name"))->findAll($typesCriteria);
        
        $brandsCriteria = new CDbCriteria();
        $brandsCriteria->order = "t.name asc";
        if ($brand !== null)
        {
            $brand = intval($brand);
            $goodsCriteria->addCondition("t.brand = :brand");
            $goodsCriteria->params['brand'] = $brand;
            $brand = Brands::model()->findByPk($brand);
            $filters["view"]["Производитель"] = $brand->name;
            $filters["controller"]["brand"] = $brand->id;
        }
        if ($type !== null)
        {
            $type = intval($type);
            $goodsCriteria->addCondition("t.type = :type");
            $goodsCriteria->params['type'] = $type;
            $type = GoodsTypes::model()->with(array("name"))->findByPk($type);
            $filters["view"]["Тип"] = $type->name->name;
            $filters["controller"]["type"] = $type->id;
            $brands = Brands::model()->with(array(
                "goods"=>array(
                    "select"=>false,
                    "joinType"=>"INNER JOIN",
                    "on"=>"goods.type = :type",
                    "params"=>array("type"=>$type->id),
                    //"group"=>"goods.brand",
                ),
            ))->findAll($brandsCriteria);
        } else {
            $brands = Brands::model()->findAll($brandsCriteria);
        }
        
        if (isset($search) && !empty($search))
        {
            $goodsCriteria->addCondition("CONCAT(brand_data.name, ' ', t.name) LIKE :name");
            $goodsCriteria->params['name'] = '%'.str_replace("&nbsp;", " ", $search).'%';
        }
        
        $goodsCount = $goods = Goods::model()->with(array(
            "brand_data"=>array(
                "joinType"=>"INNER JOIN",
            ),
        ))->count($goodsCriteria);
                
        $pages = new CPagination($goodsCount);
        $pages->setPageSize(25);
        $pages->applyLimit($goodsCriteria);
        
        $goods = Goods::model()->with(array(
            "brand_data"=>array(
                "joinType"=>"INNER JOIN",
            ),
        ))->findAll($goodsCriteria);
        
        
        $this->render("goods", array(
            "types"=>$types,
            "brands"=>$brands,
            "goods"=>$goods,
            "goodsCount"=>$goodsCount,
            "pages"=>$pages,
            "filters"=>$filters,
        ));
    }
    
    public function actionEditgoods($id)
    {
        $data = Goods::model()->findByPk($id);
        if (!$data)
            throw new CHttpException(404, "Товар не найден");
        
        $typesCriteria = new CDbCriteria();
        $typesCriteria->order = "name.name asc";
        $types = GoodsTypes::model()->with(array("name"))->findAll($typesCriteria);
        
        $this->render("edit_goods", array(
            "data"=>$data,
            "types"=>$types,
        ));
    }
}
