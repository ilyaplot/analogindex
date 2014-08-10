<?php
class ImagesModel extends Model
{
    public $sql;
    
    const SIZE_SMALL = 1;
    const SIZE_MEDIUM = 2;
    const SIZE_PREVIEW = 3;
    const SIZE_BIG = 4;
    
    public function __construct() {
        $this->sql = Yii::app()->db;
    }

    /**
     * Получает список изображений
     * @param int $id id товара
     * @param int $type Тип товара
     */
    public function getList($id, $type)
    {
        $query = "SELECT * FROM images";
        $list = $this->sql->createCommand($query)->queryAll();
        return $list;
    }
    
    public function getForGoods($id, $type)
    {
        $query = "SELECT i.link, i.file, f.ext, f.filesize, f.mime_type FROM images_{$type} i INNER JOIN files f ON i.file = f.id WHERE i.disabled = 0 and i.goods = :id ORDER BY i.priority asc, i.id asc";
        $result = $this->sql->createCommand($query)->queryAll(true,array('id'=>$id));
        return $result;
    }

    public function getParent($id, $type, $size)
    {
        $query = "SELECT i.link, i.file, f.ext FROM images_{$type} i INNER JOIN files f ON i.file = f.id WHERE i.disabled = 0 and i.goods = :id ORDER BY i.priority LIMIT 1";
        $result = $this->sql->createCommand($query)->queryRow(true,array('id'=>$id));
        return $result;
    }
    
    public function getForResize($type)
    {
        $select = "SELECT i.id, i.link, i.file FROM images_1 i INNER JOIN files f ON i.file = f.id WHERE i.resized = 0";
        return $this->sql->createCommand($select)->queryAll();
        
    }

    public function setResized($type, $id, $fail = 1)
    {
        $update = "update images_{$type} set resized = :fail where id = :id";
        return $this->sql->createCommand($update)->execute(array('id'=>$id, 'fail'=>$fail));
    }

        /**
     * 
     * @param int $type Тип товара
     * @param int $goods ID товара
     * @param int $file ID файла
     * @param string $source Источник
     * @param string $link Название файла
     * @return type
     * @throws Exception
     */
    public function add($type, $goods, $file, $source, $link)
    {
        $query = "insert into images_{$type} (link, goods, file, source)
               values (:link, :goods, :file, :source) on duplicate key update link = :link, goods = :goods, file = :file, source = :source";
        $select = "select id from images_{$type} where source = :source";
        $source = md5($source);
        try
        {
            $this->sql->createCommand($query)->execute(array(
                'link'=>$link,
                'goods'=>$goods,
                'file'=>$file,
                'source'=>$source,
            ));
            $id = $this->sql->lastInsertID;
            if (!$id)
                return $this->sql->createCommand ($select)->queryScalar (array(
                    'source'=>$source,
                ));
            return $id;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
