<?php
class GoodsModel extends Model
{
    public $id;
    public $name;
    public $sql;
    public $lang;
    
    const LIST_ORDER_RATING = 1;
    const LIST_ORDER_LIKES = 2;
    const LIST_ORDER_PRICE = 3;
    const LIST_ORDER_NAME = 4;
    
    public function __construct() {
        $this->sql = Yii::app()->db;
        $this->lang = Language::getCurrentLang();
    }

    /**
     * Получает список товаров для виджета
     * @param int $type Тип товаров
     * @param int $limit Количество записей
     * @param int $order Параметр сортировки (см self::LIST_ORDER_...)
     * @param bool $desc true - от лучшего к худшему, false - наоборот
     */
    public function getWidgetList($type, $limit = 5, $order = null, $desc = true)
    {
        $manufacturer = new ManufacturersModel();
        $images = new ImagesModel();
        
        $sorts = array(
            1=>'DESC',
            0=>'ASC',
        );
        // Строим массив сортировки
        switch ($order)
        {
            // Сортировка по цене
            case self::LIST_ORDER_PRICE :
                $order = array(
                    "o.price ".$sorts[!$desc],
                    "g.likes ".$sorts[$desc],
                    "g.rating ".$sorts[$desc],
                );
            break;
            // Сортировка по рейтингу
            case self::LIST_ORDER_RATING :
               $order = array(
                   "g.rating ".$sorts[$desc],
                   "g.likes ".$sorts[$desc],
                   "o.price ".$sorts[!$desc],
               );    
            break;
        
            case self::LIST_ORDER_NAME :
               $order = array(
                   "g.name ".$sorts[!$desc],
               );    
            break;
            default:
            // Сортировка по лайкам
            case self::LIST_ORDER_LIKES :
                $order = array(
                    "g.likes ".$sorts[$desc],
                    "g.rating ".$sorts[$desc],
                    "o.price ".$sorts[!$desc],
                );
            break;
        }
        
        
        $query = "SELECT
            g.id,
            g.name,
            g.manufacturer,
            g.link,
            o.price as price,
            ROUND(g.rating) as rating,
            ROUND(g.likes) as likes
        FROM goods_{$type} g
        LEFT JOIN offers_{$type} o ON (
            g.id = o.goods 
            and o.price = (
                select min(price)
                from offers_{$type}
                where goods = g.id
            )
        )
        ORDER BY 
            ".implode(", ", $order)."
        LIMIT {$limit}";
        $list = $this->sql->createCommand($query)->queryAll();
        
        foreach ($list as $key=>&$item)
        {
            $item['manufacturer'] = $manufacturer->getById($item['manufacturer']);
            $item['image'] = $images->getParent($item['id'], $type, ImagesModel::SIZE_SMALL);
            $item['price'] = doubleval($item['price']);
        }
        
        return $list;
    }
    
    public function sphinx($ids)
    {
        if (empty($ids))
            return array();
        $type = 1;
        $ids = array_reverse($ids);
        $query = " select 
                g.id,
                g.name,
                g.likes,
                g.rating,
                g.link,
                m.link as mlink,
                m.name as manufacturer
            from
                goods_{$type} g
            inner join
                manufacturers m
                on g.manufacturer = m.id
            where g.id IN (".  implode(", ", $ids).")
            order by field(g.id,".  implode(", ", $ids).")
 ";
             $result = $this->sql->createCommand($query)->queryAll();
             $images= new ImagesModel();
             foreach ($result as &$item)
             {
                 $item['image'] = $images->getParent($item['id'], $type, ImagesModel::SIZE_MEDIUM);
             }
             return $result;
    }


    public function add($type, $data, $source='analogindex', $lang = 'ru')
    {
        
        $transaction = $this->sql->beginTransaction();
        $insert = "
            INSERT IGNORE INTO
                goods_{$type}
            (name, link, manufacturer, description)
            VALUES
            (:name, :link, :manufacturer, :description)
            ";
        $sphinx = "INSERT INTO goods_sphinx 
            (`language`,
            `goods`, 
            `type`, 
            `gname`, 
            `mname`, 
            `full`) 
            VALUES 
            (1, :id, :type, :gname, :mname, :fullname)";
        $id = false;
        if ($data['manufacturer'] == "Другие")
        {
            $transaction->rollback();
            return false;
        }
        try 
        {
            $filesModel = new FilesModel();
            $imagesModel = new ImagesModel();
            $manufacturerModel = new ManufacturersModel();
            $manufacturerName = self::prepeareName($data['manufacturer']);
            $name = $data['name'];
            $link = $this->str2url($name);
            
            $name = self::prepeareName($data['name']);

            $mid = $manufacturerModel->add($type, $manufacturerName, "");
            
            $id = $this->sql
                        ->createCommand("select id from goods_{$type} where manufacturer = :manufacturer and link = :link")
                        ->queryScalar(array('manufacturer'=>$mid, 'link'=>$link));
            
            $params = array(
                'name'=>$name,
                'link'=>$link,
                'manufacturer'=> $mid,
                'description'=>'',
            );
            if (!$id)
            {
                $this->sql->createCommand($insert)->execute($params);
                $id = $this->sql->lastInsertID;
                
                $this->sql->createCommand($sphinx)->execute(array(
                    'id'=>$id,
                    'type'=>$type,
                    'gname'=>$name,
                    'mname'=>$manufacturerName,
                    'fullname'=>$manufacturerName . " " . $name,
                ));
            }
            
            $characteristicsModel = new CharacteristicsModel();
            foreach ($data['characteristics'] as &$val)
            {
                if (is_array($val['value']))
                    $val['value'] = json_encode ($val['value']);
                $chid = $characteristicsModel->addMain($type, $val['name'], $lang);
                $characteristicsModel->add($type, $id, $chid, $val['value']);
            }
            
            if (isset($data['images']))
            {
                foreach ($data['images'] as $src=>$filename)
                {
                    $link = basename(str_replace("://", "/", $src));
                    $exp = explode(".", $link);
                    $ext = end($exp);
                    $fileId = $filesModel->add($filename, $ext);
                    $link = explode("/", $link);
                    $link = end($link);
                    echo $src. " - ". $imagesModel->add($type, $id, $fileId, $src, $link).PHP_EOL;
                }
            }
            
            $transaction->commit();
        } 
        catch (Exception $ex) 
        {
            $transaction->rollback();
            throw $ex;
            //var_dump($data);
        }
        
    }
    
