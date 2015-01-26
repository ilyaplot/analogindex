<?php

class SystemCommand extends ConsoleCommand
{

    public function actionSendEmails()
    {
        $notifications = Notifications::model()->findAllByAttributes(array("sended" => 0));
        foreach ($notifications as $notify) {
            $to = $notify->email;
            $subject = $notify->subject;
            $message = $notify->message;

            $mailer = Yii::app()->Smtpmail;
            $mailer->IsSMTP();
            $mailer->IsHTML(true);
            $mailer->Subject = $subject;
            $mailer->AddAddress($to);
            $mailer->Body = $message;

            if (!$mailer->Send()) {
                //$notify->sended = ;
                echo $mailer->ErrorInfo . PHP_EOL;
            } else {
                $notify->sended = 1;
                $notify->save();
                Echo 'EMail OK' . PHP_EOL;
            }
        }
    }

    public function actionImportReviews()
    {
        $connection = Yii::app()->reviews;
        $urlManager = new UrlManager;
        $goods = Goods::model()->with(array(
                    "brand_data",
                    "synonims",
                ))->findAll();
        foreach ($goods as $product) {
            $query = "select * from reviews where product like :name ";
            $queryParams = array("name" => $product->brand_data->name . " " . $product->name);
            $params = array();
            foreach ($product->synonims as $synonim) {
                $params[] = $product->brand_data->name . " " . $synonim->name;
            }

            foreach ($params as $paramKey => $paramValue) {
                $query .= ' or product like :name' . $paramKey;
                $queryParams["name" . $paramKey] = $paramValue;
            }
            $reviews = $connection->createCommand($query)->queryAll(true, $queryParams);
            foreach ($reviews as $review) {
                if (!Reviews::model()->countByAttributes(array("source" => $review['url']))) {
                    $reviewModel = new Reviews("import");
                    $reviewModel->source = $review['url'];
                    $reviewModel->goods = $product->id;
                    $reviewModel->title = $review['title'];
                    $reviewModel->link = $urlManager->translitUrl($review['title']);
                    $reviewModel->author = 0;
                    $reviewModel->content = $review['content'];
                    $reviewModel->original = $review['content'];
                    $reviewModel->disabled = 0;
                    $reviewModel->save();
                    if ($review['rating']) {
                        $rating = new RatingsGoods("import");
                        $rating->goods = $product->id;
                        $rating->user = 0;
                        $rating->value = $review['rating'];
                        $rating->save();
                    }
                    echo ".";
                }
            }
        }
        echo PHP_EOL;
    }

    public function actionReviewFilter()
    {
        $reviews = Reviews::model()->findAllByAttributes(array('filtered' => 0));
        foreach ($reviews as $review) {
            $review->title = ucfirst(trim(strip_tags($review->title)));
            $html = phpQuery::newDocumentHTML($review->content);
            foreach ($html->find("a") as $a) {
                // Удаляем Lightbox ссылки и заменяем на изображения
                $image = (string) pq($a)->find("img");
                if (!empty($image)) {
                    $rel = pq($a)->attr("rel");
                    if (!empty($rel)) {
                        if (preg_match("~light~", $rel)) {

                            pq($a)->find("img")->attr("src", pq($a)->attr("href"));
                            $image = (string) pq($a)->find("img");
                        }
                    }
                    pq($a)->replaceWith($image);
                } else {
                    if (!preg_match("~^http://.*~", pq($a)->attr("href")))
                        pq($a)->replaceWith(pq($a)->html());
                }
            }

            // Заменяем div на p
            while (count($html->find('div'))) {
                foreach ($html->find('div') as $div) {
                    $divHtml = pq($div)->html();
                    if (!empty($divHtml))
                        pq($div)->replaceWith("<p>" . pq($div)->html() . "<p>");
                    else
                        pq($div)->remove();
                }
            }

            pq($html)->find("p:empty")->remove();

            $review->content = (string) $html;
            $review->filtered = 1;
            $review->save();
            echo ".";
        }
        echo PHP_EOL;
    }

    public function actionFillSelector()
    {
        $ids = array(5, 6, 7, 8, 13, 14, 31);
        $goods = Goods::model()->findAll();




        foreach ($goods as $product) {
            $attributes = array(
                "id" => $product->id,
                "type" => $product->type,
                "brand" => $product->brand_data->link,
                "os" => "any",
                "screensize" => 0,
                "cores" => 0,
                "cpufreq" => 0,
                "ram" => 0,
                "processor" => 'any',
                'gpu' => 'any',
            );

            if (!$model = CharacteristicsSelector::model()->findByPk($product->id))
                $model = new CharacteristicsSelector();


            $characteristics = $product->getCharacteristics(array(), true);

            $characteristicsLinks = new CharacteristicsLinks($characteristics);
            $attributes = $characteristicsLinks->getLinks($attributes);
            $model->attributes = $attributes;
            $model->save();
        }
        echo PHP_EOL;
    }

    public function actionFillProcessors()
    {
        $criteria = new CDbCriteria();
        $criteria->select = "round(count(id)/2) as characteristic, value";
        $criteria->condition = "characteristic = 7";
        $criteria->group = "value";
        $criteria->order = "value";
        $processors = GoodsCharacteristics::model()->findAll($criteria);
        $cnt = 0;
        foreach ($processors as $processor) {
            if ($processor->characteristic > 2) {
                $model = new Processors("fill");
                $model->name = $processor->value;
                if ($model->validate())
                    $model->save();
                $cnt++;
                echo $cnt . " - " . $processor->characteristic . " - " . str_replace("\t", " ", $processor->value) . PHP_EOL;
            }
        }
    }

