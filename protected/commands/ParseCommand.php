<?php
class ParseCommand extends CConsoleCommand
{
    public function actionTest()
    {
        $content = file_get_contents("/home/ilyaplot/analogindex/acer_allegro-3966.php");
        //$content = file_get_contents("/home/ilyaplot/analogindex/lg-g3.php");
        //$content = file_get_contents("/home/ilyaplot/analogindex/s4-mini.php");
        $content = file_get_contents("/home/ilyaplot/analogindex/iphone-5c.php");
        $html = phpQuery::newDocumentHTML($content);
        // Производитель
        $brand = pq($html)->find("#brandmenu ul li.on")->text();
        // Модель
        $name = pq($html)->find("#ttl h1")->text();
        $name = trim(str_replace($brand, "", $name));
        // Синонимы
        $synonimsText = pq($html)->find("#specs-list p:first-child")->html();
        $synonimsText = explode("<br>", $synonimsText);
        $synonims = "";
        foreach ($synonimsText as $synonim)
        {
            if (preg_match("~Also known as~", $synonim))
            {
                $synonims = trim($synonim);
                break;
            }
        }
        
        $synonims = str_replace("Also known as ", "", $synonims);
               
        if ($synonims)
        {
            $synonims = explode(", ", $synonims);
            foreach ($synonims as &$item)
            {
                $item = trim(str_replace($brand, "", $item));
                if (empty($item))
                    unset($item);
            }
        } else {
            $synonims = array();
        }
        
        // Характеристики
        $characteristics_tables = pq($html)->find("#specs-list > table");
        $characteristicsLines = array();
        foreach ($characteristics_tables as $index=>$table)
        {
            $characteristicHeader = pq($table)->find("tr:first-child th");
            $chHeader = trim($characteristicHeader->text($header));
            $characteristicHeader->remove();
            
            $rows = pq($table)->find("> tr");
            $lastRowName = '';
            foreach ($rows as $row)
            {
                $rowIndex = trim(pq($row)->find("td:first-child")->text());
                if (empty($rowIndex))
                {
                    $rowIndex = $lastRowName;
                }
                $lastRowName = $rowIndex;
                $rowValue = pq($row)->find("td:last-child")->html();
                $characteristicsLines[] = $chHeader.".".$rowIndex."::::".trim($rowValue);
            }
        }
        $parser = new GsmarenaCharacteristicsParser($characteristicsLines);
        $result = $parser->run();
        
        var_dump($result);

        // Ищем модель в бд
        $criteria = new CDbCriteria();
        $criteria->condition = 
                "(CONCAT(brand_data.name, ' ', t.name) LIKE :search "
                . "OR CONCAT(brand_data.name, ' ', synonims.name) LIKE :search) ";
        $search = $brand." ".$name;
        $search = htmlspecialchars($search);
        $search = str_replace("&nbsp;", " ", $search);
        $search = "{$search}";
        $criteria->params = array(
            "search"=>$search,
        );
        $goods = Goods::model()->with("brand_data","synonims")->find($criteria);
        // Если что-то нашли
        Yii::app()->language  = 'ru';
        if ($goods)
        {
            foreach ($result as $characteristic)
            {
                $goodsCharacteristic = new GoodsCharacteristics();
                $goodsCharacteristic->goods = $goods->id;
                $goodsCharacteristic->characteristic = $characteristic['id'];
                $goodsCharacteristic->lang = $characteristic['lang'];
                $goodsCharacteristic->value = is_array($characteristic['values']) ? json_encode($characteristic['values']) : $characteristic['values'];
                if ($goodsCharacteristic->validate())
                {
                    $goodsCharacteristic->save();
                } else {
                    var_dump($goodsCharacteristic->getErrors());
                }
            }
        }
        
    }
    
    public function actionFreq()
    {
        $goods = Goods::model()->findByPk(953);
        $goods->getCharacteristics();
    }
}