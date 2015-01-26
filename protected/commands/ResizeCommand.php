<?php

class ResizeCommand extends CConsoleCommand
{

    public function actionIndex()
    {
        // http://wideimage.sourceforge.net/
        include 'WideImage/WideImage.php';

        $sizes = Images::$sizes;
        $criteria = new CDbCriteria();
        //$criteria->limit = 200;
        foreach ($sizes as $size => $params) {
            $criteria->condition = "t.size{$size} = 0";
            $images = Images::model()->with(array(
                        "file_data" => array(
                            "joinType" => "INNER JOIN",
                        ),
                    ))->findAll($criteria);
            foreach ($images as $image) {
                $file = new Files();
                $file->save();
                try {
                    $temp = "/tmp/" . md5(time() . microtime()) . ".jpg";
                    WideImage::load($image->file_data->getFilename())->resize($params[0], $params[1])->saveToFile($temp);
                    rename($temp, $file->getFilename());
                } catch (Exception $ex) {
                    echo $ex->getMessage() . PHP_EOL;
                    $file->delete();
                    continue;
                }

                $file->name = preg_replace("~(.*)\.[a-z]+~", "$1.jpg", $image->file_data->name);
                $file->size = $file->getFilesize();
                $file->mime_type = $file->getMimeType();

                if (!$file->size || !preg_match("~image/~", $file->mime_type)) {
                    $file->delete();
                    continue;
                } else {
                    $sz = "size{$size}";
                    $image->$sz = $file->id;
                    $image->save();
                }
                echo $file->getFilename() . PHP_EOL;
                echo $file->name . " " . $file->size . " " . $size . " " . $image->file_data->id . PHP_EOL;
                $file->save();
            }
        }
        
        ArticlesImages::model()->createPreviews();
    }

    public function actionCount()
    {
        $sizes = Images::$sizes;
        $criteria = new CDbCriteria();

        foreach ($sizes as $size => $params) {
            $criteria->condition = "t.id not in (select image from {{images_resized}} where size = {$size}) and t.size = 1";
            $images = Images::model()->with(array(
                        "file_data" => array(
                            "joinType" => "INNER JOIN",
                        ),
                    ))->count($criteria);
            echo $images . " " . $size . PHP_EOL;
        }
    }

}
