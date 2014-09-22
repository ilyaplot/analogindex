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
                'roles'=>array(Users::ROLE_ADMIN),
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
        $errors = array();
        $success = array();
        $newSynonims = isset($_POST['newsynonims']) ? $_POST['newsynonims'] : null;
        $goodsParams = isset($_POST['Goods']) ? $_POST['Goods'] : null;
        $modificationsComments = isset($_POST['ModificationsComments']) ? $_POST['ModificationsComments'] : null; 
        $deleteModifications = isset($_POST['DeleteModifications']) ? $_POST['DeleteModifications'] : null;
        $newModifications = isset($_POST['newmodifications']) ? $_POST['newmodifications'] : null;
        // Добавление синонимов
        if (is_array($newSynonims))
        {
            foreach ($newSynonims as $key=>$synonim)
            {
                if (empty($synonim))
                    continue;
                $model = new GoodsSynonims();
                $model->goods = isset($_POST['Goods']['id']) ? $_POST['Goods']['id'] : null;
                $model->name = trim(htmlspecialchars($synonim));
                $model->visibled = isset($_POST['newsynonimscheck'][$key]) ? true : false;
                if ($model->validate())
                {
                    $model->save();
                    $success[] = "Добавлен синоним {$model->name}.";
                } else {
                    $error = $model->getErrors();
                    foreach ($error as $field=>$e)
                    {
                        $errors[] = "Произошла ошибка в поле <strong>".$model->getAttributeLabel($field)."</strong>: ". implode(", ", $e);
                    }
                }
            }
        }
        
        //Комментарии модификаций
        if (is_array($modificationsComments))
        {
            foreach ($modificationsComments as $commentId=>$commentText)
            {
                $comment = ModificationsComments::model()->findByPk($commentId);
                if (!$comment)
                    continue;
                $comment->comment = htmlspecialchars(trim($commentText));
                if (!$comment->validate())
                {
                    $error = $comment->getErrors();
                    foreach ($error as $field=>$e)
                    {
                        $errors[] = "Произошла ошибка в поле <strong>".$comment->getAttributeLabel($field)."</strong>: ". implode(", ", $e);
                    }
                } else {
                    $comment->save();
                }
            }
        }
        
        $data = Goods::model()->findByPk($id);
        // Созранение изменений товара
        if (is_array($goodsParams))
        {
            $data->type = isset($goodsParams['type']) ? $goodsParams['type'] : null;
            $data->brand = isset($goodsParams['brand']) ? $goodsParams['brand'] : null;
            $data->link = isset($goodsParams['link']) ? $goodsParams['link'] : null;
            $data->name = isset($goodsParams['name']) ? $goodsParams['name'] : null;
            $data->is_modification = isset($goodsParams['is_modification']) ? true : false;
            if ($data->validate())
            {
                $data->save();
                $success[] = "Изменения товара были сохранены.";
             } else {
                $error = $data->getErrors();
                foreach ($error as $field=>$e)
                {
                    $errors[] = "Произошла ошибка в поле <strong>".$data->getAttributeLabel($field)."</strong>: ". implode(", ", $e);
                }
            }
        }
        
        // Удаление модификаций
        if (is_array($deleteModifications))
        {
            foreach ($deleteModifications as $deleteModification=>$tmp)
            {
                $modification = GoodsModifications::model()->findByAttributes(array("id"=>$deleteModification, "goods_parent"=>$id));
                if (!$modification)
                    continue;
                Goods::model()->updateByPk($modification->goods_children, array('is_modification'=>false));
                $modification->delete();
                ModificationsComments::model()->deleteAllByAttributes(array("modification"=>$deleteModification));
                $success[] = "Модификация была удалена.";
            }
        }
        
        // Добавление модификаций
        if (is_array($newModifications))
        {
            foreach ($newModifications as $goods=>$newMod)
            {
                if (isset($newMod['merge']))
                {
                    $modification = new GoodsModifications();
                    $modification->goods_parent = $id;
                    $modification->goods_children = $goods;
                    
                    if ($modification->validate())
                    {
                        $modification->save();
                        $ruComment = new ModificationsComments();
                        $ruComment->modification = $modification->id;
                        $ruComment->lang = 'ru';
                        $ruComment->comment = trim(htmlspecialchars(isset($newMod['ru']) ? $newMod['ru'] : null));
                        if ($ruComment->validate())
                        {
                            $ruComment->save();
                        } else {
                            $modification->delete();
                            $error = $ruComment->getErrors();
                            foreach ($error as $field=>$e)
                            {
                                $errors[] = "Произошла ошибка в поле <strong>".$ruComment->getAttributeLabel($field)."</strong>: ". implode(", ", $e);
                            }
                            continue;
                        }
                        $enComment = new ModificationsComments();
                        $enComment->modification = $modification->id;
                        $enComment->lang = 'en';
                        $enComment->comment = trim(htmlspecialchars(isset($newMod['en']) ? $newMod['en'] : null));
                        if ($enComment->validate())
                        {
                            $enComment->save();
                        } else {
                            $modification->delete();
                            $error = $enComment->getErrors();
                            foreach ($error as $field=>$e)
                            {
                                $errors[] = "Произошла ошибка в поле <strong>".$enComment->getAttributeLabel($field)."</strong>: ". implode(", ", $e);
                            }
                            continue;
                        }
                        Goods::model()->updateByPk($goods, array('is_modification'=>true));
                        $success[] = "Была добавлена модификация товара.";
                    } else {
                        $error = $modification->getErrors();
                        foreach ($error as $field=>$e)
                        {
                            $errors[] = "Произошла ошибка в поле <strong>".$modification->getAttributeLabel($field)."</strong>: ". implode(", ", $e);
                        }
                    }
                }
            }
        }
        
        if (!$data)
            throw new CHttpException(404, "Товар не найден");
        
        $typesCriteria = new CDbCriteria();
        $typesCriteria->order = "name.name asc";
        $types = GoodsTypes::model()->with(array("name"))->findAll($typesCriteria);
        
        
        $this->render("edit_goods", array(
            "data"=>$data,
            "types"=>$types,
            "errors"=>$errors,
            "success"=>$success,
        ));
    }
    
    public function actionAjaxModifications($search, $type, $exclude)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 
                "(CONCAT(brand_data.name, ' ', t.name) LIKE :search OR CONCAT(brand_data.name, ' ', synonims.name) LIKE :search) ".
                "AND t.id NOT IN ({$exclude}) AND t.type = :type";
        $search = trim($search);
        $search = htmlspecialchars($search);
        $search = str_replace("&nbsp;", " ", $search);
        $search = "%{$search}%";
        $criteria->params = array(
            "search"=>$search,
            "type"=>$type,
        );
        $results = Goods::model()->with("brand_data","synonims")->findAll($criteria);
        $return = array();
        foreach ($results as $result)
        {
            $synonims = array();
            foreach ($result->synonims as $synonim)
                $synonims[] = $synonim->name;
            
            $return[] = array(
                "id"=>$result->id,
                "name"=>$result->name,
                "brand"=>$result->brand_data->name,
                "synonims" => implode(", <br />", $synonims),
            );
            
            
            
        }
        $this->layout = "empty";
        $this->render("_ajax_modifications", array("data"=>$return));
    }
}
