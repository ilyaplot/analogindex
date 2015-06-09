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

    /*
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
     * 
     */

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
                echo $brand->name . PHP_EOL;
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


            $name = $product->brand_data->name . " " . $product->name;
            $model = new Tags();
            $model->name = $name;
            $model->type = Tags::TYPE_PRODUCT;


            if ($model->validate()) {
                echo $name . PHP_EOL;
                $model->save();
                $tag = $model->id;

                $model = new GoodsTags();
                $model->goods = $product->id;
                $model->tag = $tag;
                if ($model->validate()) {
                    $model->save();
                }


                foreach ($product->synonims as $synonim) {
                    $name = $product->brand_data->name . " " . $synonim->name;
                    echo $name . PHP_EOL;
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
         * */
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

    public function actionGetYoutubeSnippets()
    {

        $videos = Videos::model()->findAll();
        foreach ($videos as $video) {

            $snippet = $video->getYoutubeSnippet($video->link);
            if ($snippet === false) {
                $video->delete();
                echo $video->link . " deleted" . PHP_EOL;
            } else {
                $video->title = $snippet->title;
                $video->description = $snippet->description;
                $video->duration = $snippet->duration;
                $video->thumbnail = $snippet->thumbnail;
                $video->date_added = $snippet->date_added;
                $video->save();
                echo $video->link . " updated" . PHP_EOL;
            }
        }
    }

    public function actionPosting($lang)
    {
        // Нужно только для удаления script из кода
        require 'phpQuery.php';

        $apis = [
            // id приложения
            // секретный ключ
            // ключ доступа
            // id паблика
            // id альбома для загрузки фотографий
            'ru' => [
                4943931,
                'b4r92CYlgPPUG6NcSSi1',
                '299375e7a6a2c136e3d5ffb95a69fc5ba7ad45d8d420846d7df79e86bfc41290b277aa87eb5a9959dc832',
                95455492,
                216269780,
            ],
            'en' => [
                4934698,
                '3djYV1o2nXEQCzydPGTn',
                'd8ebb4f7ad6667e9792f8b14e2f33b00608f0e6f0e7ae76c150be49ec615d67e3224dd0146f42001716b5',
                95455563,
                216611873,
            ]
        ];
        $criteria = new CDbCriteria();
        $criteria->condition = 't.type = :type and t.created >= CURDATE() and !t.vk_post_id and t.lang = :lang';
        $criteria->params = [
            'type' => Articles::TYPE_NEWS,
            'lang' => $lang
        ];
        $criteria->order = 'rand()';
        $criteria->limit = 1;
        Yii::app()->language = $lang;
        $article = Articles::model()->find($criteria);

        if (!$article) {
            echo "Empty set, exiting." . PHP_EOL;
            exit();
        }

        //
        // Данные для API
        $api = new VkApi($apis[$lang][0], $apis[$lang][1], $apis[$lang][2]);
        // Раскомментить для получения ключа
        //$api->authorize(); exit();
        $attachments = array();


        if (!empty($article->preview_image->filename) && file_exists($article->preview_image->filename)) {
            echo $article->preview_image->filename . PHP_EOL;
            // Получаем адрес сервера для загрузки фотографии
            $result = $api->run('photos.getUploadServer', array(
                'group_id' => (int) $apis[$lang][3],
                'album_id' => (int) $apis[$lang][4],
            ));

            $upload_url = $result->upload_url;

            $postData = array(
                'file1' => new CURLFile($article->preview_image->filename),
            );

            $upload_result = $api->upload($upload_url, $postData);
            //var_dump($upload_result);
            if (!empty($upload_result->photos_list)) {
                // Сохраняем загруженную фотку в альбоме группы
                $result = $api->run('photos.save', array(
                    'album_id' => $upload_result->aid,
                    'group_id' => $upload_result->gid,
                    'server' => $upload_result->server,
                    'photos_list' => $upload_result->photos_list,
                    'hash' => $upload_result->hash,
                    'caption' => htmlspecialchars_decode($article->title),
                ));

                $results = array($result);
                foreach ($results as $result) {
                    foreach ($result as $uploaded) {
                        $attachments[] = $uploaded->id;
                    }
                }
            }
        }
        $tags = [];
        $relatedVideos = [];
        $relatedProducts = [];
        foreach ($article->tags as $tag) {
            if (!empty($tag->tag_data)) {
                $tags[] = $tag->tag_data->name;
            }

            if (!empty($tag->tag_data->name) && $tag->tag_data->disabled == 0) {

                if ($tag->tag_data->type == 'product' && !empty($tag->tag_data->goods->goods)) {
                    $relatedProducts[] = $tag->tag_data->goods->goods;
                }
            }
        }



        $tags = array_map(function($value) {
            $value = '#' . preg_replace('/\s+/isu', "_", trim(htmlspecialchars_decode($value)));
            $value = preg_replace("/[^\w\_#]+/isu", '', $value);
            return $value;
        }, $tags);

        $tags = array_unique($tags);

        // Чистим описание
        $description = html_entity_decode(strip_tags($article->description)) . PHP_EOL;
        $description = preg_replace('/\s+/isu', ' ', $description);

        // Формируем тело поста
        $message = htmlspecialchars_decode($article->title) . '... ' . PHP_EOL;
        $message .= implode(" ", $tags) . PHP_EOL;
        $message .= $description . PHP_EOL;


        $videos = [];

        if (!empty($article->content_videos)) {
            foreach ($article->content_videos as $video) {
                $videos[] = $video;
            }
        } else  if (!empty($relatedProducts)) {
            // Упомянутые товары
            $criteria = new CDbCriteria();
            $criteria->addInCondition('t.id', $relatedProducts);
            $criteria->group = 't.id';
            $criteria->order = "t.updated asc";
            $widgets['related_products'] = Goods::model()->cache(60 * 60 * 24)->with(["brand_data", 'type_data', 'primary_video'])->findAll($criteria);

            foreach ($widgets['related_products'] as $product) {
                if (!empty($product->primary_video->link) && !in_array($product->primary_video->link, $relatedVideos)) {
                    $videos[] = $product->primary_video;
                }
            }
        }

        if (!empty($videos)) {
            foreach ($videos as $video) {

                sleep(1);
                $videoResult = $api->run('video.save', array(
                    'name' => strip_tags(htmlspecialchars_decode($video->title)),
                    'description' => strip_tags(htmlspecialchars_decode($video->description)),
                    'wallpost' => 0,
                    'link' => 'http://www.youtube.com/watch?v=' . $video->link,
                    'group_id' => $apis[$lang][3],
                    'album_id' => 1,
                    'privacy_view' => 'all',
                    'repeat' => 0,
                ));

                if (!empty($videoResult->upload_url)) {
                    $res = @json_decode(file_get_contents($videoResult->upload_url));
                    if ((!empty($res->response) && $res->response == 1) || (!empty($res->error_code) && $res->error_code == 15)) {
                        $attachments[] = 'video-' . $apis[$lang][3] . '_' . $videoResult->vid;
                    }
                }
            }
        }


        $attachments[] = Yii::app()->createAbsoluteUrl("articles/index", ['type' => $article->type, 'link' => $article->link, 'id' => $article->id, 'language' => Language::getZoneForLang($article->lang)]);
        // Формироем запрос на добавление поста
        $postParams = array(
            'owner_id' => ($apis[$lang][3]) * -1,
            'from_group' => 1,
            'message' => $message,
            'services' => 'twitter',
            'attachments' => str_replace('67186202', '-' . $apis[$lang][3], implode(",", $attachments)),
                //'captcha_sid'=>'604216209994',
                //'captcha_key'=>'sp7p5v',
        );

        // Постим!
        sleep(1);
        $result = $api->run('wall.post', $postParams);
        //var_dump($result);
        $article->vk_post_id = $result->post_id;
        $article->save();
    }

    public function actionTweet($lang)
    {
        $twitterPath = realpath(dirname(dirname(__FILE__)) . "/components/twitter/");

        include_once $twitterPath . "/Config.php";
        include_once $twitterPath . "/SignatureMethod.php";
        include_once $twitterPath . "/HmacSha1.php";
        include_once $twitterPath . "/Response.php";
        include_once $twitterPath . "/Consumer.php";
        include_once $twitterPath . "/Token.php";
        include_once $twitterPath . "/Request.php";
        include_once $twitterPath . "/Util.php";
        include_once $twitterPath . "/Util/JsonDecoder.php";
        include_once $twitterPath . "/TwitterOAuth.php";
        #############

        $apis = [
            // id приложения
            // секретный ключ
            // ключ доступа
            // id паблика
            // id альбома для загрузки фотографий
            'ru' => [
                'CONSUMER_KEY' => 'ljOvX5bYwX0EVZGM5CnAwgnCW',
                'CONSUMER_SECRET' => '1CUIiwSvNINOpNE7Da9EoAofnz9FMsiBB5VxXVUCXgf4S1lBC9',
                'OAUTH_TOKEN' => '3306970905-18lZw8CJIvJdtZwiChnWGrnVULWrsuKHndX7YgI',
                'OAUTH_SECRET' => '9OnYW5qXOFHqOQSCeSZjBzQiWM4VVGtAHNLqsynhBXMc6',
            ],
            'en' => [
                'CONSUMER_KEY' => 'sQu2nCWKrFusDNnvnXkwrdbx4',
                'CONSUMER_SECRET' => '3N178gTzxGZ88NruDD9xqbhziix8R8AzUwDKBYEnpqsINrSvuw',
                'OAUTH_TOKEN' => '3307212963-pDjIRnnoFdgrfFUl9Rqr1xOFtpVY5NBJ1RFgAki',
                'OAUTH_SECRET' => 'EDHPbk3HWLdP19x7Bs4XHes6RAwMlvnRvkYYpmmOoPJlo',
            ]
        ];

        $options = $apis[$lang];

        $criteria = new CDbCriteria();
        $criteria->condition = 't.created >= CURDATE() and !t.tweet_id and t.lang = :lang';
        $criteria->params = [

            'lang' => $lang
        ];
        $criteria->order = 'rand()';
        $criteria->limit = 1;
        Yii::app()->language = $lang;
        $article = Articles::model()->find($criteria);

        if (!$article) {
            echo "Empty set, exiting." . PHP_EOL;
            exit();
        }

        $connection = new Abraham\TwitterOAuth\TwitterOAuth($options['CONSUMER_KEY'], $options['CONSUMER_SECRET'], $options['OAUTH_TOKEN'], $options['OAUTH_SECRET']);

        $images = array();
        $medias = array();

        if (!empty($article->preview_image->filename) && file_exists($article->preview_image->filename)) {
            echo $article->preview_image->filename.PHP_EOL;
            $images[] = $article->preview_image->filename;
        }

        foreach ($images as $image) {
            usleep(500);
            $upload = $connection->upload("media/upload", array('media' => $image));
            if (!empty($upload->media_id) && count($medias) < 4) {
                $medias[] = $upload->media_id;
            } else {
                break;
            }
        }



        $tags = [];

        $relatedProducts = [];
        foreach ($article->tags as $tag) {
            if (!empty($tag->tag_data)) {
                $tags[] = $tag->tag_data->name;
            }

            if (!empty($tag->tag_data->name) && $tag->tag_data->disabled == 0) {

                if ($tag->tag_data->type == 'product' && !empty($tag->tag_data->goods->goods)) {
                    $relatedProducts[] = $tag->tag_data->goods->goods;
                }
            }
        }



        $tags = array_map(function($value) {
            $value = '#' . preg_replace('/\s+/isu', "_", trim(htmlspecialchars_decode($value)));
            $value = preg_replace("/[^\w\_#]+/isu", '', $value);
            return $value;
        }, $tags);

        $tags = array_unique($tags);

        $messageLimit = 117;

        if (!empty($medias)) {
            $messageLimit -= 23;
        }

        $message = '';

        $maxTags = 7;
        foreach ($tags as $tag) {
            if ((mb_strlen($message) + mb_strlen($tag) + 1) > $messageLimit && $maxTags > 0) {
                continue;
            }
            $maxTags--;
            $message .= $tag . ' ';
        }




        if (mb_strlen($message) - $messageLimit >= 8) {
            $message .= mb_substr(htmlspecialchars_decode($article->title), 0, mb_strlen($message) - $messageLimit - 1) . ' ';
        }

        $message .= Yii::app()->createAbsoluteUrl("articles/index", ['type' => $article->type, 'link' => $article->link, 'id' => $article->id, 'language' => Language::getZoneForLang($article->lang)]);

        $params = array(
            "status" => $message,
        );

        if (!empty($medias)) {
            $params['media_ids'] = implode(",", $medias);
        }

        $status = $connection->post("statuses/update", $params);
        if (!empty($status->id)) {
            $article->tweet_id = $status->id;
            $article->save();
        }
        
        var_dump($status);
    }

    public function actionUpdateVideos()
    {
        $criteria = new CDbCriteria();
        //$criteria->condition = "t.duration = '0000-00-00 00:00:00' or t.title = ''";
        $videos = Videos::model()->findAll($criteria);
        foreach ($videos as $video) {
            $snipet = $video->getYoutubeSnippet($video->link, true);
            if (!$snipet || $snipet->duration == '0000-00-00 00:00:00' || empty($snipet->title)) {
                $video->delete();
                echo "-";
            } else {
                echo "+";
                $video->save();
            }
        }
    }
}
