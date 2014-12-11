<?php
foreach ($newsTags as $link) {
            $news = $link->news_data;
            $link = Yii::app()->createAbsoluteUrl("news/index", ['link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getCurrentZone()]);
            echo CHtml::link($news->title, $link)."<br/ >".PHP_EOL;
        }
        ?>