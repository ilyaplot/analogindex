<?php
class ParseCommand extends CConsoleCommand
{
    public function actionGsmArena()
    {
        $task = SourcesGsmarena::model()->with("file_data")->findByAttributes(array("completed"=>0));
        if (!$task)
        {
            echo "No tasks for parse".PHP_EOL;
            exit();
        }
        $content = $task->file_data->getContent();
        if (!$content)
        {
            $task->completed = -1;
            $task->save();
            echo "Null content for task".PHP_EOL;
        }
        $html = phpQuery::newDocumentHTML($content);
        // Производитель

        $brand = trim(str_replace(" phones", "" ,pq($html)->find("#all-phones h2 a")->text()));
        if (!$brand)
            $brand = trim(str_replace(" phones", "" ,pq($html)->find("#all-phones-small h2 a")->text()));
        
        $brand = trim(str_replace(" PHONES", "" ,$brand));

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
        
        //var_dump($result);

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
        echo $search.PHP_EOL;
        $goods = Goods::model()->with("brand_data","synonims")->find($criteria);
        // Если что-то нашли
        Yii::app()->language  = 'ru';
        if (!$goods)
        {
            if (!$brandModel = Brands::model()->findByAttributes(array("name"=>$brand)))
            {
                $brandModel = new Brands();
                $brandModel->name = $brand;
                $brandModel->link = Model::str2url($brand);
                if ($brandModel->validate())
                {
                    echo "Добавлен бренд {$brand}".PHP_EOL;
                    $brandModel->save();
                } else {
                    var_dump($brandModel->getErrors());
                    $task->completed = -1;
                    $task->save();
                    return $this->actionGsmArena();
                }
            }
            $goods = new Goods();
            $goods->brand = $brandModel->id;
            $goods->name = $name;
            $goods->link = Model::str2url($name);
            $goods->type = 1;
            echo Model::str2url($name).PHP_EOL;
            if ($goods->validate())
            {
                echo "Добавлен товар {$brand} {$name}".PHP_EOL;
                $goods->save();
            } else {
                echo "Не добавлен товар {$brand} {$name}".PHP_EOL;
                var_dump($goods->getErrors());
                $task->completed = -1;
                $task->save();
                return $this->actionGsmArena();
            }
        }
        
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
                //var_dump($goodsCharacteristic->getErrors());
            }
        }
        $task->completed = 1;
        $task->save();
        $this->actionGsmArena();
    }
    
}