    public function getForPage($link, $type=1)
    {
        $select = "
            select 
                g.id,
                g.name,
                g.likes,
                g.rating,
                g.link,
                m.name as manufacturer
            from
                goods_{$type} g
            inner join
                manufacturers m
                on g.manufacturer = m.id
            where g.link like :link    ";
                
        $data = $this->sql->createCommand($select)->queryRow(true, array('link'=>$link));
        $data['characteristics'] = array();
        
        $characteristics = "
            select 
                n.name,
                gc.value
            from 
                characteristics c
            inner join goods_{$type}_characteristics gc
                on gc.characteristic = c.id
            inner join characteristics_names n 
                on n.characteristic = c.id and n.language = :lang
            where 
                gc.goods = :id
            ";
        $data['characteristics'] = $this->sql->createCommand($characteristics)->queryAll(true, array(
            'id'=>$data['id'],
            'lang'=>  $this->lang,
        ));
        
        foreach ($data['characteristics'] as $ch=>&$val)
        {
            $v = json_decode($val['value'], true);
            if (is_array($v))
            {
                $val['value'] = implode(", ", $v);
            }
        }
        $imagesModel = new ImagesModel();
        $data['images'] = $imagesModel->getForGoods($data['id'], $type);
        return $data;
    }
    
    public function getListForImages($type)
    {
        $select = "
            select 
                g.id,
                CONCAT(m.name,' ',g.name) as name
            from
                goods_{$type} g
            inner join 
                manufacturers m
                on m.id = g.manufacturer
            ";
        return $this->sql->createCommand($select)->queryAll();
    }
    
    public function getListNoImages($type)
    {

        $select = "
            select 
                g.id,
                CONCAT(m.name,' ',g.name) as name
            from
                goods_{$type} g
            inner join 
                manufacturers m
                
                on m.id = g.manufacturer
            where g.id NOT IN (SELECT goods FROM images_{$type} where disabled = 0)
                and g.id not in (select image from temp_images)
            order by name asc
            ";
        return $this->sql->createCommand($select)->queryAll();
    }


    
    public function getListForSitemap($type)
    {
        $select = "
            select 
                CONCAT(m.link,'/',g.link) as link        
            from
                goods_{$type} g
            inner join 
                manufacturers m
                on m.id = g.manufacturer
            ";
        return $this->sql->createCommand($select)->queryAll();
    }
    
    public function getCount($search = null)
    {
        return $this->sql->createCommand("select 
                count(g.id)
            from goods_1 g 
            inner join manufacturers m on g.manufacturer = m.id 
            ".(($search) ? 'where concat(m.name, " ", g.name) like \'%'.$search.'%\'' : ''))->queryScalar();
    }

    public function getForAdmin($page = 1, $search = null)
    {
        $limit = (($page-1)*50).",50";
        $query = "
            select 
                g.id,
                m.name as brand,
                g.name
            from goods_1 g 
            inner join manufacturers m on g.manufacturer = m.id 
            ".(($search) ? 'where concat(m.name, " ", g.name) like \'%'.$search.'%\'' : '')."
            limit ".$limit;
        return $this->sql->createCommand($query)->queryAll();
    }
    
    public function getForEdit($id)
    {
        $qGoods = "select * from goods_1 where id = :id";
        $qBrand = "select * from manufacturers where id = :id";
        $qImages = "select i.id, i.link, i.priority, i.source, i.resized, i.file, f.filesize, f.updated, i.disabled, f.mime_type, f.ext, f.source from images_1 i inner join files f on f.id = i.file where goods = :goods";
        $qSynonims = "select * from goods_1_synonims where goods = :goods";
        $data = array();
        $data['goods'] = $this->sql->createCommand($qGoods)->queryRow(true, array('id'=>$id));
        if (!$data['goods'])
            return false;
        $data['synonims'] = $this->sql->createCommand($qSynonims)->queryAll(true, array("goods"=>$id));
        $data['brand'] = $this->sql->createCommand($qBrand)->queryRow(true, array("id"=>$data['goods']['manufacturer']));
        $data['images']= $this->sql->createCommand($qImages)->queryAll(true, array("goods"=>$id));
        $data['reviews'] = array();
        
        return $data;
    }
}