    public function actionFillGPU()
    {
        $criteria = new CDbCriteria();
        $criteria->select = "round(count(id)/2) as characteristic, value";
        $criteria->condition = "characteristic = 31";
        $criteria->group = "value";
        $criteria->order = "value";
        $processors = GoodsCharacteristics::model()->findAll($criteria);
        $cnt = 0;
        foreach ($processors as $processor) {
            if ($processor->characteristic > 2) {
                $model = new Gpu("fill");
                $model->name = $processor->value;
                if ($model->validate())
                    $model->save();
                $cnt++;
                echo $cnt . " - " . $processor->characteristic . " - " . str_replace("\t", " ", $processor->value) . PHP_EOL;
            }
        }
    }

    public function actionCharacteristicsLinks()
    {
        $products = Goods::model()->findAll();
        foreach ($products as $product) {
            $rules = array();
            $characteristics = $product->getCharacteristics($product->generalCharacteristics, true);
            foreach ($characteristics as $catalog) {
                foreach ($catalog as $characteristic) {
                    var_dump($characteristic);
                    exit();
                    $rules[$characteristic['id']] = $characteristic['value'];
                }
            }
            var_dump($rules);
        }
    }
    
    /**
     * Создает несуществующие тэги
     */
    public function actionFillTags()
    {
        $brands = Brands::model()->findAll();
        foreach ($brands as $brand) {
            $model = new Tags();
            $model->name = $brand->name;
            $model->type = Tags::TYPE_BRAND;
            if ($model->validate()) {
                echo $brand->name.PHP_EOL;
                $model->save();
                $tag = $model->id;
                $model = new BrandsTags();
                $model->brand = $brand->id;
                $model->tag = $tag;
                if ($model->validate()) {
                    $model->save();
                }
            }
        }
        unset($brand, $brands);
        
        $products = Goods::model()->with(["brand_data"])->findAll();
        
        foreach ($products as $product) {
            
            if (!preg_match("/^\d+$/isu", $product->name) && mb_strlen($product->name) > 2) {
                $model = new Tags();
                $model->name = $product->name;
                $model->type = Tags::TYPE_PRODUCT;
                if ($model->validate()) {
                    $model->save();
                    $tag = $model->id;

                    $model = new GoodsTags();
                    $model->goods = $product->id;
                    $model->tag = $tag;
                    if ($model->validate()) {
                        $model->save();
                    }
                }
            }
            
            
            $name = $product->brand_data->name." ".$product->name;
            $model = new Tags();
            $model->name = $name;
            $model->type = Tags::TYPE_PRODUCT;
            
            
            if ($model->validate()) {
                echo $name.PHP_EOL;
                $model->save();
                $tag = $model->id;
                
                $model = new GoodsTags();
                $model->goods = $product->id;
                $model->tag = $tag;
                if ($model->validate()) {
                    $model->save();
                }
                
                
                foreach ($product->synonims as $synonim) {
                    $name = $product->brand_data->name." ".$synonim->name;
                    echo $name.PHP_EOL;
                    $model = new Tags();
                    $model->name = $name;
                    $model->type = Tags::TYPE_PRODUCT;
                    if ($model->validate()) {
                        $model->save();
                        $tag = $model->id;
                        $model = new GoodsTags();
                        $model->goods = $product->id;
                        $model->tag = $tag;
                        if ($model->validate()) {
                            $model->save();
                        }
                    }
                }
            }
        }
        
        unset($product, $products);
        
        $osItems = Os::model()->findAll(); 
        foreach ($osItems as $os) {
            $model = new Tags();
            $model->name = $os->name;
            $model->type = Tags::TYPE_OS;
            if ($model->validate()) {
                $model->save();
                $tag = $model->id;
                $model = new OsTags();
                $model->os = $os->id;
                $model->tag = $tag;
                if ($model->validate()) {
                    $model->save();
                }
            }
        }
        
        Tags::model()->filter();
    }

    public function actionImportYoutube()
    {
        /**
        Yii::app()->language = 'ru';
        $goods = Goods::model()->findall();
        foreach ($goods as $product) {
            $videos = $product->getVideos(false, 'ru');
            foreach ($videos as $video) {
                $model = new Videos();
                $model->goods = $product->id;
                $model->lang = 'ru';
                $model->type = Videos::TYPE_YOUTUBE;
                $model->link = $video;
                if ($model->validate()) {
                    $model->save();
                    echo ".";
                }
            }
        }
        **/
        Yii::app()->language = 'en';
        $goods = Goods::model()->findall();
        foreach ($goods as $product) {
            $videos = $product->getVideos(false, 'en');
            foreach ($videos as $video) {
                $model = new Videos();
                $model->goods = $product->id;
                $model->lang = 'en';
                $model->type = Videos::TYPE_YOUTUBE;
                $model->link = $video;
                if ($model->validate()) {
                    $model->save();
                    echo ".";
                }
            }
        }
        echo PHP_EOL;
    }
}
