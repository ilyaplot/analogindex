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
        $ids = array(5, 6, 8, 13, 14);
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
            );

            if (!$model = CharacteristicsSelector::model()->findByPk($product->id))
                $model = new CharacteristicsSelector();


            $characteristics = $product->getCharacteristics(array(), true);

            $characteristicsLinks = new CharacteristicsLinks($characteristics);
            $attributes = $characteristicsLinks->getLinks($attributes);
            $model->attributes = $attributes;
            $model->save();
        }
    }

}
