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
        $exp = "~(<[^aA][^>]*?>[^<\"]*?[^\w\d\-:])({$pattern})([^\w\d\-][^>\"]*?<)~iu";
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

}
