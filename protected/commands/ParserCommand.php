<?php
class ParserCommand extends CConsoleCommand
{
    public function actionMob()
    {
        require 'phpQuery.php';
        $conn = Yii::app()->db;
        $storage = Yii::app()->mob;
        $select = "select * from mob where completed = 0";
        $update = "update mob set completed = 1 where id = :id";
        $items = $conn->createCommand($select)->queryAll();
        foreach ($items as $item)
        {
            $content = $storage->getFile($item['id']);
            $html = phpQuery::newDocumentHTML($content);

            $brand = pq($html)->find("h1.logo_page");
            $model = $brand->find("span")->text();
            $brand = $brand->text();
            $brand = mb_substr($brand, 0, mb_strlen($brand) - (mb_strlen($model)+1)); 
            $images = array();
            echo $brand." - ".$model.PHP_EOL;

            $id = $conn->createCommand("select g.id from goods_1 g inner join manufacturers m on g.manufacturer = m.id where CONCAT(m.name, ' ', g.name) = :model")->queryScalar(array('model'=>$brand." ".$model));

            $photos = pq($html)->find("#phonePhotoCont tr td > a");
            foreach ($photos as $photo)
            {
                $photo = pq($photo)->attr("onclick");
                $photo = explode(", '", $photo);
                $photo = mb_substr($photo[1], 0, mb_strlen($photo[1])-1);
                //echo $photo.PHP_EOL;
                echo ".";
                $images[] = $photo;
                if ($id)
                {
                    $conn->createCommand("insert ignore into temp_images (url, image) values (:url, :id)")->execute(array(
                        'url'=>$photo,
                        'id'=>$id,
                    ));
                }
            }
            $conn->createCommand($update)->execute(array('id'=>$item['id']));
            echo PHP_EOL;
        }
    }
}