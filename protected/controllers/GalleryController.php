<?php
class GalleryController extends Controller
{
    public function actionProduct($brand, $product, $page=null)
    {
        $brand = Brands::model()->findByAttributes(array("link" => $brand));
        if (!$brand)
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        
        $criteria = new CDbCriteria();
        $criteria->condition = "t.link = :link and t.brand = :brand";
        $criteria->params = array("link" => $product, "brand" => $brand->id);
        
        $product = Goods::model()->find($criteria);

        if (!$product) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        }
        
        $this->pageTitle = Yii::t("main", "Фотогалерея")." ".$brand->name." ".$product->name;
        
        $page = abs(intval($page));
        if ($page > 0) $page--;

        list($countImages, $gallery) = Images::model()->getProductGallery($product->id, $page);
        
        //if ($countImages < $page+1) {
        //    return false;
        //}

        $image = $gallery[$page];
        $gallery = array_chunk($gallery, 6);
        $this->render("image", ['product'=>$product, 'brand'=>$brand, 'gallery'=>$gallery, 'image'=>$image, 'countImages'=>$countImages]);
    }
}