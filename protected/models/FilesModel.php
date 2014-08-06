<?php
class FilesModel extends Model
{
    public $sql;
    
    public $filespath = "/inktomia/db/analogindex/files/";
    public $tmppath = "/inktomia/db/analogindex/tmp/";
    
    public function __construct() {
        $this->sql = Yii::app()->db;
    }
    
    /**
     * Удаляет файлы, которые не были привязаны
     */
    public function removeDublicates()
    {
        $select = "select * from files f where id not in (select file from images_1) and id not in (select logo from manufacturers)";
        $remove = "delete from files where id = :id";
        $files = $this->sql->createCommand($select)->queryAll();
        foreach ($files as $file)
        {
            $dir = $this->getDirById($file['id']);
            if (file_exists($dir.md5($file['id'])))
                unlink($dir.md5($file['id']));
            $this->sql->createCommand($remove)->execute(array('id'=>$file['id']));
        }
    }
   
    public function getFileById($id)
    {
        $file = $this->getDirById($id)."/".md5($id);
        return $file;
    }
    
    public function send($id, $filename='', $size = null)
    {
        $query = "select * from files where id = :id";
        
        $file = $this->sql->createCommand($query)->queryRow(true,array('id'=>$id));
        if (!$file)
        {
            throw new CHttpException(404, 'Файл не найден');
            exit();
        }
        
        $src = "/files/{$this->getDirNum($id)}/".md5($id);
        if ($size && file_exists($this->getDirById($id).md5($id)."_{$size}"))
        {
            $src = $src."_".intval($size);
        }
        
        header("Content-Type: {$file['mime_type']}");
        header("Content-Length: {$file['filesize']}");
        header("Content-Disposition: attachment; filename=\"{$filename}\""); 
        header('Content-Transfer-Encoding: binary');
        header("X-Accel-Redirect: {$src}");
        exit();
    }

    public function add($filename, $ext = null, $source=null)
    {
        if (!$source)
            $source = md5(time ().microtime ());
        if (!file_exists($filename))
            return false;
        $mime = mime_content_type($filename);
        $size = filesize($filename);
        if (!$ext)
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $insert = "insert ignore into files (filesize, mime_type, ext, source) values (:size, :mime, :ext, :source)";
    
        try
        {
            if ($this->sql->createCommand($insert)->execute(array(
                'size'=>$size,
                'mime'=>$mime,
                'ext'=>$ext,
                'source'=>md5($source),
                )))
            {
                $id = $this->sql->lastInsertID;
                $this->_newDir($id);
                rename($filename, $this->getDirById($id).md5($id));
            } else {
                $id = $this->sql->createCommand("select id from files where source = :source")
                    ->queryScalar(array('source'=>$source));
                unlink($filename);
            }
            return $id;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _newDir($id)
    {
        $dirname = $this->getDirById($id);
        try 
        {
            if (!file_exists($dirname) || !is_dir($dirname))
            {
                mkdir($dirname, 0777);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getDirById($id)
    {
        return $this->filespath.$this->getDirNum($id)."/";
    }
    
    public function getDirNum($id)
    {
        return ceil($id/ 10000);
    }
}