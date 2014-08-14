<?php
class SiteController extends Controller
{
    public function actionIndex()
    {
        $model = new GoodsModel();
        $this->setPageTitle("Analogindex");
        //echo Yii::t('main', 'Тест');
        //print_r($model->getWidgetList(1));
        $this->render("index");
    }
    
    public function actionGoods($language, $type, $brand, $link)
    {
        if ($link == 'www')
            Yii::app ()->request->redirect ("http://".str_replace("www.", "", $_SERVER['HTTP_HOST']));
        
        $linkPattern = "~[\w\d\-_]+~";
        if (!preg_match($linkPattern, $link) || !preg_match($linkPattern, $brand) || !preg_match($linkPattern, $type))
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));            
        
        $criteria = new CDbCriteria();
        $criteria->limit = "1";
        $criteria->compare("t.link", $type);
        
        $data = GoodsTypes::model()->cache(10)->with(array(
            "name",
            "page_goods"=>array(
                "on"=>"page_goods.link = '{$link}'",
            ),
            "page_goods.brand_data"=>array(
                "joinType"=>"INNER JOIN",
                "on"=>"brand_data.link = '{$brand}'",
            ),
            "page_goods.rating",
            "page_goods.videos",
            "page_goods.reviews",
            "page_goods.faq",
            "page_goods.images"=>array(
                "order"=>"images.priority desc",
                "on"=>"images.disabled = 0",
            ),
            "page_goods.synonims",
            "page_goods.images.image_data",
            "page_goods.images.image_data.resized_preview",
            "page_goods.images.image_data.resized_list",
        ))->find($criteria);
        
        if (!$data)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        
        $this->setPageTitle($data->page_goods->brand_data->name." ".$data->page_goods->name);

        /**
        
        if (Yii::app()->language == "ru")
        {
            $connection = Yii::app()->reviews;
            $data['reviews'] = $connection->createCommand($query)->queryAll(true, array('name'=>$data['manufacturer']. " " .$data['name']));
            $query = "select q.question, q.answer from qa_devices d inner join qa_relations r on r.device = d.id inner join qa_questions q on r.question = q.id where d.name = :name and q.answer != ''";
            $data['questions'] = $connection->createCommand($query)->queryAll(true, array('name'=>$data['manufacturer']. " " .$data['name']));
        } else {
            $data['reviews'] = array();
            $data['questions'] = array();
        }
        
        
        $data['videos'] = array();
        require_once Yii::app()->basePath.'/extensions/google-api-php-client/src/Google_Client.php';
        require_once Yii::app()->basePath.'/extensions/google-api-php-client/src/contrib/Google_YouTubeService.php';
        $client = new Google_Client();
        $client->setDeveloperKey("AIzaSyCm5k_ScE8R_WiSyEBOc3xWGM9oXFg2RRI");
        $youtube = new Google_YoutubeService($client);
        try
        {
            $searchResponse = $youtube->search->listSearch('id', array(
                'q' => $data['manufacturer']. " " .$data['name']." ".Yii::t('goods', "Обзор телефона"),
                'maxResults' => 3,
                'regionCode' => (Yii::app()->language == 'ru') ? 'ru' : 'us',
            ));
        } catch (Exception $ex) {
            
        }

        if (isset($searchResponse['items']) && !empty($searchResponse['items']))
        {
            foreach ($searchResponse['items'] as $video)
            {
                if (isset($video['id']['videoId']))
                    $data['videos'][] = $video['id']['videoId'];
            }
        }

        $this->setPageTitle($data['manufacturer']." ".$data['name']);
        $this->render("goods", array('data'=>$data));
         * 
         */
        $this->render("goods", array("data"=>$data));
    }
    
    
    public function actionLanguage($language)
    {
        $zones = Language::$zones;
        $url = Yii::app()->request->urlReferrer;
        $url = preg_replace("~http://(.*)analogindex.(\w+)/(.*)~", "http://$1analogindex.{$language}/$3", $url);
        Yii::app()->request->redirect($url);
    }

    public function actionDownload()
    {
        $id = intval(isset($_GET['id'])? $_GET['id'] : 0 );
        $filename = isset($_GET['filename']) ? trim($_GET['filename']) : "file";
        $filesModel = new FilesModel();
        $size = isset($_GET['size']) ? intval($_GET['size']) : null;
        $filesModel->send($id,$filename,$size);
    }
    
    public function actionSearch()
    {
        $keyword = isset($_GET['keyword']) ? empty($_GET['keyword']) ? 'test' : $_GET['keyword'] : 'test';
        $this->setPageTitle("Поиск товаров");
        $model = new GoodsModel();
        $searchCriteria = new stdClass();
        $searchCriteria->select = '*';
        $searchCriteria->query = '@full '.Yii::app()->search->EscapeString($keyword).'*';
        $searchCriteria->from = 'goods_index';
        $searchCriteria->paginator = null;
        $srch = Yii::App()->search;
        $srch->SetMaxQueryTime(300);
        $srch->setMatchMode(SPH_MATCH_EXTENDED2);
        $srch->SetRankingMode(SPH_RANK_SPH04);
        $resArray = $srch->searchRaw($searchCriteria); 
        $result = array_keys($resArray['matches']);
        /**if (!$result)
        {
            $srch->setMatchMode(SPH_MATCH_ALL);
            $srch->SetRankingMode(SPH_SORT_RELEVANCE);
            $searchCriteria->query = '@full '.Yii::app()->search->EscapeString($keyword).'*';
            $resArray = $srch->searchRaw($searchCriteria); 
            $result = array_keys($resArray['matches']);
        }**/
        $items = $model->sphinx($result);
        //var_dump($items);
        $this->render("search", array('items'=>$items));
    }
    
    public function actionTest()
    {
        $criteria = new CDbCriteria();
        //$criteria->order = "t.name";
        $brands = Brands::model()->with(array(
            "goods"=>array("order"=>"goods.id asc"),
            "description",
        ))->findAll($criteria);
        echo "<table border = 1>";
        echo "<tr><th>ID</th><th>link</th><th>name</th></tr>";
        foreach ($brands as $brand)
        {
            echo "<tr>";
            echo "<td>".$brand->id."</td>";
            echo "<td>".$brand->link ."</td>";
            echo "<td>".$brand->name."</td>";
            echo "</tr>";
            foreach($brand->goods as $goods)
            {
                echo "<tr><td colspan=3>{$goods->name}</td></tr>";
            }
        }
        echo "</table>";
    }
    
    
    public function actionTest2()
    {
        exit();
        $model = new Goods();
        $transaction = $model->getDbConnection()->beginTransaction();
        try 
        {
            $model->brand_data = new Brands();
            $model->brand_data->name = "Test";
            $model->brand_data->link = "test";

            $model->name = "Test";
            $model->link = "test";
            $model->type = 1;

            if ($model->brand_data->save())
            {
                $model->brand = $model->brand_data->id;

                $model->save();
                $transaction->commit();
            }
        } catch (Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
    }
}

