<?php
class TestCommand extends CConsoleCommand
{
    public function actionCharacteristicItem()
    {
        //public function __construct($id, $catalog, $name, $formatter, $description, $raw, $product = null)
        $item = new CharacteristicItem(1,"Пробный", "Тест", "formatSize", "Описание", "memory", "1024", Goods::model()->findByPk(1));
        echo $item->getValue(true);
        echo PHP_EOL;
        
    }
    
    public function actionIndex()
    {
        $product = Goods::model()->findByPk(1640);
        $characteristics = $product->getCharacteristicsNew(array("in"=>$product->generalCharacteristics, "createLinks"=>true));
        foreach ($characteristics as $characteristic) {
            echo $characteristic->getValue(false)." - ".$characteristic->getValue().PHP_EOL;
        }
    }
    
    public function actionParse()
    {
        $reviews = Reviews::model()->findAll();
        foreach ($reviews as $review) {
            if (empty($review->original)) {
                $review->original = $review->content;
                $review->save();
            } else if (empty($review->content) && !empty($review->original)) {
                $review->content = $review->original;
                $review->save();
            }
            
            $product = $review->goods_data;
            $characteristics = $product->getCharacteristicsNew(array("in"=>$product->generalCharacteristics, "createLinks"=>true));
            foreach ($characteristics as $characteristic) {
                $keyword = $characteristic->getValue(false);
                $value = $characteristic->getValue();
                
                if (mb_strlen($keyword) < 3)
                    continue;
                $keyword = str_replace(".", "\.", $keyword);
                $keyword = str_replace("(", "\(", $keyword);
                $keyword = str_replace(")", "\)", $keyword);
                if (!$characteristic->linkValue)
                    continue;
                
                if (preg_match_all("~([^>]{1}{$keyword}[^<]{1})~i", $review->content, $matches, PREG_PATTERN_ORDER)) {
                    echo $product->type_data->link."/".$product->brand_data->link."/".$product->link.".html";
                    echo " : ".$keyword.PHP_EOL;
                    echo $review->id." - ".$review->title.PHP_EOL.PHP_EOL;
                    
                    if (!empty($matches[1])) {
                        $strings = array_unique($matches[1]);
                    }
                    foreach ($strings as $string)
                    {
                        $review->content = str_replace($string, " ".$value." ", $review->content);
                       

                        echo $value.PHP_EOL;
                    }
                }
            }
            
            if (preg_match_all("~([^>]{1}{$product->brand_data->name}[^<]{1})~i", $review->content, $matches, PREG_PATTERN_ORDER)) {
                echo $product->type_data->link."/".$product->brand_data->link."/".$product->link.".html";
                echo " : ".$product->brand_data->name.PHP_EOL;
                echo $review->id." - ".$review->title.PHP_EOL.PHP_EOL;

                if (!empty($matches[1])) {
                    $strings = array_unique($matches[1]);
                }
                $value = CHtml::link($product->brand_data->name, Yii::app()->createUrl("site/type", array(
                    "language"=>  Language::getZoneForLang($review->lang),
                    "type"=>$product->type_data->link,
                    "brands"=>$product->brand_data->link,
                )));
                foreach ($strings as $string)
                {
                    $review->content = str_replace($string, " ".$value." ", $review->content);


                    echo $value.PHP_EOL;
                }
            }
            echo ".";
            $review->save();
            
        }
        
    }
}

