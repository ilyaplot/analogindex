<?php

class ParseCommand extends CConsoleCommand
{

    protected static $tags;
    protected static $goods;
    protected static $brands;
    
    protected static $downloader = null;

    public function actionIrecommend()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "downloaded = 1 and completed = 0";
        $criteria->order = "id asc";
        $criteria->limit = 50;
        
        $items = SourcesIrecommend::model()->findAll($criteria);
        $articlesFilter = new ArticlesFilter();
        foreach ($items as $item) {
            
            $review = Articles::model()->findByAttributes(['source_url'=>$item->url]);
            if (empty($review->id)) {
                $review = new Articles();
                $review->source_url = $item->url;
                $review->has_filtered = 0;
                $review->lang = 'ru';
            }
            
            $review->type = Articles::TYPE_OPINION;
            
            $html = phpQuery::newDocumentHTML($item->getContent());
            $element = pq($html)->find('li.qtab-myreviewinfo.active.last a');
            if ($element->text() != "Отзыв")
            {
                echo "Страница не является отзывом".PHP_EOL;
                continue;
            }
            $element->remove();
            echo $item->url.PHP_EOL;
            
            $productElem = pq($html)->find("div[itemprop=itemReviewed]");
            $product = pq($productElem)->find("span[itemprop=name]")->text();
            if ($item->type == 'phones') {
                $product = preg_replace("/смартфон/isu", "", $product);
                $product = preg_replace("/мобильный телефон/isu", "", $product);
            } else if ($item->type = 'tablets') {
                $product = preg_replace("/планшет/isu", "", $product);
            }
            
            $element = pq($html)->find('div.main-comment');
            $title = $element->find("h2.summary")->text();

            $title = preg_replace("/".preg_quote($product)."/isu", '' ,  $title);
            $title = $product." : ".trim($title);
            
            if (empty($review->title) && empty($review->id)) {
                $review->link = Yii::app()->urlManager->translitUrl($title);
            }
            $review->title = $title;

            $plus = trim(pq($element)->find("span.plus")->text());
            $minus = trim(pq($element)->find("span.minus")->text());
            
            $content = $element->find('div.views-field-teaser');
            $images = pq($content)->find(".field-items")->html();
            $content->find('.social_buttons_wrapper, .add-ticket-button-wrapper, div.clear, fieldset, .smiley-content')->remove();
            $content = pq($content)->find(".description")->html().(!empty($images) ? "<br />".$images : '');
            
            if (!empty($minus)) {
                $content = "<strong class='minus'>Недостатки:</strong> <span>{$minus}</span><br /> <hr /><br />".PHP_EOL.$content;
            }
            if (!empty($plus)) {
                $content = "<strong class='plus'>Достоинства:</strong> <span>{$plus}</span><br />".PHP_EOL.$content;
            }
            
            $review->source_content = $content."<!--{$product}-->";

            $created = pq($html)->find("meta[itemprop=datePublished]")->attr("content");
            if (!empty($created)) {
                $review->created = date("Y-m-d H:i:s", strtotime($created));
            }
            if ($review->validate()) {
                $review->save();
                $review = $articlesFilter->filter($review);
                $review->save();
                echo "+";
                $item->completed = 1;
                $item->save();
            }
            phpQuery::unloadDocuments();
        }
    }

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
            $goods->type = 1;
            $goods->brand = $brandModel->id;
            $goods->name = $name;
            $goods->link = $urlManager->translitUrl($name);
            
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
            if (in_array($characteristic['id'], GoodsCharacteristics::$needReplace)) {
                $gchCriteria = new CDbCriteria();
                $gchCriteria->condition = 'characteristic = :id and goods = :goods';
                $gchCriteria->params = ['id'=>$characteristic['id'], 'goods'=>$goods->id];
                GoodsCharacteristics::model()->deleteAll($gchCriteria);
                unset($gchCriteria);
            }
            
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
        
        $downloader = (self::$downloader == null) ? new Downloader("http://www.smartphone.ua/", 20) : self::$downloader;
        self::$downloader = $downloader;
        $task = SourcesSmartphoneua::model()->with("file_data")->findByAttributes(array("completed" => 0));
        
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
            if (in_array($characteristic['id'], GoodsCharacteristics::$needReplace)) {
                $gchCriteria = new CDbCriteria();
                $gchCriteria->condition = 'characteristic = :id and goods = :goods';
                $gchCriteria->params = ['id'=>$characteristic['id'], 'goods'=>$goods->id];
                GoodsCharacteristics::model()->deleteAll($gchCriteria);
                unset($gchCriteria);
            }
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
                if (!$downloader->downloadFile($image, $filename)) {
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

    public function actionPhonearena()
    {
        $downloader = new Downloader("http://www.phonearena.com/", 15);
        $urlManager = new UrlManager();
        $types = GoodsTypes::model()->findAll();
        $goodsTypes = [];
        
        $typePatterns = [
            1=>'Smart phone',
            2=>'Tablet',
            5=>'Feature phone',
            6=>'Basic phone',
        ];
        
        foreach ($types as $type) {
            $goodsTypes[$type->id] = $type->link;
        }
        unset($types);
        
        
        $list = PhonearenaUrls::model()->getParseList();
        
        foreach ($list as &$item) {
            //usleep(500000);
            
            $html = phpQuery::newDocumentHtml($item->content);
            pq($html)->find("div.s_breadcrumbs > ul > li.s_sep")->remove();
            
            $breadcrumbs = pq($html)->find("div.s_breadcrumbs > ul > li");

            $brand = '';
            $name = '';
            $type = 0;
            $synonims = [];
            /**
             * Проходимся по крошкам и выделем оттуда бренд и аппарат
             */
            foreach ($breadcrumbs as $index=>$breadcrumb) {
                $text = pq($breadcrumb)->text();
                switch ($index) {
                    case 1:
                        $brand = preg_replace("/(.*)\s(?:phones|tablets)/isu", "$1", $text);
                    break;
                    case 2:
                        $pattern = preg_quote($brand, '/');
                        $name = preg_replace("/{$pattern}\s(.*)/isu", "$1", $text);
                        
                        if (preg_match("/.*\/.*/isu", $name)) {
                            $synonims = explode(" / ", $name);
                            $name = $synonims[0];
                            unset($synonims[0]);
                        }
                    break;
                }
            }
            unset($breadcrumbs, $index, $breadcrumb);

            $specificatons = pq($html)->find("#phone_specificatons");

            /**
             * Определяем тип устройства
             */
            foreach ($typePatterns as $typeId=>$pattern) {
                $exp = "/<strong class=\" s_lv_1 \">Device type:<\/strong><ul class=\" s_lv_1 \"><li>{$pattern}<\/li>/isu";
                if (preg_match($exp, $specificatons->html())) {
                    $type = $typeId;
                    break;
                }
            }
            
            if (!$type) {
                $type = 1;
            }
                
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
            echo $search . PHP_EOL;
            $goods = Goods::model()->with("brand_data", "synonims")->find($criteria);
            unset($criteria);
            
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
                        echo "Не добавлен бренд {$brand}" . PHP_EOL;
                        unset($brandModel, $html);
                        continue;
                    }
                }
                $goods = new Goods();

                $goods->brand = $brandModel->id;
                $goods->name = $name;
                $goods->link = $urlManager->translitUrl($name);
                $goods->type = $type;
                echo $urlManager->translitUrl($name) . PHP_EOL;
                if ($goods->validate()) {
                    echo "Добавлен товар {$brand} {$name}" . PHP_EOL;
                    $goods->source_url = $item->fullurl;
                    $goods->save();
                } else {
                    echo "Не добавлен товар {$brand} {$name}" . PHP_EOL;
                    var_dump($goods->getErrors());
                    $connection = Yii::app()->db;
                    $connection->createCommand("update phonearena_urls set parsed = 2 where id = {$item->id}")->execute();
                    unset($goods, $html, $item, $connection);
                    continue;
                }
            } elseif (empty($goods->source_url) || $goods->type != $type) {
                if (!$type) {
                    $type = 1;
                }
                if ($goods->type != $type) {
                    $redirect = new Redirects();
                    $redirect->from = '/'.$goodsTypes[$goods->type].'/'.$goods->brand_data->link.'/'.$goods->link.".html";
                    $redirect->to = '/'.$goodsTypes[$type].'/'.$goods->brand_data->link.'/'.$goods->link.".html";
                    $redirect->code = 301;
                    if ($redirect->validate()) {
                        $redirect->save();
                    }
                    $goods->type = $type;
                }
                $goods->source_url = $item->fullurl;
                $goods->save();
            }

            /**
             * Характеристики
             */
            $fullspecs = pq($html)->find("div.s_specs_box");
            $characteristicsTable = [];
            foreach ($fullspecs as $specs) {
                $title = pq($specs)->find("h2.htitle")->html();
                pq($specs)->find("h2.htitle")->remove();
                //echo $title.PHP_EOL;
                $sitems = pq($specs)->find('ul > li');
                foreach($sitems as $sitem) {
                    if ($subtitle = pq($sitem)->find('span.s_tooltip_anchor')->html()) {
                        $subtitle = pq($sitem)->find('span.s_tooltip_anchor')->html();
                        pq($sitem)->find('span.s_tooltip_anchor, span.s_tooltip_content')->remove();
                    } else {
                        $subtitle = pq($sitem)->find('strong')->html();
                    }
                    pq($sitem)->find('strong')->remove();
                    $subsubtitle = pq($sitem)->find("li");
                    foreach ($subsubtitle as $subtitleitem) {
                        $characteristicsTable[] = $title.":::".$subtitle.":::".pq($subtitleitem)->text();
                    }
                }
            }
            unset($fullspecs, $sitems, $sitem);
            $characteristicsTable = array_unique($characteristicsTable);
            $parser = new PhonearenaCharacteristicsParser($characteristicsTable);
            
            $result = $parser->run();
            unset($parser, $characteristicsTable);
            foreach ($result as $characteristic) {
                if (in_array($characteristic['id'], GoodsCharacteristics::$needReplace)) {
                    $gchCriteria = new CDbCriteria();
                    $gchCriteria->condition = 'characteristic = :id and goods = :goods';
                    $gchCriteria->params = ['id'=>$characteristic['id'], 'goods'=>$goods->id];
                    GoodsCharacteristics::model()->deleteAll($gchCriteria);
                    unset($gchCriteria);
                }
                $goodsCharacteristic = new GoodsCharacteristics();
                $goodsCharacteristic->goods = $goods->id;
                $goodsCharacteristic->characteristic = $characteristic['id'];
                $goodsCharacteristic->lang = $characteristic['lang'];
                $goodsCharacteristic->value = is_array($characteristic['values']) ? json_encode($characteristic['values']) : $characteristic['values'];
                if ($goodsCharacteristic->validate()) {
                    echo $goodsCharacteristic->characteristic." ";
                    $goodsCharacteristic->save();
                } else {
                    //var_dump($goodsCharacteristic->getErrors());
                }
            }
            unset($result, $characteristic, $goodsCharacteristic);
            echo PHP_EOL;

            /**
             * Изображения
             */
            if (!empty($item->photos)) {
                if (preg_match_all("/paGallery\.image\('[^']+', '(?P<images>\/\/i\-cdn\.phonearena\.com\/images\/phones\/\d+\-[x]*large\/[^.]+\.jpg)', '[^']+', '\d+', '[^']+'\)/isu", $item->photos, $matches, PREG_PATTERN_ORDER)) {
                    $matches['images'] = array_map(function($value){return "http:".$value;}, $matches['images']);

                    foreach ((array) $matches['images'] as $image) {
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
                            if (!$downloader->downloadFile($image, $filename)) {
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
                            echo "Добавлено изображение {$image} {$file->size} байт" . PHP_EOL;
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
                            unset($goodsImage, $imageModel, $file);
                        } else {
                            //echo "Image exists" . PHP_EOL;
                        }
                        unset ($goodsImage, $imageModel, $size, $file, $filename);
                    }
                }
            }
            $connection = Yii::app()->db;
            $connection->createCommand("update phonearena_urls set parsed = 1 where id = {$item->id}")->execute();
            unset($item, $matches, $html, $goods, $connection);
            echo gc_collect_cycles().PHP_EOL;
            phpQuery::unloadDocuments();
        }
    }
    
    
    public function getFile($url, $filename)
    {
        if (empty($filename))
            return false;
        if (!$file = fopen($filename, 'w'))
            return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.".rand(100, 900)." Safari/537.".rand(10, 90));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_PROXY, "183.207.232.193:8080");
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
        shuffle($reviews);
        $criteria = new CDbCriteria();
        $criteria->condition = "disabled = 0";
        $tags = Tags::model()->findAll($criteria);
        foreach ($reviews as $review) {
            echo ".";
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
    
    
    protected function hasTag($content, $tag) 
    {
        $content = strip_tags($content);
        $pattern = preg_quote($tag, '/');
        $exp = "/[^\w]{1}{$pattern}[^\w]{1}/isu";
        $result =  preg_match($exp, $content);
        unset($pattern, $tag, $content, $exp);
        return $result;
    }
    
    public function replaceRecursive($content, $pattern, $value, $id=null)
    {
        $exp = "~(<[^aA][^>]*?>[^<\"]*?[^\w\d\-:])({$pattern})([^\w\d\-][^>\"]*?)~iu";
        //"~(.{0,10}[^>\"/\-\w\d\._\[\]#]{1})({$pattern})([^<\"/\-\w\d_\[\]#]{1}.{0,10})~iu"
        if (preg_match_all($exp, $content, $matches, PREG_SET_ORDER)) {
            $match = $matches[0];

            $content = str_replace($match[0], $match[1].$value.$match[3], $content);
            //echo $id." : ". $pattern." : ".$match[0]." : ".$match[1].$value.$match[3].PHP_EOL;
            unset($pattern, $value, $id, $matches);
            return $content;
            
        }
        unset($content, $pattern, $value, $id, $matches);
        return false;
    }
    
}
