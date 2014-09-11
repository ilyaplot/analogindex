<?php
class SystemCommand extends ConsoleCommand
{
    public function actionSendEmails()
    {
        $to = "ilyaplot@gmail.com";
        $subject = "Подтверждение регистрации";
        $message = "Данунах";
        
        $mailer = Yii::app()->Smtpmail;
        $mailer->IsSMTP();
        $mailer->IsHTML(true);
        $mailer->Subject = $subject;
        $mailer->AddAddress($to);
        $mailer->Body = $message;
        
        if (!$mailer->Send())
        {
            echo $mailer->ErrorInfo.PHP_EOL;
        } else {
            Echo 'EMail OK'.PHP_EOL;
        }
    }
    
    public function actionTest()
    {
        $connection = Yii::app()->reviews;
        $query = "select r.id, r.url, d.name, d.rating, r.title, r.content as content from destinations d inner join reviews r on d.id = r.destination where d.name = :name";
        $goods = Goods::model()->findAll();
        foreach ($goods as $item)
        {
            $params = array('name'=>$item->brand_data->name. " " .$item->name);
            $append = '';
            foreach ($item->synonims as $key=>$synonim)
            {
                $append.=' and name = :name'.$key.'';
                $params['name'.$key] = $item->brand_data->name." ".$synonim->name;
            }
            $urlManager = new UrlManager();
            $reviews = $connection->createCommand($query.$append)->queryAll(true, $params);
            if ($reviews)
            {
                foreach ($reviews as $review)
                {
                    $ratingsCount = RatingsGoods::model()->countByAttributes(array(
                        "goods"=>$item->id,
                        "user"=>0,
                        "value"=>doubleval($review['rating']),
                    ));
                    if (!$ratingsCount)
                    {
                        $rating = new RatingsGoods();
                        $rating->goods = $item->id;
                        $rating->user = 0;
                        $rating->value = doubleval($review['rating']);
                        $rating->save();
                        echo $item->id." ".$ratingsCount." ".$item->brand_data->name. " " .$item->name." ";
                        echo "Добавлен рейтинг".PHP_EOL;
                    }
                    $reviewCount = Reviews::model()->countByAttributes(array(
                        "goods"=>$item->id,
                        "source"=>$review['url'],
                    ));
                    if (!$reviewCount)
                    {
                        $reviewModel = new Reviews();
                        $reviewModel->goods = $item->id;
                        $reviewModel->source = $review['url'];
                        $reviewModel->title = $review['title'];
                        $reviewModel->lang = 'ru';
                        $reviewModel->link = $urlManager->translitUrl($review['title']);
                        $reviewModel->author = 0;
                        $reviewModel->content = $review['content'];
                        $reviewModel->disabled = 0;
                        if ($reviewModel->validate())
                            $reviewModel->save();
                        echo $item->id." ".$reviewCount." ".$item->brand_data->name. " " .$item->name." ";
                        echo "Добавлен обзор".PHP_EOL;
                    }
                }
            }
        }
    }
}