<?php
class ResizeCommand extends CConsoleCommand
{
    public function actionGoods()
    {
        $sizes = Images::$sizes;
        $criteria = new CDbCriteria();
        
        foreach ($sizes as $size=>$params)
        {
            $criteria->condition = "t.id not in (select image from {{images_resized}} where size = {$size}) and t.size = 1";
            $images = Images::model()->with(array(
                "file_data"=>array(
                    "joinType"=>"INNER JOIN",
                ),
            ))->findAll($criteria);
            foreach ($images as $image)
            {
                try 
                {
                    $imagick = new Imagick($image->file_data->getFilename());
                    $imagick->setImageFormat('jpeg');
                } catch (ImagickException $ex) {
                    echo $ex->getMessage().PHP_EOL;
                    continue;
                }
                
                $imageprops = $imagick->getImageGeometry();
                $width = $imageprops['width'];
                $height = $imageprops['height'];
                
                $imagick->thumbnailImage($params[0], $params[1]);
                $file = new Files();
                $file->save();
                try
                {
                    $filename = tempnam(Yii::app()->basePath."/runtime/", "resize");
                    $imagick->writeImage($filename);
                    $file->setFile($filename);
                    @unlink($filename);
                } catch (Exception $ex) {
                    echo $ex->getMessage().PHP_EOL;
                    $file->delete();
                    continue;
                }
                $file->name = preg_replace("~(.*)\.[a-z]+~", "$1.jpg", $image->file_data->name);
                $file->size = $file->getFilesize();
                $file->mime_type = $file->getMimeType();
                if (!$file->size || !preg_match("~image/~", $file->mime_type))
                {
                    $file->delete();
                    continue;
                }
                echo $file->name." ".$file->size.PHP_EOL;
                $file->save();
                
                $imageprops = $imagick->getImageGeometry();
                
                $resizedImage = new ImagesResized();
                $resizedImage->image = $image->id;
                $resizedImage->size = $size;
                $resizedImage->width = $imageprops['width'];
                $resizedImage->height = $imageprops['height'];
                $resizedImage->file = $file->id;
                $resizedImage->save();
            }
        }
    }
}

