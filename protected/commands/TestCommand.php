<?php

class TestCommand extends CConsoleCommand
{

    

    public function actionReviewsBack()
    {
        $reviews = Reviews::model()->findAll();
        foreach ($reviews as $review) {
            $review->content = $review->original;
            $review->save();
            echo ".";
        }
        echo PHP_EOL;
    }

    public function actionLink()
    {
        echo CHtml::link(123, Yii::app()->createUrl("site/goods", array(
                    'link' => "test",
                    'brand' => "brand",
                    'type' => "pda",
                    'language' => Language::getZoneForLang('ru'),
        )));
    }
    
    
    public function actionGsmarena()
    {
        $id = "http://www.gsmarena.com/amazon_kindle_fire_hd_8_9_lte-4996.php";
        $task = SourcesGsmarena::model()->with("file_data")->findByAttributes(array("url"=>$id));
        
        if (!$task) {
            echo "No tasks for parse" . PHP_EOL;
            exit();
        }
        
        $content = $task->file_data->getContent();
        
        if (!$content) {
            
            
            echo "Null content for task" . PHP_EOL;
            exit();
        }
        
        $html = phpQuery::newDocumentHTML($content);
        // Производитель

        $brand = trim(str_replace(" phones", "", pq($html)->find("#all-phones h2 a")->text()));
        
        
        if (!$brand)
            $brand = trim(str_replace(" phones", "", pq($html)->find("#all-phones-small h2 a")->text()));

        $brand = trim(str_replace(" PHONES", "", $brand));

        echo "Brand: {$brand}".PHP_EOL;
        
        // Модель
        $name = pq($html)->find("#ttl h1")->text();
        $name = trim(str_replace($brand, "", $name));

        echo "Model: {$name}".PHP_EOL;
        
        // Синонимы
        $synonimsText = pq($html)->find("#specs-list p:first-child")->html();
        $synonimsText = explode("<br>", $synonimsText);
        $synonims = "";
        
        foreach ($synonimsText as $synonim) {
            if (preg_match("~Also known as~", $synonim)) {
                $synonims = trim($synonim);
                break;
            }
        }

        $synonims = str_replace("Also known as ", "", $synonims);
        
        if ($synonims) {
            $synonims = explode(", ", $synonims);
            foreach ($synonims as &$item) {
                $item = trim(str_replace($brand, "", $item));
                if (empty($item))
                    unset($item);
            }
        } else {
            $synonims = array();
        }
        echo "Synonims: ".implode(", ",$synonims).PHP_EOL;
        // Характеристики
        $characteristics_tables = pq($html)->find("#specs-list > table");
        $characteristicsLines = array();
        foreach ($characteristics_tables as $index => $table) {
            $characteristicHeader = pq($table)->find("tr:first-child th");
            $chHeader = trim($characteristicHeader->text($header));
            $characteristicHeader->remove();

            $rows = pq($table)->find("> tr");
            $lastRowName = '';
            foreach ($rows as $row) {
                $rowIndex = trim(pq($row)->find("td:first-child")->text());
                if (empty($rowIndex)) {
                    $rowIndex = $lastRowName;
                }
                $lastRowName = $rowIndex;
                $rowValue = pq($row)->find("td:last-child")->html();
                $characteristicsLines[] = $chHeader . "." . $rowIndex . "::::" . trim($rowValue);
            }
        }
        
        $parser = new GsmarenaCharacteristicsParserTest($characteristicsLines);
        $result = $parser->run();
        //var_dump($characteristicsLines);
        var_dump($result);
        // Ищем модель в бд
        $criteria = new CDbCriteria();
        $criteria->condition = "(CONCAT(brand_data.name, ' ', t.name) LIKE :search "
                . "OR CONCAT(brand_data.name, ' ', synonims.name) LIKE :search) ";
        $search = $brand . " " . $name;
        $search = htmlspecialchars($search);
        $search = str_replace("&nbsp;", " ", $search);
        $search = "{$search}";
        $criteria->params = array(
            "search" => $search,
        );
        $urlManager = new UrlManager();
        echo $search . PHP_EOL;
        $goods = Goods::model()->with("brand_data", "synonims")->find($criteria);

        // Если что-то нашли
        Yii::app()->language = 'ru';
        if (!$goods) {
            if (!$brandModel = Brands::model()->findByAttributes(array("name" => $brand))) {

                $brandModel = new Brands();
                $brandModel->name = $brand;
                $brandModel->link = $urlManager->translitUrl($brand);
                if ($brandModel->validate()) {
                    echo "Добавлен бренд {$brand}" . PHP_EOL;
                    //$brandModel->save();
                } else {
                    var_dump($brandModel->getErrors());
                    $task->completed = -1;
                    //$task->save();
                    //return $this->actionGsmArena();
                }
            }
            
            $goods = new Goods();
            $goods->brand = $brandModel->id;
            $goods->name = $name;
            $goods->link = $urlManager->translitUrl($name);
            $goods->type = 1;
            echo $urlManager->translitUrl($name) . PHP_EOL;
            if ($goods->validate()) {
                echo "Добавлен товар {$brand} {$name}" . PHP_EOL;
                //$goods->save();
            } else {
                echo "Не добавлен товар {$brand} {$name}" . PHP_EOL;
                var_dump($goods->getErrors());
                $task->completed = -1;
                //$task->save();
                //return $this->actionGsmArena();
            }
        }
    }

    
    public function actionMarkList()
    {
        $connection = Yii::app()->db;
        $query = "select concat(b.name, ' ', g.name) as name from ai_goods g inner join ai_brands b on g.brand = b.id order by name asc";
        $res = $connection->createCommand($query)->queryAll();
        foreach ($res as $r)
        {
            echo $r['name'].PHP_EOL;
        }
    }
    
