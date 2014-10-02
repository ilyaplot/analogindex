<?php
class Storage extends CComponent
{
    public $path;
    public $section;
    public $folderSize = 10000;
    
    public function init()
    {
        if (!is_dir($this->path))
        {
            throw new CException("Переменная $this->path не содержит правильный путь к папке хранения файлов.");
        }
    }
    
    /**
     * Записывает файл
     * @param type $id
     * @param type $content
     * @param type $section
     * @return boolean
     */
    public function putFile($id, $content)
    {
        $filename = $this->getFilename($id);
        try 
        {
            return file_put_contents($filename, $content);
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Удаляет файл
     * @param type $id
     * @param type $section
     */
    public function deleteFile($id)
    {
        return @unlink($this->getFilename($id));
    }


    /**
     * Отдает содержимое файла
     * @param type $id
     * @param type $section
     * @return boolean
     */
    public function getFile($id)
    {
        try 
        {
            return @file_get_contents($this->getFilename($id));
        } catch (Exception $ex) {
            return false;
        }
    }
    
    /**
     * Возвращает полный путь к файлу
     * @param type $id
     * @param type $section
     * @return type
     */
    public function getFilename($id)
    {
        return $this->getPath($id).md5($id).".file";
    }
    
    /**
     * Возвращает директорию по id
     * @param type $id
     * @param type $section
     * @return string
     */
    public function getPath($id)
    {
        $path = $this->path.DIRECTORY_SEPARATOR.$this->section.DIRECTORY_SEPARATOR.ceil($id / $this->folderSize).DIRECTORY_SEPARATOR;
        if (!file_exists($path))
        {
            mkdir($path, 0777, true);
        }
        return $path;
    }
}