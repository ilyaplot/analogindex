<?php
class ImportCommand extends CConsoleCommand
{
    public function actionBrands()
    {
        $query = "select * from manufacturers";
        $connection = Yii::app()->db;
        $results = $connection->createCommand($query)->queryAll();
        foreach ($results as $item)
        {
            $model = new Brands();
            $model->name = $item['name'];
            $model->link = $item['link'];        
            $model->save();
            
            $description = new BrandsDescriptions();
            $description->lang = 'ru';
            $description->brand = $model->id;
            $description->save();
            
            $description = new BrandsDescriptions();
            $description->lang = 'en';
            $description->brand = $model->id;
            $description->save();
        }
    }
    
    public function actionGoods()
    {
        $query = "select g.name, g.link, g.id, m.link as brand from goods_1 g inner join manufacturers m on m.id = g.manufacturer";
        $conn = Yii::app()->db;
        $goods = $conn->createCommand($query)->queryAll();
        foreach($goods as $item)
        {
            $brand = Brands::model()->findByAttributes(array("link"=>$item["brand"]));
            if (!$brand)
                continue;
            $model = new Goods();
            $model->type = 1;
            $model->brand = $brand->id;
            $model->name = $item['name'];
            $model->link = $item['link'];
            $model->temp_id = $item['id'];
            $model->save();
        }
        
    }
    
    
    public function actionImages()
    {
        $goods = Goods::model()->findAll();
        $imagesModel = new ImagesModel();
        foreach ($goods as $item)
        {
            $images = $imagesModel->getForGoods($item->temp_id, 1);
            if (!$images)
                continue;
            foreach ($images as $image)
            {
                if (!$image['filesize'])
                    continue;
                
                if (!file_exists("/inktomia/db/analogindex/files/".ceil($image['file']/10000)."/".md5($image['file'])))
                    continue;
                
                if (preg_match("~<html>~", file_get_contents("/inktomia/db/analogindex/files/".ceil($image['file']/10000)."/".md5($image['file']))))
                    continue;
                
                $model = new Images();
                $file = new Files();
                $file->save();
                $file->setFile("/inktomia/db/analogindex/files/".ceil($image['file']/10000)."/".md5($image['file']));
                $file->name = $item->brand_data->link."_".$item->link.".".$image['ext'];
                echo $file->name.PHP_EOL;
                $file->size = $file->getFilesize();
                $file->mime_type = $file->getMimeType();
                if (!$file->size || !preg_match("~image/~", $file->mime_type))
                {
                    $file->delete();
                    continue;
                }
                $file->save();
                $model->file = $file->id;
                $model->size = Images::SIZE_BIG;
                $size = getimagesize($file->getFilename());
                if (!is_array($size))
                {
                   $file->delete();
                   continue;
                }
                $model->width = $size[0];
                $model->height = $size[1];
                $model->save();
                
                $goodsImage = new GoodsImages();
                $goodsImage->goods = $item->id;
                $goodsImage->image = $model->id;
                $goodsImage->save();
            }
        }
    }
}