    /**
     * Парсинг цветов с wiki
     */
    public function actionColors()
    {
        //http://ru.wikipedia.org/wiki/%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D1%86%D0%B2%D0%B5%D1%82%D0%BE%D0%B2
        $ch = curl_init("http://ru.wikipedia.org/wiki/%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D1%86%D0%B2%D0%B5%D1%82%D0%BE%D0%B2");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        $html = phpQuery::newDocumentHTML($content);
        $tables = pq($html)->find("table");
        foreach ($tables as $table) {
            $rows = pq($table)->find("tr");
            foreach($rows as $row) {
                $eng = pq($row)->find("th:eq(0)")->text();
                $ru = pq($row)->find("td:eq(0)")->text();
                $code = pq($row)->find("td:eq(2)")->text();
                $code = preg_replace("/\[.*\]/isu", '', $code);
                $ru = preg_replace("/\[.*\]/isu", '', $ru);
                $ru = preg_replace("/\(.*\)/isu", '', $ru);
                $ru = explode(", ", $ru);
                
                if (empty($eng) || empty($ru) || empty($code))
                    continue;
                foreach ($ru as $name) {
                    $attributes = [
                        'code'=>trim($code),
                        'ru'=>trim($name),
                        'en'=>trim($eng),
                    ];
                    if (!Colors::model()->countByAttributes($attributes)) {
                        $color = new Colors();
                        $color->code = $attributes['code'];
                        $color->ru = $attributes['ru'];
                        $color->en = $attributes['en'];
                        //$color->save();
                    }
                }
            }
        }
    }
    
    public function actionGoodsFilter()
    {
        $goods = Goods::model()->with(["brand_data"])->findAll();
        $patterns = [
            "/[^\d](?P<cut>\s\d+gb)/isu",
            "/[^\d](?P<cut>\s\d+\sgb)/isu",
            "/(?P<cut>\swi[\-\s]{0,1}fi)/isu",
            "/(?P<cut>\sDual.SIM)/isu",
            //"/galaxy.*(?P<cut>\sgt\-\w{1}\d{3,4})/isu",
            //"/galaxy.*[^\w](?P<cut>t\-\w{1}\d{3,4})/isu",
            //"/galaxy.*[^\w](?P<cut>g\-\w{1}\d{3,4})/isu",
            //"/galaxy.*[^\w](?P<cut>g\-\w{1}\d{3,4})/isu",
            //"/galaxy.*[^\w](?P<cut>i\d{4}\w{0,1})/isu",
            //"/.*(?P<cut>\s4g[^b])/isu",
            "/.*(?P<cut>\s\(\d{4}\))/isu",
        ];
        $colors = Colors::model()->findAll();
        foreach ($colors as $color) {
            $color = preg_quote($color->en);
            //$patterns[] = "/(?P<cut>{$color})/isu";
        }
        
        $count = 0;
        foreach ($goods as &$product) {
            // Если название товара - одно слово, пропускаем
            if (preg_match("/^\w+$/isu", $product->name)) {
                //echo $product->brand_data->name." - ".$product->name.PHP_EOL;
                continue;
            }
            $detect = false;
            $cuts = [];
            foreach ($patterns as $pattern) {
                
                
                if (preg_match($pattern, $product->name, $matches)) {
                    $detect = true;
                    $product->name = str_replace($matches['cut'], '', $product->name);
                    $cuts[] = $matches['cut'];
                    
                }
                
            }
            if ($detect) {
                //if (Goods::model()->countByAttributes(["brand"=>$product->brand, "name"=>$product->name])) {
                    $count++;
                    echo $product->brand_data->name." ".$product->name." # ".implode(", ", $cuts).PHP_EOL;
                //}
            }
        }
        
        echo $count.PHP_EOL;
        
    }

    
    public function actionHelpixReviews()
    {
        $products = Goods::model()->with(["brand_data"])->findAll();
        /**
         * "goods"=>Yii::t("model", "Товар"),
            "link"=>Yii::t("model", "Ссылка"),
            "lang"=>Yii::t("model", "Код языка"),
            "author"=>Yii::t("model", "Автор"),
            "title"=>Yii::t("model", "Заголовок"),
            "content"=>Yii::t("model", "Текст"),
            "priority"=>Yii::t("model", "Порядок сортировки"),
            "source"=>Yii::t("model", "Ссылка на оригинал"),
            "disabled"=>Yii::t("model", "Не показывать"),
         */
        foreach ($products as $product) {
            $urlManager = new UrlManager();
            $name = $product->brand_data->name." ".$product->name;
            $criteria = new CDbCriteria();
            $criteria->condition = 'mobile like :name';
            $criteria->params = ['name'=>$name];
            if ($helpixReviews = HelpixReviews::model()->findAll($criteria)) {
                foreach ($helpixReviews as $review) {
                    $model = new Reviews();
                    $model->goods = $product->id;
                    $model->link = $urlManager->translitUrl(htmlspecialchars_decode($review->title));
                    $model->lang = 'ru';
                    $model->title = htmlspecialchars_decode($review->title);
                    $model->content = $review->body;
                    $model->original = $review->body;
                    $model->source = $review->url;
                    $model->created = date("Y-m-d ".rand(10,20).":".rand(10,59).":s", strtotime(str_replace(".", "-", $review->date)));
                    
                    if ($model->validate()) {
                        $model->save();
                        echo $review->mobile." ".$model->title.PHP_EOL;
                    }
                }
            }
        }
    }
    
