<?php

class SiteController extends Controller
{

    public function actions()
    {
        return array(
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
                'transparent' => true,
                'testLimit' => 1,
                'foreColor' => 0x999999,
                'minLength' => 3,
                'maxLength' => 5,
                'offset' => 1,
            ),
        );
    }

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'goods', 'language'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array("brand"),
                'users' => array('?'),
            )
        );
    }

    public function actionIndex()
    {
        $this->setPageTitle("Analogindex");
        $this->layout = 'materialize';
        $this->render("index");
    }
    
    
    public function actionGoods($language, $type, $brand, $link)
    {
        
        $criteria = new CDbCriteria();
        $criteria->condition = "t.link = :link and brand_data.link = :brand";
        $criteria->params = array("link" => $link, "brand" => $brand);
        $product = Goods::model()->cache(60 * 60 * 6)->with(['brand_data', 'type_data'])->find($criteria);
        
        if (!$product)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));

        if ($product->type_data->link !== $type) {
            $redirect = "/{$product->type_data->link}/{$brand->link}/{$product->link}.html";
            Redirects::model()->deleteAllByAttributes(['from' => $redirect]);
            Yii::app()->request->redirect($redirect, true, 302);
        }

        $relatedArticles = [];
        $relatedCounts = [];
        $articlesCriteria = new CDbCriteria();
        $articlesCriteria->select = 't.id, t.description, t.created, t.title, t.link, t.type, t.lang';
        $articlesCriteria->condition = 't.lang = :lang and t.type = :type and product.goods = :product';
        $articlesCriteria->limit = 5;
        
        foreach (Articles::model()->related as $type=>$null) {
            $articlesCriteria->params = [
                'lang' => Yii::app()->language,
                'type' => $type,
                'product' => $product->id,
            ];
            Yii::app()->sourceLanguage = (Yii::app()->language == 'en') ? 'ru' : 'en';
            $relatedCounts[$type]['title'] = Yii::t('articles', $type . '-many');
            $relatedCounts[$type]['more'] = Yii::t('articles', $type . '-readall');
            $relatedCounts[$type]['count'] = Articles::model()->cache(60*60*24)->with(['product'])->count($articlesCriteria);
            Yii::app()->sourceLanguage = (Yii::app()->language == 'en') ? 'ru' : 'en';
            
            $relatedArticles[$type] = Articles::model()->cache(60*60*6)->with(['product'])->findAll($articlesCriteria);
        }
        
        $this->setPageTitle($product->type_data->name->item_name . " " . $product->fullname);
        $this->addKeywords(array($product->type_data->name->item_name, $product->brand_data->name, $product->name));

        foreach ($product->type_data->keywords as $keyword) {
            $this->addKeyword($keyword->keyword);
        }

        foreach ($product->getGeneralCharacteristics() as $characteristic) {
            $this->addDescription($characteristic['characteristic_name'] . " " . $characteristic['value']);
        }
        $this->scripts[] = 'jquery.lockfixed.min.js';
        $this->scripts[] = 'floating-div.js';
        $this->scripts[] = 'gallery.js';
        $this->scripts[] = 'product.js';
        $this->layout = 'materialize';
        $this->breadcrumbs = [
            [
                'url' => 'http://analogindex.' . Language::getCurrentZone() . '/',
                'title' => Yii::t('main', 'Главная'),
            ],
            [
                'url' => Yii::app()->createAbsoluteUrl("site/type", array("type" => $product->type_data->link, "language" => Language::getCurrentZone())),
                'title' => $product->type_data->name->name,
            ],
            [
                'url' => Yii::app()->createAbsoluteUrl("site/brand", array(
                    "link" => $product->brand_data->link,
                    "language" => Language::getCurrentZone(),
                    "type" => $product->type_data->link,
                )),
                'title' => $product->brand_data->name,
            ],
            [
                'url' => Yii::app()->createAbsoluteUrl("site/goods", array(
                    "link" => $product->link,
                    "brand" => $product->brand_data->link,
                    "language" => Language::getCurrentZone(),
                    "type" => $product->type_data->link,
                )),
                'title' => $product->fullname,
        ]];
        
        $images = array_map(function($value) {
            return $value->image_data;
        }, $product->cache(60*5)->gallery([
            'limit' => Goods::IMAGES_LIMIT, 
            'with' => ['image_data'],
            'order' => 'gallery.id asc',
        ]));
        $firstImage = reset($images);

        $characteristics = $product->getCharacteristics();
        $characteristicsLinks = new CharacteristicsLinks($characteristics);
        $characteristics = $characteristicsLinks->getCharacteristics($product->type_data->link);
      
        
        $criteria = new CDbCriteria();
        $criteria->order = "priority desc";
        $criteria->condition = "t.goods = :id and t.lang = :lang";
        $criteria->params = ['id' => $product->id, 'lang' => Yii::app()->language];
        $criteria->limit = 3;
        $relatedVideos = Videos::model()->cache(60*60*24)->findAll($criteria);
        
        
        
        $this->render("product", array(
            'firstImage'=>$firstImage,
            'images'=>$images,
            'characteristics'=>$characteristics,
            'product' => $product,
            'relatedArticles' => $relatedArticles,
            'relatedCounts' => $relatedCounts,
            'relatedVideos' => $relatedVideos,
        ));
    }

    public function actionLanguage($language)
    {
        $zones = Language::$zones;
        $url = Yii::app()->request->urlReferrer;
        $url = preg_replace("~http://(.*)analogindex.(\w+)/(.*)~", "http://analogindex.{$language}/$3", $url);
        Yii::app()->request->redirect($url);
    }

    public function actionDownload()
    {
        $id = intval(isset($_GET['id']) ? $_GET['id'] : 0 );
        $filename = isset($_GET['filename']) ? trim($_GET['filename']) : "file";
        $filesModel = new FilesModel();
        $size = isset($_GET['size']) ? intval($_GET['size']) : null;
        $filesModel->send($id, $filename, $size);
    }

    public function actionBrand($link, $language, $type = null)
    {
        $brand = Brands::model()->cache(60 * 60 * 24)->findByAttributes(array("link" => $link));
        if (!$brand)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        if ($type !== null) {
            $type = GoodsTypes::model()->cache(60 * 60 * 24)->findByAttributes(array("link" => $type));
            if (!$type)
                throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        }

        $criteria = new CDbCriteria();
        $criteria->compare("t.brand", $brand->id);
        if (isset($type->id)) {
            $criteria->compare("t.type", $type->id);
        }
        $goodsCount = Goods::model()->cache(60 * 60 * 48)->count($criteria);
        $criteria->limit = 12;
        $criteria->order = "t.updated desc";
        $pages = new CPagination($goodsCount);
        $pages->setPageSize(12);
        $pages->applyLimit($criteria);
        $goods = Goods::model()->cache(60 * 60 * 2)->with([
                    'type_data' => [
                        "joinType" => "inner join",
                    ],
                    'type_data.name',
                    //'primary_image',
                    //'primary_image.image_data',
                ])->findAll($criteria);
        $this->pageTitle = $brand->name . (isset($type->name->name) ? ': ' . $type->name->name : null);
        $this->layout = 'materialize';

        if (isset($type->link)) {
            $breadcrumb = [
                'url'=>Yii::app()->createAbsoluteUrl('site/type', ['type'=>$type->link, 'language'=>Language::getCurrentZone()]),
                'title'=>$type->name->name,
            ];
        } else {
            $breadcrumb = [
                'title'=>Yii::t("main", "Производители"),
                'url'=>'#',
            ];
        }
        
        $this->breadcrumbs = [
            [
                'url'=>'http://analogindex.'.Language::getCurrentZone(),
                'title'=> Yii::t('main', 'Главная'),
            ],
            $breadcrumb,
            [
                'url'=>Yii::app()->request->requestUri,
                'title'=>$brand->name,
            ]
        ];
        
        
        
        $this->render("brand", array(
            "brand" => $brand,
            "goods" => $goods,
            "pages" => $pages,
            "type_selected" => isset($type->link) ? $type : null,
        ));
    }

    public function actionSearch()
    {
        $query = Yii::app()->request->getParam("keyword");
        $paramType = Yii::app()->request->getParam("type");
        $searchCriteria = new stdClass();
        $search = Yii::app()->search;



        $pages = new CPagination(10000000000000);
        $pages->pageSize = 12;
        //$searchCriteria->select = 'id';
        if ($paramType)
            $searchCriteria->filters = array('type' => $paramType);

        $searchCriteria->paginator = $pages;
        //$searchCriteria->groupby = $groupby;
        //$searchCriteria->orders = array('f_name' => 'ASC');
        $searchCriteria->from = 'goods_index';
        try {
            $query = $search->escape($query);
            $searchCriteria->query = '@name ' . $query;
            $pages->applyLimit($searchCriteria);
            $resIterator = $search->search($searchCriteria); // interator result
        } catch (Exception $ex) {
            // Не пашет sphinx;
        }

        $goods = array();

        if (!empty($resIterator) && $resIterator->getTotal()) {
            $pages->setItemCount($resIterator->getTotalFound());
            $criteria = new CDbCriteria();
            $criteria->addInCondition("t.id", $resIterator->getIdList());
            //$criteria->group = "t.id, rating.value";
            $goods = Goods::model()->with(array(
                        "brand_data" => array(
                            "joinType" => "inner join"
                        ),
                        "primary_image",
                        //"rating"
                    ))->findAll($criteria);
        }
        
        $this->layout = 'materialize';
        $this->breadcrumbs = [
            [
                'title'=>Yii::t("main", "Главная"),
                'url'=>'http://analogindex.'.Language::getCurrentZone(),
            ],
            [
                'title'=>Yii::t("main", "Поиск"),
                'url'=>Yii::app()->request->requestUri,
            ]
        ];
        $this->render("search", array("goods" => $goods, "pages" => $pages));
    }

    public function actionType($type, $brands = array(), $os = array(), $screensizes = array(), $cores = array(), $cpufreq = array(), $ram = array(), $processor = array(), $gpu = array())
    {
        $this->layout = 'materialize';
        $this->scripts[] = 'grid-to-list.js';
        
        $brands = !empty($brands) ? explode(".", $brands) : array();
        $os = !empty($os) ? explode(".", $os) : array();
        $screensizes = !empty($screensizes) ? explode(".", $screensizes) : array();
        $cores = !empty($cores) ? explode(".", $cores) : array();
        $cpufreq = !empty($cpufreq) ? explode(".", $cpufreq) : array();
        $ram = !empty($ram) ? explode(".", $ram) : array();
        $processor = !empty($processor) ? explode(".", $processor) : array();
        $gpu = !empty($gpu) ? explode(".", $gpu) : array();

        $urlOptions = array(
            "language" => Language::getCurrentZone(),
            "type" => $type,
        );

        $typeString = $type;
        if (!$typeModel = GoodsTypes::model()->findByAttributes(array("link" => $typeString)))
            throw new CHttpException(404, "Страница не найдена");

        $type = $typeModel->id;

        if ($filterBrands = Yii::app()->request->getPost("brands")) {
            array_multisort($filterBrands);
            $filterBrands = array_unique($filterBrands);
            $urlOptions['brands'] = implode(".", $filterBrands);
        }

        if ($filterOs = Yii::app()->request->getPost("os")) {
            array_multisort($filterOs);
            $filterOs = array_unique($filterOs);
            $urlOptions['os'] = implode(".", $filterOs);
        }

        if ($filterScreenSizes = Yii::app()->request->getPost("screensizes")) {
            array_multisort($filterScreenSizes);
            $filterScreenSizes = array_unique($filterScreenSizes);
            $urlOptions['screensizes'] = implode(".", $filterScreenSizes);
        }

        if ($filterCores = Yii::app()->request->getPost("cores")) {
            array_multisort($filterCores);
            $filterCores = array_unique($filterCores);
            $urlOptions['cores'] = implode(".", $filterCores);
        }

        if ($filterCpuFreq = Yii::app()->request->getPost("cpufreq")) {
            array_multisort($filterCpuFreq);
            $filterCpuFreq = array_unique($filterCpuFreq);
            $urlOptions['cpufreq'] = implode(".", $filterCpuFreq);
        }

        if ($filterRam = Yii::app()->request->getPost("ram")) {
            array_multisort($filterRam);
            $filterRam = array_unique($filterRam);
            $urlOptions['ram'] = implode(".", $filterRam);
        }

        if ($filterProcessor = Yii::app()->request->getPost("processor")) {
            array_multisort($filterProcessor);
            $filterProcessor = array_unique($filterProcessor);
            $urlOptions['processor'] = implode(".", $filterProcessor);
        }

        if ($filterGpu = Yii::app()->request->getPost("gpu")) {
            array_multisort($filterGpu);
            $filterGpu = array_unique($filterGpu);
            $urlOptions['gpu'] = implode(".", $filterGpu);
        }

        if (count($urlOptions) > 2) {
            $url = Yii::app()->createUrl("site/type", $urlOptions);
            Yii::app()->request->redirect($url);
            exit();
        }

        $brandsCriteria = new CDbCriteria();
        $brandsCriteria->order = "t.name asc";
        $brandsCriteria->group = "t.id";
        $brandsList = Brands::model()->with(array("goods" => array(
                        "joinType" => "inner join",
                        "on" => "goods.type = :type",
                        "params" => array("type" => $type),
                        "select" => array("name", "link"),
                    //"condition"=>"goods.id = null",
            )))->findAll($brandsCriteria);



        $osList = Os::model()->findAll(array(
            "order" => "t.name asc",
            "select" => array("name", "link"),
        ));

        $processorList = Processors::model()->findAll(array(
            "order" => "t.name asc",
            "select" => array("name", "link"),
        ));

        $gpuList = Gpu::model()->findAll(array(
            "order" => "t.name asc",
            "select" => array("name", "link"),
        ));

        $screenSizesList = array(
            (object) array(
                "name" => " до 5 дюймов",
                "link" => "0-5",
            ),
            (object) array(
                "name" => " от 5 до 7 дюймов",
                "link" => "5-7",
            ),
            (object) array(
                "name" => " от 7 до 10 дюймов",
                "link" => "7-10",
            ),
            (object) array(
                "name" => " больше 10 дюймов",
                "link" => "10plus",
            ),
        );

        $coresList = array(
            (object) array(
                "name" => "1 ядро",
                "link" => "1",
            ),
            (object) array(
                "name" => "2 ядра",
                "link" => "2",
            ),
            (object) array(
                "name" => "3 и более",
                "link" => "3plus",
            ),
        );

        $cpuFreqList = array(
            (object) array(
                "name" => "до 1 Ггц",
                "link" => "1",
            ),
            (object) array(
                "name" => "от 1 до 2 Ггц",
                "link" => "2",
            ),
            (object) array(
                "name" => "2 Ггц и более",
                "link" => "2plus",
            ),
        );

        $ramList = array(
            (object) array(
                "name" => "до 512 Мб",
                "link" => "512",
            ),
            (object) array(
                "name" => "от 512 Мб до 1 Гб",
                "link" => "512-1024",
            ),
            (object) array(
                "name" => "от 1 Гб до 2 Гб",
                "link" => "1024-2048",
            ),
            (object) array(
                "name" => "от 2 Гб до 4 Гб",
                "link" => "2048-4096",
            ),
            (object) array(
                "name" => "более 4 Гб",
                "link" => "4096plus",
            ),
        );



        $criteria = new CDbCriteria();
        $criteria->addCondition("type = {$type}");

        if (!empty($brands)) {
            $criteria->addInCondition("brand", $brands);
        }

        if (!empty($os)) {
            $criteria->addInCondition("os", $os);
        }

        if (!empty($screensizes)) {
            $criteria->addInCondition("screensize", $screensizes);
        }

        if (!empty($cores)) {
            $criteria->addInCondition("cores", $cores);
        }

        if (!empty($cpufreq)) {
            $criteria->addInCondition("cpufreq", $cpufreq);
        }

        if (!empty($ram)) {
            $criteria->addInCondition("ram", $ram);
        }

        if (!empty($processor)) {
            $criteria->addInCondition("processor", $processor);
        }

        if (!empty($gpu)) {
            $criteria->addInCondition("gpu", $gpu);
        }

        $goodsSelector = CharacteristicsSelector::model()->cache(60 * 60)->findAll($criteria);
        $goodsSelector = CHtml::listData($goodsSelector, "id", "id");

        $criteria = new CDbCriteria();
        $criteria->addInCondition("t.id", $goodsSelector);
        $criteria->addCondition("t.type = {$type}");
        $pages = new CPagination(Goods::model()->cache(60 * 60)->count($criteria));
        $pages->pageSize = 12;
        $pages->applyLimit($criteria);
        //$criteria->group = "t.id, rating.value";
        $goods = Goods::model()->cache(60 * 60)->with(array(
                    "brand_data" => array(
                        "joinType" => "inner join"
                    ),
                    //"primary_image",
                    //"rating"
                ))->findAll($criteria);

        $this->breadcrumbs = [
            [
                'url'=>'http://analogindex.'.Language::getCurrentZone(),
                'title'=>Yii::t("main", "Главная"),
            ],
            [
                'url'=>Yii::app()->createAbsoluteUrl('site/type', ['type'=>$typeModel->link, 'language'=>Language::getCurrentZone()]),
                'title'=>$typeModel->name->name,
            ]
        ];
        $this->setPageTitle($typeModel->name->name);
        $this->render("type", array(
            "brands" => $brandsList,
            "os" => $osList,
            "screenSizes" => $screenSizesList,
            "cores" => $coresList,
            "cpufreq" => $cpuFreqList,
            "ram" => $ramList,
            "processor" => $processorList,
            "gpu" => $gpuList,
            //////////
            "brandsSelected" => $brands,
            "osSelected" => $os,
            "screenSizesSelected" => $screensizes,
            "coresSelected" => $cores,
            "cpuFreqSelected" => $cpufreq,
            "ramSelected" => $ram,
            "processorSelected" => $processor,
            "gpuSelected" => $gpu,
            /////////////
            "goods" => $goods,
            "pages" => $pages,
            "type"=>$typeModel,
        ));
    }

    public function actionTest()
    {

        $criteria = new CDbCriteria();
        $criteria->condition = "t.lang = :lang";
        $criteria->params = ['lang'=>Yii::app()->language];
        $criteria->order = "t.key";
        
        $categories = SpecificationsCategories::model()->with(['specifications', 'specifications.value'=>['on'=>'value.goods = 10562']])->findAll($criteria);
        
        foreach ($categories as $category) {
            echo $category->name."<br />";
            foreach($category->specifications as $specification) {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;".$specification->key." ".$specification->name.(isset($specification->value->raw) ? ":::".$specification->value->raw : '')."<br />";
            }
        }
    }

}
