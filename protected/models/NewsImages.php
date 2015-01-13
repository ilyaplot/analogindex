<?php
class NewsImages extends CActiveRecord
{
    /**
     * Папка для хранения файлов
     * @var string
     */
    public $path = "/inktomia/db/analogindex/news_images";
    public $previews_prefix = "/preview";
    protected $preview_size = [130,130];
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{news_images}}";
    }

    public function attributeLabels()
    {
        return array(
            "news" => Yii::t("model", "Новость"),
            "size" => Yii::t("model", "Размер"),
            "width" => mYii::t("model", "Ширина"),
            "height" => mYii::t("model", "Высота"),
            "mime_type" => Yii::t("model", "Тип"),
            "name" => Yii::t("model", "Имя файла"),
            "alt_replaced" => Yii::t("model", "Заменен alt"),
            "alt" => Yii::t("model", "Альтернатива"),
            "source_url" => Yii::t("model", "Url источник"),
        );
    }
    
    public function afterDelete()
    {
        $this->deleteFile();
        return parent::afterDelete();
    }
    
    /**
     * Записывает файл
     * @param string $content
     * @return boolean
     */
    public function putFile($content)
    {
        if (!$this->getPrimaryKey())
            return false;
        $filename = $this->getFilename($this->getPrimaryKey());
        try {
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
        
        if (copy($filename, $to)) {
            if (!$this->isImage()) {
                $this->delete();
                return false;
            }
            $this->size = $this->getFilesize();
            $this->mime_type = $this->getMimeType();
            $size = getimagesize($this->getFilename());
            if ($size[0] < 30 || $size[1] < 30) {
                $this->delete();
                
                return false;
            }
            
            $this->width = $size[0];
            $this->height = $size[1];
            return true;
        }
        
        return false;
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
        try {
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
        return $this->getPath($this->getPrimaryKey()) . md5($this->getPrimaryKey()) . ".file";
    }
    
    /**
     * Возвращает директорию по id
     * @return string
     */
    public function getPath()
    {
        if (!$this->getPrimaryKey())
            return false;
        $path = $this->path . DIRECTORY_SEPARATOR . $this->getSubdirectory() . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
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
    
    public function getExt() {
        return preg_replace("/image\/(.*)/isu", "$1", $this->getMimeType());
    }
    
    public function isImage() {
        echo $this->getMimeType().PHP_EOL;
        return preg_match("/^image\/.*$/isu", $this->getMimeType());
    }
    
    public function getUrl()
    {
        return Yii::app()->createAbsoluteUrl("files/newsimage", [
            'language' => Language::getCurrentZone(),
            'id'=>  $this->id,
            'name'=>  $this->name,
        ]);
    }
    
    public function getPreviewUrl()
    {
        return Yii::app()->createAbsoluteUrl("files/newsimagepreview", [
            'language' => Language::getCurrentZone(),
            'id'=>  $this->id,
            'name'=>  $this->name,
        ]);
    }
    
    public function createPreviews()
    {
        include_once 'WideImage/WideImage.php';
        $query = "select i.id, i.news, i.width, i.height from {{news_images}} i group by i.news having max(i.has_preview) = 0 order by i.id asc";
        $list = $this->getDbConnection()->createCommand($query)->queryAll(true);
        $updateQuery = "update {{news_images}} set has_preview = 1 where id = :id";
        foreach ($list as $image) {
            $path = $this->path . $this->previews_prefix . DIRECTORY_SEPARATOR . ceil($image['id'] / 10000) . DIRECTORY_SEPARATOR;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            try {
                $temp = "/tmp/_analogindex_preview_" . md5(time() . microtime()) . ".jpg";
                if (!@copy($this->path . DIRECTORY_SEPARATOR 
                    . ceil($image['id'] / 10000) . DIRECTORY_SEPARATOR.md5($image['id']) 
                    . ".file", $temp)) {
                    continue;
                }
                        
                WideImage::load($temp)->resize($this->preview_size[0], $this->preview_size[1])
                    ->saveToFile($temp);
                rename($temp, $path.md5($image['id']).".file");
                $this->getDbConnection()->createCommand($updateQuery)->execute([
                    'id'=>$image['id'],
                ]);
                echo "+";
            } catch (Exception $ex) {
                echo $ex->getMessage() . PHP_EOL;
                continue;
            }
            unset ($image);
        }
        echo PHP_EOL;
    }
}
