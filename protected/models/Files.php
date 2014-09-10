<?php
/**
 * Файлы
 */
class Files extends CActiveRecord
{
    /**
     * Папка для хранения файлов
     * @var string
     */
    public $path = "/inktomia/db/analogindex/storage";
    
    public static function model($className = __CLASS__) 
    {
        return parent::model($className);
    }

    public function tableName() 
    {
        return "{{files}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "name"=>Yii::t("model", "Имя файла"),
            "size"=>Yii::t("model", "Размер"),
            "mime_type"=>Yii::t("model", "Тип"),
            "updated"=>mYii::t("model", "Последнее изменение"),
        );
    }
    
    
    public function afterDelete() 
    {
        $this->deleteFile();
        return parent::afterDelete();
    }
    
    /**
     * Записывает файл
     * @param type $content
     * @param type $section
     * @return boolean
     */
    public function putFile($content)
    {
        if (!$this->getPrimaryKey())
            return false;
        $filename = $this->getFilename($this->getPrimaryKey());
        try 
        {
            return file_put_contents($filename, $content);
        } catch (Exception $ex) {
            return false;
        }
    }
    
    /**
     * Перемещает файл в хранилище
     * @param string $filename
     * @return boolean
     */
    public function setFile($filename)
    {
        $to = $this->getFilename();
        if (!$to)
            return false;
        return copy($filename, $to);
        //return rename($filename, $to);
    }
    
    /**
     * Удаляет файл
     */
    public function deleteFile()
    {
        return @unlink($this->getFilename($this->getPrimaryKey()));
    }
    
    /**
     * Проверяет наличие файла
     * @return boolean
     */
    public function fileExists()
    {
        $filename = $this->getFilename();
        return file_exists($filename) && is_file($filename);
    }
    
    /**
     * Отдает содержимое файла
     * @return mixed
     */
    public function getContent()
    {
        try 
        {
            return @file_get_contents($this->getFilename());
        } catch (Exception $ex) {
            return false;
        }
    }
    
    /**
     * Возвращает полный путь к файлу
     * @return string
     */
    public function getFilename()
    {
        if (!$this->getPrimaryKey())
            return false;
        return $this->getPath($this->getPrimaryKey()).md5($this->getPrimaryKey()).".file";
    }
    
    /**
     * Возвращает директорию по id
     * @return string
     */
    public function getPath()
    {
        if (!$this->getPrimaryKey())
            return false;
        $path = $this->path.DIRECTORY_SEPARATOR.$this->getSubdirectory().DIRECTORY_SEPARATOR;
        if (!file_exists($path))
        {
            mkdir($path, 0777, true);
        }
        return $path;
    }
    
    public function getSubdirectory()
    {
        return ceil($this->getPrimaryKey() / 10000);
    }
    
    /**
     * Возвращает размер файла
     * @return boolean
     */
    public function getFilesize()
    {
        if (!$this->getPrimaryKey())
            return false;
        if ($this->fileExists())
            return filesize($this->getFilename());
        else
            return false;
    }
    
    /**
     * Возвращает mime type файла
     * @param string $filename
     * @return string
     */
    public function getMimeType($filename = null)
    {
        if ($filename == null)
            $filename = $this->getFilename();
        return mime_content_type($filename);
    }
}