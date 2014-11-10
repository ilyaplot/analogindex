<?php

class TestCommand extends CConsoleCommand
{

    public function actionCharacteristicItem()
    {
        //public function __construct($id, $catalog, $name, $formatter, $description, $raw, $product = null)
        $item = new CharacteristicItem(1, "Пробный", "Тест", "formatSize", "Описание", "memory", "1024", Goods::model()->findByPk(1));
        echo $item->getValue(true);
        echo PHP_EOL;
    }

    public function actionIndex()
    {
        $product = Goods::model()->findByPk(1640);
        $characteristics = $product->getCharacteristicsNew(array("in" => $product->generalCharacteristics, "createLinks" => true));
        foreach ($characteristics as $characteristic) {
            echo $characteristic->getValue(false) . " - " . $characteristic->getValue() . PHP_EOL;
        }
    }

    public function actionParse()
    {
        $reviews = Reviews::model()->with(array("goods_data"))->findAll();

        $goods = Goods::model()->with(array(
                "brand_data", 
                "type_data", 
                "synonims"
            ))->findAll(array(
            "order"=>"LENGTH(t.name) desc"
        ));
        
        $brands = Brands::model()->findAll(array("condition"=>"t.id not in (167)"));
        /**
         * Перебираем отзывы
         */
        foreach ($reviews as $review) {
            // Берем оригинал для обработки
            $review->content = $review->original;

            $product = $review->goods_data;
                
            

            $characteristics = $product->getCharacteristicsNew(array("in" => $product->generalCharacteristics, "createLinks" => true));

            /**
             * Перебираем характеристики
             */
            foreach ($characteristics as $characteristic) {
                // Слово для поиска
                $keyword = $characteristic->getValue(false);
                // Ссылка для замены
                $value = $characteristic->getValue();
                
                if ($keyword == $value)
                    continue;
                
                // Мелкие строки не берем
                if (mb_strlen($keyword) < 3)
                    continue;

                // Экранируем
                $pattern = preg_quote($keyword, "~");

                // Если нет значения для подстановки в ссылку, продолжаем перебор
                if (!$characteristic->linkValue)
                    continue;
                
                do {
                    $replaced = $this->replaceRecursive($review->content, $pattern, $value, $review->id);
                    if ($replaced !== false) {
                        $review->content = $replaced;
                    }
                } while($replaced !== false);
                
            }

            
            /**
             * Ссылки на товары
             */
            foreach ($goods as $item) {
                $pattern = preg_quote("{$item->brand_data->name} {$item->name}", "~");


                $value = CHtml::link("{$item->brand_data->name} {$item->name}", "http://".Yii::app()->createUrl("site/goods", array(
                    'link' => $item->link,
                    'brand' => $item->brand_data->link,
                    'type' => $item->type_data->link,
                    'language' => Language::getZoneForLang(($review->lang) ? $review->lang : 'ru'),
                )));
                
                do {
                    $replaced = $this->replaceRecursive($review->content, $pattern, $value, $review->id);
                    if ($replaced !== false) {
                        $review->content = $replaced;
                    }
                } while($replaced !== false);

                /**
                 * Перебираем синонимы товара
                 */
                foreach ($product->synonims as $synonim) {
                    
                    $pattern = preg_quote("{$product->brand_data->name} {$synonim->name}", "~");
                    
                    
                    $value = CHtml::link("{$product->brand_data->name} {$product->name}", "http://".Yii::app()->createUrl("site/goods", array(
                        'link' => $product->link,
                        'brand' => $product->brand_data->link,
                        'type' => $product->type_data->link,
                        'language' => Language::getZoneForLang(($review->lang) ? $review->lang : 'ru'),
                    )));
                        
                    do {
                        $replaced = $this->replaceRecursive($review->content, $pattern, $value, $review->id);
                        if ($replaced !== false) {
                            $review->content = $replaced;
                        }
                    } while($replaced !== false);
                   
                }
            }

            /**
             * Расставляем ссылки на бренды
             */
            foreach ($brands as $brand) {
                $pattern = preg_quote($brand->name, "~");
                $value = CHtml::link($brand->name, "http://".Yii::app()->createUrl("site/brand", array(
                    "language" => Language::getZoneForLang(($review->lang) ? $review->lang : 'ru'),
                    "type" => $product->type_data->link,
                    "link" => $brand->link,
                )));
                do {
                    $replaced = $this->replaceRecursive($review->content, $pattern, $value, $review->id);
                    if ($replaced !== false) {
                        $review->content = $replaced;
                    }
                } while($replaced !== false);
            }
            $review->save();
        }
    }
    
