<?php
class GalleryController extends Controller
{
    
    /**
     * Галерея картинок товара
     * @param string $product
     * @param int $page
     * @throws CHttpException
     */
    
    public function actionProduct($brand, $product, $page=null, $link=null, $id = null)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "t.link = :product and brand_data.link = :brand";
        $criteria->params = ['product'=>$product, 'brand'=>$brand];
        
        // Выбираем товар
        $product = Goods::model()->cache(60*60)->with([
            'brand_data', 
            'type_data',
            'primary_image',
            'primary_image.image_data',
        ])->find($criteria);
        
        if (!$product) {
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        }
        
        // Параметры для выборки галереи
        $criteria = new CDbCriteria();
        $criteria->condition = "t.goods = :product "
                . "and image_data.width > 299 "
                . "and image_data.height > 299 "
                ."and (
                    image_data.alt like concat('%', REPLACE(:brand_name, ' ', '_'), '%')
                    or image_data.alt like concat('%', REPLACE(:product_name, ' ', '_'), '%')
                )";
        $criteria->params = [
            'product' => $product->id,
            'product_name' => $product->name,
            'brand_name' => $product->brand_data->name,
        ];
        
        // Количество найденных элементов
        $count = Gallery::model()->cache(60*60)->with([
            'image_data'=>['joinType'=>'inner join'],
        ])->count($criteria);
        
        
        // Если ничего не нашлось, страницы нет
        if (!$count) {
            throw new CHttpException(404, Yii::t("errors", "Страница не найдена"));
        }
        // Пагинатор
        $pages = new CPagination($count);
        $pages->pageSize = Gallery::GALLERY_SIZE;
        $pages->applyLimit($criteria);
        
        $criteria->order = "field(image_data.type, 'goods', 'article'), t.id asc";
        // Добавляем 1 к limit что бы выбрать первый элемент следующей страницы
        $criteria->limit = $criteria->limit+1;
        
        if ($page > 0) {
            // Если это не первая страница, то добавляем к limit еще 1 и 
            $criteria->limit = $criteria->limit+1;
            // отнимаем от offset 1 что бы выбрать последний элемент предыдущей страницы
            $criteria->offset = $criteria->offset-1 ;
        }
        
        
        // Получаем галерею
        $gallery = (array) Gallery::model()->cache(60*60)->with([
            'image_data',
        ])->findAll($criteria);

        // По умолчанию нет предыдущей ссылки
        $prev_url = null;
        
        // Если это не первая страница
        if ($page > 0 && $criteria->offset > 0) {
            // Получаем id элемента предыдущей страницы
            $prev_url = Gallery::getUrl(
                        $product->brand_data->link, 
                        $product->link, 
                        $gallery[0]->image_data->name, 
                        $gallery[0]->id, ($page-1 == 1) ? null : $page-1);
            // Удаляем, т.к. это предыдущая страница
            unset($gallery[0]);
        }
        
        // По умолчанию последнего элемента нет
        $last_url = null;
        
        // Если не последняя страница
        if ($pages->getPageCount() != $page && $pages->getPageCount() > 1) {
            // Получаем последний элемент
            $last_url = end($gallery);
            $last_url = Gallery::getUrl(
                    $product->brand_data->link, 
                    $product->link, 
                    $last_url->image_data->name, 
                    $last_url->id, ($page+1 == 1) ? 2 : $page+1);
            // Удаляем последний элемент (следующая страница)
            unset($gallery[key($gallery)]);
        }
        $currentImage = null;

        // Перебираем галерею и устанавливаем предыдущие и следующие элементы
        foreach ($gallery as $key=>&$item) {
            
            $item->prev_url = $prev_url;
            if (isset($gallery[$key+1])) {
                $item->next_url = Gallery::getUrl(
                        $product->brand_data->link, 
                        $product->link, 
                        $gallery[$key+1]->image_data->name, 
                        $gallery[$key+1]->id, $page);
            } else {
                $item->next_url = $last_url;
            }
            
            $item->self_url = Gallery::getUrl(
                        $product->brand_data->link, 
                        $product->link, 
                        $item->image_data->name, 
                        $item->id, $page);
            
            $prev_url = Gallery::getUrl(
                    $product->brand_data->link, 
                    $product->link, 
                    $item->image_data->name, 
                    $item->id, $page);
            
            if ($currentImage == null && $item->id == $id) {
                $currentImage = $item;
            }
        }
        if ($currentImage == null) {
            $currentImage = reset($gallery);
        }
        // Строки по 6 элементов
        $gallery = array_chunk($gallery, 6);
        
        $this->pageTitle = Yii::t("main", "Фотогалерея")." ".$product->brand_data->name." ".$product->name;
        if (Yii::app()->language == 'ru') {
            $this->addKeywords([
                'фото',
                'картинки',
                'фотогалерея',
                $product->type_data->name->item_name,
                $product->brand_data->name,
                $product->name,
            ]);
            $this->addDescription("Фотографии {$product->type_data->name->item_name} {$product->brand_data->name} {$product->name}");
            if (!empty($currentImage->image_data->article->article_data) && $currentImage->image_data->article->article_data->lang == 'ru') {
                $this->addDescription($currentImage->image_data->article->article_data->title);
            }
            
        } else if (Yii::app()->language == 'en') {
            $this->addKeywords([
                'photo',
                'images',
                'gallery',
                $product->type_data->name->item_name,
                $product->brand_data->name,
                $product->name,
            ]);
            $this->addDescription("Photos {$product->type_data->name->item_name} {$product->brand_data->name} {$product->name}");
            if (!empty($currentImage->image_data->article->article_data) && $currentImage->image_data->article->article_data->lang == 'en') {
                $this->addDescription($currentImage->image_data->article->article_data->title);
            }
        }
        
        $characteristics = $product->getCharacteristics($product->generalCharacteristics);
        foreach ($characteristics as $catalog) {
            foreach ($catalog as $characteristic) {
                $this->addDescription($characteristic['characteristic_name'] . ": " . $characteristic['value'].",");
            }
        }
        
        
        $this->render('index', [
            'pages'=>$pages, 
            'gallery'=>$gallery, 
            'product'=>$product,
            'currentImage'=>$currentImage,
            'characteristics'=>$characteristics,
        ]);
    }
}