    public function actionServer()
    {
        $gmc= new GearmanClient();
        $gmc->addServer();
        
        $gmc->setCompleteCallback([$this, "news_completed"]);
        $gmc->setFailCallback([$this, "news_fail"]);
        
        $products = Goods::model()->findAll();
        foreach ($products as $product) {
            $task= $gmc->addTask("news", serialize($product));
        }
        
        if (! $gmc->runTasks())
        {
            echo "ERROR " . $gmc->error() . "\n";
            exit;
        }
    }
    
    public function actionClient()
    {
        # Создание обработчика.
        $gmworker= new GearmanWorker();

        # Указание сервера по умолчанию  (localhost).
        $gmworker->addServer();
        
        $gmworker->addFunction("news", [$this,"news_function"]);
        

        print "Waiting for job...\n";
        
        while($gmworker->work())
        {
          if ($gmworker->returnCode() != GEARMAN_SUCCESS)
          {
            echo "return_code: " . $gmworker->returnCode() . "\n";
            break;
          }
        }
    }
    
    
    public function news_completed($task)
    {
        echo "COMPLETE: " . $task->jobHandle() .PHP_EOL;
    }
    
    public function news_fail($task)
    {
        echo "FAILED: " . $task->jobHandle() . "\n";
    }
    
    public function news_function($job)
    {
        echo "Received job: " . $job->handle() . "\n";

        $product = unserialize($job->workload());
        echo $product->brand_data->name." ".$product->name.PHP_EOL;
        return true;
    }
    
    public function actionParse()
    {
        $content = file_get_contents("/var/www/analogindex/www/phonearena.html");
        $html = phpQuery::newDocumentHTML($content);
        $fullspecs = pq($html)->find("div.s_specs_box");
        $characteristicsTable = [];
        foreach ($fullspecs as $specs) {
            $title = pq($specs)->find("h2.htitle")->html();
            pq($specs)->find("h2.htitle")->remove();
            //echo $title.PHP_EOL;
            $items = pq($specs)->find('ul > li');
            foreach($items as $item) {
                if ($subtitle = pq($item)->find('span.s_tooltip_anchor')->html()) {
                    $subtitle = pq($item)->find('span.s_tooltip_anchor')->html();
                    pq($item)->find('span.s_tooltip_anchor, span.s_tooltip_content')->remove();
                } else {
                    $subtitle = pq($item)->find('strong')->html();
                }
                pq($item)->find('strong')->remove();
                $subsubtitle = pq($item)->find("li");
                foreach ($subsubtitle as $subtitleitem) {
                    $characteristicsTable[] = $title.":::".$subtitle.":::".pq($subtitleitem)->text();
                }
            }
        }
        $characteristicsTable = array_unique($characteristicsTable);
        var_dump($characteristicsTable);
        
    }
    
    
    public function actionImage() {
        $item = PhonearenaUrls::model()->findByPk(200);
        echo $item->photos;
        if (preg_match_all("/paGallery\.image\('[^']+', '(?P<images>\/\/i\-cdn\.phonearena\.com\/images\/phones\/\d+\-[x]+large\/[^.]+\.jpg)', '[^']+', '\d+', '[^']+'\)/isu", $item->photos, $matches, PREG_PATTERN_ORDER)) {
            $matches['images'] = array_map(function($value){return "http:".$value;}, $matches['images']);
            var_dump($matches['images']);
        }
    }
    
    
    public function actionProxy()
    {
        $downloader = new Downloader("http://google.com/", 2);
        echo $downloader->getProxy().PHP_EOL;
        $proxy = $downloader->getProxy();
        $downloader->deleteProxy($proxy);
        echo $downloader->getProxy().PHP_EOL;
        $proxy = $downloader->getProxy();
        $downloader->deleteProxy($proxy);
        echo $downloader->getProxy().PHP_EOL;
    }
    
    public function actionTrends()
    {
        //java -jar j-google-trends-client-1.2.8-jar-with-dependencies.jar -l INFO -q "Apple Iphone 6" -u ilyaplot@gmail.com -p 4qeruj4qeruj -m 1 -P "http://203.144.144.162:8080" -C ":"
    }
}