    public function replaceRecursive($content, $pattern, $value, $id)
    {
        $exp = "~(<[^aA][^>]*?>[^<\"]*?[^\w\d\-:])({$pattern})([^\w\d\-][^>\"]*?)~iu";
        //"~(.{0,10}[^>\"/\-\w\d\._\[\]#]{1})({$pattern})([^<\"/\-\w\d_\[\]#]{1}.{0,10})~iu"
        if (preg_match_all($exp, $content, $matches, PREG_SET_ORDER)) {
            $match = $matches[0];

            $content = str_replace($match[0], $match[1].$value.$match[3], $content);
            echo $id." : ". $pattern." : ".$match[0]." : ".$match[1].$value.$match[3].PHP_EOL;
            return $content;
            
        }
        return false;
    }

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

    
    public function actionTest()
    {
        $review = Reviews::model()->findByPk(1601);
        $content =  $review->original;
        $pattern = preg_quote("LG G2");
        echo $content.PHP_EOL;
        
        $exp = "~(<[^aA][^>]*?>[^<\"]*?[^\w\d\-:])({$pattern})([^\w\d\-][^>\"]*?<)~iu";
        echo $exp.PHP_EOL;
        if (!preg_match($exp, $content))
            echo "FAIL".PHP_EOL;
    }
    
    
    public function actionTrends()
    {
        $service = "trendspro";
        $serviceUrl = "http://www.google.com/trends/";
        $downloadUrl = $serviceUrl + "trendsReport?";
        $useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0";
        $headers = [
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: en-gb,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            "Connection: keep-alive",
        ];
        $loginUrl = 'https://accounts.google.com/ServiceLogin?service='.$service.'&passive=1209600&continue='.$serviceUrl.'&followup='.$serviceUrl;
        $authUrl = 'https://accounts.google.com/accounts/ServiceLoginAuth';
        
        $ch = curl_init($loginUrl);
        curl_setopt($ch, CURLOPT_COOKIE, "version=0; name='I4SUserLocale'; value='en_US'; port=None; port_specified=False; domain='www.google.com'; domain_specified=False; domain_initial_dot=False; path='/trends'; path_specified=True; secure=False; expires=None; discard=False; comment=None; comment_url=None; rest=None");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $form = curl_exec($ch);
        $html = phpQuery::newDocumentHTML($form);
        $inputs = pq($html)->find("#gaia_loginform input");
        $fields = [];
        foreach ($inputs as $input) {
            $fields[pq($input)->attr("name")] = pq($input)->val();
        }
        $fields['Email'] = "ilyaplot@gmail.com";
        $fields['Passwd'] = "3qeruj3qeruj";
        curl_setopt($ch, CURLOPT_URL, $authUrl);
        curl_setopt($ch, CURLOPT_COOKIE, "version=0; name='PREF'; value=''; port=None; port_specified=False; domain='www.google.com'; domain_specified=False; domain_initial_dot=False; path='/trends'; path_specified=True; secure=False; expires=None; discard=False; comment=None; comment_url=None; rest=None");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/ilyaplot/cookie.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/ilyaplot/cookie.txt");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        echo curl_exec($ch);
        
        /**
         * '''
        Authenticate to Google:
        1 - make a GET request to the Login webpage so we can get the login form
        2 - make a POST request with email, password and login form input values
        '''
        
        # Make sure we get CSV results in English
        ck = Cookie(version=0, name='I4SUserLocale', value='en_US', port=None, port_specified=False, domain='www.google.com', domain_specified=False,domain_initial_dot=False, path='/trends', path_specified=True, secure=False, expires=None, discard=False, comment=None, comment_url=None, rest=None)
        ck_pref = Cookie(version=0, name='PREF', value='', port=None, port_specified=False, domain='www.google.com', domain_specified=False,domain_initial_dot=False, path='/trends', path_specified=True, secure=False, expires=None, discard=False, comment=None, comment_url=None, rest=None) 

        self.cj = CookieJar()                            
        self.cj.set_cookie(ck)
        self.cj.set_cookie(ck_pref)
        self.opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(self.cj))
        self.opener.addheaders = self.headers
        
        # Get all of the login form input values
        find_inputs = etree.XPath("//form[@id='gaia_loginform']//input")
        try:
            #
            resp = self.opener.open(self.url_login)
            
            if resp.info().get('Content-Encoding') == 'gzip':
                buf = StringIO( resp.read())
                f = gzip.GzipFile(fileobj=buf)
                data = f.read()
            else:
                data = resp.read()
            
            xmlTree = etree.fromstring(data, parser=html.HTMLParser(recover=True, remove_comments=True))
            
            for input in find_inputs(xmlTree):
                name = input.get('name')
                if name:
                    name = name.encode('utf8')
                    value = input.get('value', '').encode('utf8')
                    self.login_params[name] = value
        except:
            print("Exception while parsing: %s\n" % traceback.format_exc())    
        
        self.login_params["Email"] = username
        self.login_params["Passwd"] = password
        
        params = urllib.urlencode(self.login_params)
        self.opener.open(self.url_authenticate, params)
         */
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
}
