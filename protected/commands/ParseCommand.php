<?php

class ParseCommand extends CConsoleCommand
{

    public function actionGsmArena()
    {
        $task = SourcesGsmarena::model()->with("file_data")->findByAttributes(array("completed" => 0));
        $source_url = $task->url;
        if (!$task) {
            echo "No tasks for parse" . PHP_EOL;
            exit();
        }
        $content = $task->file_data->getContent();
        if (!$content) {
            $task->completed = -1;
            $task->save();
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
        $parser = new GsmarenaCharacteristicsParser($characteristicsLines);
        $result = $parser->run();

        //var_dump($result);
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
        
        Yii::app()->language = 'ru';
        if (!$goods) {
            if (!$brandModel = Brands::model()->findByAttributes(array("name" => $brand))) {

                $brandModel = new Brands();
                $brandModel->name = $brand;
                $brandModel->link = $urlManager->translitUrl($brand);
                if ($brandModel->validate()) {
                    echo "Добавлен бренд {$brand}" . PHP_EOL;
                    $brandModel->save();
                } else {
                    var_dump($brandModel->getErrors());
                    $task->completed = -1;
                    $task->save();
                    return $this->actionGsmArena();
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
                $goods->source_url = $source_url;
                $goods->save();
            } else {
                echo "Не добавлен товар {$brand} {$name}" . PHP_EOL;
                var_dump($goods->getErrors());
                $task->completed = -1;
                $task->save();
                return $this->actionGsmArena();
            }
        } else {
            $goods->source_url = $source_url;
            $goods->save();
        }

        
        foreach ($synonims as $synonim)
        {
            echo $synonim.PHP_EOL;
            $synModel = new GoodsSynonims();
            $synModel->goods = $goods->id;
            $synModel->name = $synonim;
            $synModel->visibled = 1;
            if ($synModel->validate()) {
                $synModel->save();
            } else {
                var_dump($synModel->getErrors());
            }
        }
        
        foreach ($result as $characteristic) {
            $goodsCharacteristic = new GoodsCharacteristics();
            $goodsCharacteristic->goods = $goods->id;
            $goodsCharacteristic->characteristic = $characteristic['id'];
            $goodsCharacteristic->lang = $characteristic['lang'];
            $goodsCharacteristic->value = is_array($characteristic['values']) ? json_encode($characteristic['values']) : $characteristic['values'];
            if ($goodsCharacteristic->validate()) {
                $goodsCharacteristic->save();
            } else {
                //var_dump($goodsCharacteristic->getErrors());
            }
        }

        $imagesContent = pq($html)->find("#specs-cp-pic > a")->attr("href");

        if (!empty($imagesContent)) {
            sleep(1);
            $imagesContent = $this->getContent("http://www.gsmarena.com/" . $imagesContent);
        }

        if ($imagesContent) {
            //echo "Images..." . PHP_EOL;
            $html = phpQuery::newDocumentHTML($imagesContent);
            $pictures = pq($html)->find("#pictures p");
            foreach ($pictures as $picture) {
                $image = pq($picture)->find("img")->attr("src");
                if (!empty($image)) {
                    $goodsImage = GoodsImages::model()->with(array(
                                "image_data" => array(
                                    "joinType" => "INNER JOIN",
                                    "condition" => "image_data.source  = :source",
                                    "params" => array("source" => $image),
                                )
                            ))->count();
                    if (!$goodsImage) {
                        $ext = explode(".", $image);
                        $ext = end($ext);
                        $file = new Files();
                        $file->name = "{$brand->name} {$goods->name}.{$ext}";
                        $file->save();
                        $filename = $file->getFilename();
                        if (!$this->getFile($image, $filename)) {
                            echo "Не удалось скачать {$image}" . PHP_EOL;
                            $file->delete();
                            continue;
                        }
                        sleep(1);
                        $file->size = $file->getFilesize();
                        $file->mime_type = $file->getMimeType();
                        if (!preg_match("~image.*~", $file->mime_type)) {
                            $file->delete();
                            echo "Тип изображения не соответствует image. {$file->mime_type}" . PHP_EOL;
                            continue;
                        }
                        echo "Добавлено изображение {$image}" . PHP_EOL;
                        $file->save();
                        $imageModel = new Images();
                        $imageModel->file = $file->id;
                        $imageModel->size = 1;
                        $size = getimagesize($filename);
                        $imageModel->width = $size[0];
                        $imageModel->height = $size[1];
                        $imageModel->source = $image;
                        $imageModel->save();
                        $goodsImage = new GoodsImages();
                        $goodsImage->goods = $goods->id;
                        $goodsImage->image = $imageModel->id;
                        $goodsImage->save();
                    } else {
                        //echo "Image exists" . PHP_EOL;
                    }
                }
            }
        }


        $task->completed = 1;
        $task->save();
        $this->actionGsmArena();
    }

    public function actionSmartphoneua()
    {
        $task = SourcesSmartphoneua::model()->with("file_data")->findByAttributes(array("completed" => 0));
        //$task = SourcesSmartphoneua::model()->with("file_data")->findByAttributes(array("completed"=>1, "id"=>2098));
        if (!$task) {
            echo "No tasks for parse" . PHP_EOL;
            exit();
        }
        $content = $task->file_data->getContent();
        if (!$content) {
            $task->completed = -1;
            $task->save();
            echo "Null content for task" . PHP_EOL;
            exit();
        }
        $html = phpQuery::newDocumentHTML($content);
        $crumbs = pq($html)->find("#breadcramps > div");
        $types = array(
            "Каталог телефонов" => array(1, "Телефоны"),
            "Каталог планшетов" => array(2, "Планшеты"),
            "Каталог электронных книг" => array(3, "Электронные книги"),
        );
        $type_replaces = array(
            1 => " ",
            2 => "Планшет ",
            3 => "Электронная книга ",
        );
        $type = 0;
        $brand = '';
        foreach ($crumbs as $key => $crumb) {
            // Тип
            if ($key == 0) {
                $text = pq($crumb)->text();
                $type = isset($types[$text]) ? $types[$text][0] : 0;
            }
            // Производитель
            if ($key == 1) {
                $brand = isset($types[$text]) ? str_replace($types[$text][1], '', pq($crumb)->text()) : '';
                $brand = trim($brand);
            }
        }



        if (!$type) {
            echo "Не удалось определить тип товара." . PHP_EOL;
            return $this->actionSmartphoneua();
        }


        if (!$brand) {
            echo "Не удалось определить производителя." . PHP_EOL;
            return $this->actionSmartphoneua();
        }

        $name = pq($html)->find("div.padding h1")->text();
        $name = trim(substr($name, strlen($brand) + strlen($type_replaces[$type]), strlen($name)));

        if (!$brand) {
            echo "Не удалось определить наименование товара." . PHP_EOL;
            return $this->actionSmartphoneua();
        }

        echo "$type $brand $name" . PHP_EOL;

        // Картинки
        $images = pq($html)->find("#fotos ul > li");
        $imagesList = array();
        foreach ($images as $image) {
            $imagesList[] = pq($image)->find("a")->attr("href");
        }


        // Характеристики
        $characteristics_elems = pq($html)->find("#allspecs > *");
        $characteristics_lines = array();
        $currentCatalog = '';
        foreach ($characteristics_elems as $elem) {
            $text = pq($elem)->text();
            if ($text == '* Найденные неточности в описании просьба сообщать на help@smartphone.ua')
                break;
            if ($elem->tagName == "strong")
                $currentCatalog = trim(substr($text, 0, strlen($text) - strlen("$brand $name ")));
            else {
                //echo "$currentCatalog::::$text".PHP_EOL;
                $characteristics_lines[] = "$currentCatalog::::$text";
            }
        }
        //var_dump($characteristics_lines);

        $parser = new SmartphoneuaCharacteristicsParser($characteristics_lines);
        $result = $parser->run();
        //var_dump($result);
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
                    $brandModel->save();
                } else {
                    var_dump($brandModel->getErrors());
                    $task->completed = -1;
                    $task->save();
                    return $this->actionGsmArena();
                }
            }
            $urlManager = new UrlManager();
            $goods = new Goods();
            $goods->brand = $brandModel->id;
            $goods->name = $name;
            $goods->link = $urlManager->translitUrl($name);
            $goods->type = $type;
            echo $goods->link . PHP_EOL;
            if ($goods->validate()) {
                echo "Добавлен товар {$brand} {$name}" . PHP_EOL;
                $goods->save();
            } else {
                echo "Не добавлен товар {$brand} {$name}" . PHP_EOL;
                var_dump($goods->getErrors());
                $task->completed = -1;
                $task->save();
                return $this->actionSmartphoneua();
            }
        } else {
            $goods->type = $type;
            $goods->save();
        }

