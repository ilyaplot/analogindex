<?php
class GalleryController extends Controller
{
    public function actionProductRedirect($brand, $product)
    {
        Yii::app()->request->redirect("/gallery/{$brand}_{$product}.html", true, 301);
        exit();
    }
    
    public function actionProduct($brand, $product, $prefix=null, $alt=null, $id=null)
    {
        $brand = Brands::model()->findByAttributes(array("link" => $brand));
        if (!$brand)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        
        $criteria = new CDbCriteria();
        $criteria->condition = "t.link = :link and t.brand = :brand";
        $criteria->params = array("link" => $product, "brand" => $brand->id);
        
        $product = Goods::model()->with(['type_data'])->find($criteria);

        if (!$product) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        }
        
        
        list($countImages, $gallery, $image) = Images::model()->getProductGallery($product->id, $prefix, $id);
        
        if ($image == null && ($prefix != null)) {
            Yii::app()->request->redirect("/gallery/{$brand->link}_{$product->link}.html", true, 302);
        } else if ($image == null) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        }
        
        $gallery = array_chunk($gallery, 6);
        
        
        $this->pageTitle = Yii::t("main", "Фотогалерея")." ".$brand->name." ".$product->name;
        if (Yii::app()->language == 'ru') {
            $this->addKeywords([
                'фото',
                'картинки',
                'фотогалерея',
                $product->type_data->name->item_name,
                $brand->name,
                $product->name,
            ]);
            $this->addDescription("Фотографии {$product->type_data->name->item_name} {$brand->name} {$product->name}");
            if (!empty($image->article) && $image->article->lang == 'ru') {
                $this->addDescription($image->article->title);
            }
            
        } else if (Yii::app()->language == 'en') {
            $this->addKeywords([
                'photo',
                'images',
                'gallery',
                $product->type_data->name->item_name,
                $brand->name,
                $product->name,
            ]);
            $this->addDescription("Photos {$product->type_data->name->item_name} {$brand->name} {$product->name}");
            if (!empty($image->article) && $image->article->lang == 'en') {
                $this->addDescription($image->article->title);
            }
        }
        
        $characteristics = $product->getCharacteristics($product->generalCharacteristics);
        foreach ($characteristics as $catalog) {
            foreach ($catalog as $characteristic) {
                $this->addDescription($characteristic['characteristic_name'] . ": " . $characteristic['value'].",");
            }
        }
        
        $this->render("image", [
            'characteristics'=>$characteristics,
            'product'=>$product, 
            'brand'=>$brand, 
            'gallery'=>$gallery, 
            'image'=>$image, 
            'countImages'=>$countImages
        ]);
    }
}