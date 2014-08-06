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

    
    
    public function actionGoods()
    {
        $search = (isset($_GET['search']) && !empty($_GET['search'])) ? htmlspecialchars($_GET['search']) : null;
        $page = isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
        $model = new GoodsModel();
        $count = $model->getCount($search);
        $pages = ceil($count/50);
        $goods = $model->getForAdmin($page, $search);
        $this->render("goods", array(
            'goods'=>$goods, 
            "maxPages"=>$pages, 
            "currentPage" =>$page,
            'search'=>$search,
        ));
    }

    public function actionGoodsEdit($id)
    {
        $model = new GoodsModel();
        $data = $model->getForEdit($id);
        if (!$data)
            throw new CHttpException(404, "Товар не найден");
        
        $data['videos'] = $model->sql->createCommand("select * from videos where goods = :id and lang = :lang order by priority desc")->queryAll(true, array('id'=>$id, 'lang'=>Yii::app()->language));
        $data['youtube'] = array();
        require_once Yii::app()->basePath.'/extensions/google-api-php-client/src/Google_Client.php';
        require_once Yii::app()->basePath.'/extensions/google-api-php-client/src/contrib/Google_YouTubeService.php';
        $client = new Google_Client();
        $client->setDeveloperKey("AIzaSyCm5k_ScE8R_WiSyEBOc3xWGM9oXFg2RRI");
        $youtube = new Google_YoutubeService($client);
        try
        {
            $searchResponse = $youtube->search->listSearch('id', array(
                'q' => $data['brand']['name']. " " .$data['goods']['name']." ".Yii::t('goods', "Обзор телефона"),
                'maxResults' => 10,
                'regionCode' => (Yii::app()->language == 'ru') ? 'ru' : 'us',
            ));
        } catch (Exception $ex) {
            
        }
        
        if (isset($searchResponse['items']) && !empty($searchResponse['items']))
        {
            foreach ($searchResponse['items'] as $video)
            {
                if (isset($video['id']['videoId']))
                    $data['youtube'][] = $video['id']['videoId'];
            }
        }
        
        
        
        if (Yii::app()->language == "ru")
        {   
            
            $condition = "where d.name like :name";
            $params = array('name'=>$data['brand']['name']. " " .$data['goods']['name']);
            if ($data['synonims'])
            {
                foreach ($data['synonims'] as $key=>$synonim)
                {
                    $condition .= " OR d.name LIKE :name{$key}";
                    $params['name'.$key] = $data['brand']['name']. " ". $synonim['name'];
                }
            }
            $query = "select d.name, d.rating, r.title, r.content from destinations d inner join reviews r on d.id = r.destination {$condition}";
            $connection = Yii::app()->reviews;
            $data['new_reviews'] = $connection->createCommand($query)->queryAll(true, $params);
            
            
            $condition = "where d.name LIKE :name";
            $params = array('name'=>$data['brand']['name']. " " .$data['goods']['name']);
            if ($data['synonims'])
            {
                foreach ($data['synonims'] as $key=>$synonim)
                {
                    $condition .= " OR d.name LIKE :name{$key}";
                    $params['name'.$key] = $data['brand']['name']. " ". $synonim['name'];
                }
            }
            $query = "select q.question, d.name, q.answer from qa_devices d inner join qa_relations r on r.device = d.id inner join qa_questions q on r.question = q.id {$condition} and q.answer != ''";
            $data['new_questions'] = $connection->createCommand($query)->queryAll(true, $params);
        } else {
            $data['new_reviews'] = array();
            $data['new_questions'] = array();
        }
        
        $data['reviews'] = array();
        $data['questions'] = array();
        
        $this->render("goodsedit",array("data"=>$data));
    }

    public function actionImages()
    {
        
        $model = new GoodsModel();
        //print("Добавление изображений приостановлено.".PHP_EOL);
        if (isset($_POST['url']))
        {
            //die("Добавление изображений приостановлено.");
            $connection = Yii::app()->db;
            foreach ($_POST['url'] as $url)
            {
                $url = trim($url);
                if (empty($url))
                    continue;
                
                $connection->createCommand("insert into temp_images (image, url) values (:id, :url)")
                    ->execute(array('id'=>$_POST['id'], 'url'=>$url));
            }
        }
        $list = $model->getListNoImages(1);
        $this->render('images', array('list'=>$list));
    }
    
    public function actionCharacteristics()
    {
        $ru = array();
        if (isset($_POST['ru']))
        {
            foreach ($_POST['ru'] as $id=>$ruitem)
            {
                $ruitem = trim($ruitem);
                if (!empty($ruitem))
                    $ru[] = array(
                        'id'=>$id,
                        'name'=>$ruitem,
                    );
            }
        }
        
        $en  =array();
        if (isset($_POST['en']))
        {
            foreach ($_POST['en'] as $id=>$enitem)
            {
                $ruitem = trim($enitem);
                if (!empty($enitem))
                    $en[] = array(
                        'id'=>$id,
                        'name'=>$enitem,
                    );
            }
        }
        
        $model = new CharacteristicsModel();
        if (!empty($ru) || !empty($en))
            $model->updateTranslations(array('ru'=>$ru, 'en'=>$en));
        $list = $model->getListForTranslations();
        $this->render('characteristics', array('list'=>$list));
    }
}