        foreach ($result as $characteristic) {
            $goodsCharacteristic = new GoodsCharacteristics();
            $goodsCharacteristic->goods = $goods->id;
            $goodsCharacteristic->characteristic = $characteristic['id'];
            $goodsCharacteristic->lang = $characteristic['lang'];
            $goodsCharacteristic->value = is_array($characteristic['values']) ? json_encode($characteristic['values']) : $characteristic['values'];
            if ($goodsCharacteristic->validate()) {
                $goodsCharacteristic->save();
            } else {
                //var_dump($goodsCharacteristic->getErrors());
            }
        }

        foreach ($imagesList as $image) {
            $goodsImage = GoodsImages::model()->with(array(
                        "image_data" => array(
                            "joinType" => "INNER JOIN",
                            "condition" => "image_data.source  = :source",
                            "params" => array("source" => $image),
                        )
                    ))->count();
            if (!$goodsImage) {
                $ext = explode(".", $image);
                $ext = end($ext);
                $file = new Files();
                $file->name = "{$brand} {$name}.{$ext}";
                $file->save();
                $filename = $file->getFilename();
                if (!$this->getFile($image, $filename)) {
                    echo "Не удалось скачать {$image}" . PHP_EOL;
                    $file->delete();
                    continue;
                }
                sleep(1);
                $file->size = $file->getFilesize();
                $file->mime_type = $file->getMimeType();
                if (!preg_match("~image.*~", $file->mime_type)) {
                    $file->delete();
                    echo "Тип изображения не соответствует image. {$file->mime_type}" . PHP_EOL;
                    continue;
                }
                echo "Добавлено изображение {$image}" . PHP_EOL;
                $file->save();
                $imageModel = new Images();
                $imageModel->file = $file->id;
                $imageModel->size = 1;
                $size = getimagesize($filename);
                $imageModel->width = $size[0];
                $imageModel->height = $size[1];
                $imageModel->source = $image;
                $imageModel->save();
                $goodsImage = new GoodsImages();
                $goodsImage->goods = $goods->id;
                $goodsImage->image = $imageModel->id;
                $goodsImage->save();
            }
        }
        $task->completed = 1;
        $task->save();
        $this->actionSmartphoneua();
    }

    public function getFile($url, $filename)
    {
        if (empty($filename))
            return false;
        if (!$file = fopen($filename, 'w'))
            return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_FILE, $file);
        curl_exec($ch);
        fclose($file);
        if (curl_errno($ch)) {
            echo "Curl error " . curl_error($ch) . PHP_EOL;
            curl_close($ch);
            return false;
        }

        $info = curl_getinfo($ch);
        if ($info['http_code'] !== 200)
            return false;
        curl_close($ch);
        return true;
    }

    public function getContent($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "Curl error " . curl_error($ch) . PHP_EOL;
            curl_close($ch);
            return false;
        }

        $info = curl_getinfo($ch);
        if ($info['http_code'] !== 200) {
            echo "Http code: {$info['http_code']}" . PHP_EOL;
            return false;
        }
        curl_close($ch);
        return $content;
    }

    public function actionReviewsTags()
    {
        $reviews = Reviews::model()->findAll();
        $criteria = new CDbCriteria();
        $criteria->condition = "disabled = 0";
        $tags = Tags::model()->findAll($criteria);
        foreach ($reviews as $review) {
            foreach ($tags as $tag) {
                if ($this->hasTag($review->title." ".$review->content, $tag->name)) {
                    $model = new ReviewsTags();
                    $model->tag = $tag->id;
                    $model->review = $review->id;
                    if ($model->validate()) {
                        $model->save();
                        echo $tag->type."_".$tag->link.PHP_EOL;
                    }
                }
            }
        }
        echo PHP_EOL;
    }
    
    public function actionNewsTags()
    {
        $news = News::model()->findAll();
        $criteria = new CDbCriteria();
        $criteria->condition = "disabled = 0";
        $tags = Tags::model()->findAll($criteria);
        foreach ($news as $item) {
            foreach ($tags as $tag) {
                if ($this->hasTag($item->title." ".$item->content, $tag->name)) {
                    $model = new NewsTags();
                    $model->tag = $tag->id;
                    $model->news = $item->id;
                    if ($model->validate()) {
                        $model->save();
                        echo $tag->type."_".$tag->link.PHP_EOL;
                    }
                }
            }
        }
        echo PHP_EOL;
    }
    
    protected function hasTag($content, $tag) 
    {
        $pattern = preg_quote($tag, '/');
        $exp = "/[^\w]{1}{$pattern}[^\w]{1}/isu";
        return preg_match($exp, $content);
    }
    
    
    public function actionReviewLinks()
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
    
}
