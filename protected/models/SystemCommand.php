<?php
class SystemCommand extends ConsoleCommand
{
    public function actionRemoveDublicateFiles()
    {
        $model = new FilesModel();
        $model->removeDublicates();
    }
    
    public function actionResize()
    {
        $model = new ImagesModel();
        $file = new FilesModel();
        
        while ($need = $model->getForResize(1))
        {
            foreach ($need as $n)
            {
                $filePath = $file->getFileById($n['file']);
                echo PHP_EOL.$filePath;
                @unlink($filePath."_1");
                @unlink($filePath."_2");
                @unlink($filePath."_3");
                @unlink($filePath."_4");
                try 
                {
                    $im = new imagick($filePath);
                } catch (ImagickException $ex) {
                    $model->setResized(1, $n['id'], 2);
                    echo PHP_EOL."NOT RESIZED ".$n['id'];
                    continue;
                }
                
                $im->setImageFormat('png');
                $imageprops = $im->getImageGeometry();
                $width = $imageprops['width'];
                $height = $imageprops['height'];
                
                if ($width > 350)
                {
                    //350, 0 - big preview
                    $im->resizeImage(350, 0, imagick::FILTER_LANCZOS, 0.9);
                    $im->writeImage( $filePath."_".ImagesModel::SIZE_PREVIEW );
                }
                
                $im = new imagick($filePath);
                $im->setImageFormat('png');
                $imageprops = $im->getImageGeometry();
                $width = $imageprops['width'];
                $height = $imageprops['height'];
                
                if ($width > 93)
                {
                    //93, 0 - images list
                    $im->resizeImage(93, 0, imagick::FILTER_LANCZOS, 0.9);
                    $im->writeImage( $filePath."_".ImagesModel::SIZE_MEDIUM );
                }
                
                
                $im->resizeImage(37, 30, imagick::FILTER_LANCZOS, 0.9);
                $im->writeImage( $filePath."_".ImagesModel::SIZE_SMALL );
                $model->setResized(1, $n['id']);
                echo PHP_EOL." Image {$n['id']} resized!";
            }
            echo PHP_EOL;
        }
    }
    
    public function actionSphinx()
    {
        $conn = Yii::app()->db;
        $select = "select g.id, m.name as manufacturer, g.name as name from goods_1 g inner join manufacturers m on g.manufacturer = m.id";
        $items = $conn->createCommand($select)->queryAll();
        $insert = "insert ignore into goods_sphinx (language, goods, type, gname, mname, full) values (1, :goods, 1, :name, :manufacturer, :full)";
        foreach ($items as $item)
        {
            $conn->createCommand($insert)->execute(array(
                'goods'=>$item['id'],
                'name'=>$item['name'],
                'manufacturer'=>$item['manufacturer'],
                'full'=>$item['manufacturer']. " " . $item['name'],
            ));
        }   
    }
    
    public function actionMobiset()
    {
        $mobiset = MobisetImages::model()->findAll();
        $connection = Yii::app()->db;
        $find = "select g.id from goods_1 g inner join manufacturers m on m.id = g.manufacturer where concat(m.name, ' ', g.name) = :name";
        $new = "insert ignore into temp_images (url, image) VALUES (:url, :image)";
        foreach ($mobiset as $image)
        {
            $item = $connection->createCommand($find)->queryScalar(array(
                'name'=>$image->mobile
            ));
            if ($item)
            {
                echo ".";
                $connection->createCommand($new)->execute(array(
                    'url'=>$image->image,
                    'image'=>$item,
                ));
                $image->is_ok = 1;
            } else {
                $image->is_ok = 0;
            }
            $image->save();
        }
    }
    
    public function actionMobiset()
    {
        $mobiset = MobisetImages::model()->findAll();
        $connection = Yii::app()->db;
        $find = "select g.id from goods_1 g inner join manufacturers m on m.id = g.manufacturer where concat(m.name, ' ', g.name) = :name";
        $new = "insert ignore into temp_images (url, image) VALUES (:url, :image)";
        foreach ($mobiset as $image)
        {
            $item = $connection->createCommand($find)->queryScalar(array(
                'name'=>$image->mobile
            ));
            if ($item)
            {
                echo ".";
                $connection->createCommand($new)->execute(array(
                    'url'=>$image->image,
                    'image'=>$item,
                ));
                $image->is_ok = 1;
            } else {
                $image->is_ok = 0;
            }
            $image->save();
        }
    }
}