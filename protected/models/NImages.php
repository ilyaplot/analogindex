<?php
/**
 * @todo Допилить валидацию
 */
class NImages extends CActiveRecord
{
    
    /**
     * Папка для хранения файлов
     * @var string
     */
    public $path = "/inktomia/db/analogindex/images";
    
    /**
     * Картинка для лайтбокса
     */
    const SIZE_PRODUCT_BIG = '1024x1024';
    /**
     * Картинки для контента
     */
    const SIZE_PRODUCT_PREVIEW = '510x510';
    /**
     * Превью списки для галереи
     */
    const SIZE_PRODUCT_LIST = '91x91';
    /**
     * Иконки для виджета
     */
    const SIZE_PRODUCT_WIDGET = '30x37';
    /**
     * Иконки в поиске
     */
    const SIZE_PRODUCT_SEARCH = '91x91';
    /**
     * Превью на страницах брендов
     */
    const SIZE_PRODUCT_BRAND = '131x131';
    
    const DEFAULT_FORMAT = 'png';
    
    /**
     * Ключ массива - width X height
     */
    public static $sizes = [
        '30x37'=>[
            'format'=>'gif',
        ], 
        '91x91'=>[
            'format'=>'png',
            'compression'=>6,
        ],
        '130x130'=>[
            'format'=>'png',
            'compression'=>6,
        ],
        '510x510'=>[
            'format'=>'jpeg',
            'quality'=>80,
        ],
        '1024x1024'=>[
            'format'=>'jpeg',
            'quality'=>90,
        ],
    ];
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @todo Заменить название таблицы после переноса
     * @return string
     */
    public function tableName()
    {
        return "new_images";
    }

    public function beforeSave()
    {
        if ($this->isNewRecord) {
            $this->created = new CDbExpression('now()');
        }
        
        $this->updated = new CDbExpression('now()');
        return parent::beforeSave();
    }


    public function afterDelete()
    {
        return parent::beforeDelete();
    }
    
    public function rules()
    {
        return [
            ['name, extension, type, source_url, mime_type, width, height, body_hash', 'required', 'on'=>'create,update'],
            // on create
        ];
    }

    public function getSizeOptions($size)
    {
        if (!$size) {
            return (object) [
                'link'=>'source',
                'notResize'=>true,
            ];
        }
        
        if (preg_match("/^(?P<width>\d+)x(?P<height>\d+)$/isu", $size, $matches)) {
            $link = "{$matches['width']}x{$matches['height']}";

            if (!isset(self::$sizes[$link])) {
                return false;
            }
            
            
            $options = (object) array_merge([
                'link' => $link, 
                'width'=>$matches['width'], 
                'height'=>$matches['height']
            ], self::$sizes[$link]);
            
            
            
            if ($this->width > $options->width || $this->height > $this->width) {
                
            } else {
                $options->notResize = true;
            }
            
            $options->format = isset($options->format) ? $options->format : self::DEFAULT_FORMAT;
            
            
            $options->path = $this->getStoragePath($link).$this->getPrimaryKey().".".$options->format;
            
            return $options;
        }
        return false;
    }
    
    public function getStoragePath($size = null)
    {
        // Нет записи в бд - нет папки
        if ($this->isNewRecord) {
            return false;
        }
        
        // Нет размера - нет файла
        if ($size != null && !isset(self::$sizes[$size]) && $size != 'source') {
            return false;
        }
        
        $size = !empty($size) ? $size : 'source';
        
        $folder = ceil($this->getPrimaryKey()/10000);
        
        // путь к хранилищу / ссылка на размер / номер папки /
        $path = "{$this->path}/{$size}/{$folder}/";
        
        if (!file_exists("{$this->path}/{$size}/{$folder}/")) {
            mkdir($path, 0777, true);
        }
        
        return "{$this->path}/{$size}/{$folder}/";
    }
    
    /**
     * Возвращает extension для файла
     * @param type $size
     * @return type
     */
    public function getExtension($size = null)
    {

        if (!$this->isNewRecord && $size != null) {
            if ($size != 'source') {
                $options = $this->getSizeOptions($size);
                return $options->format;
            } else {
                return $this->extension;
            }
        } elseif ($size == null) {
            return $this->extension;
        }
        return false;
    }


    public function getFileExtension($filename)
    {
        $mime_type = mime_content_type($filename);
        
        if (preg_match("/^image\/.*$/isu", $mime_type)) {
            return preg_replace("/^image\/(x\-ms\-)?/isu", '', $mime_type);
        }
        
        return false;
    }

    public function getFilename($size = null)
    {

        return $this->getStoragePath($size).$this->getPrimaryKey().".".$this->getExtension($size);
    }
    
    public function getNewFilename($filename)
    {
        return $this->getStoragePath().$this->getPrimaryKey().".".$this->getFileExtension($filename);
    }
    
    public function createSizes()
    {
        include_once 'WideImage/WideImage.php';
        foreach (self::$sizes as $size=>$sizeParams) {
            
            
            // Если вернулись правильные параметры для размера
            $options = $this->getSizeOptions($size);
            if (isset($options->notResize) && $options->notResize) {
                continue;
            }
            
            // PNG с указанием компрессии
            if (!empty($options->compression) && $options->format == 'png') {
                WideImage::load($this->getFilename())->resize($options->width, $options->height)->saveToFile($options->path, $options->compression);
            // Все, кроме png с указанием качества в %
            } else if (!empty($options->quality) && $options->format != 'png') {
                WideImage::load($this->getFilename())->resize($options->width, $options->height)->saveToFile($options->path, $options->quality);
            // Все форматы без указания качества и сжатия
            } else {
                WideImage::load($this->getFilename())->resize($options->width, $options->height)->saveToFile($options->path);
            }
        }
        return true;
    }
    
    public function isImage($filename)
    {
        $mime_type = mime_content_type($filename);
        
        if (preg_match("/^image\/.*$/isu", $mime_type)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Добавляет изображение в базу данных и хранилище
     * @param string $filename 
     * @param string $type
     * @param string $name
     * @param string $source_url
     * @return boolean
     * @throws Exception
     * 
     * @todo Добавить нормальную обработку ошибок
     */
    public function create($filename, $type, $name, $source_url=null)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
       
        try {
            
            if (!file_exists($filename) || !$this->isImage($filename)) {
                $transaction->rollback();
                return false;
            }
            
            $this->setScenario('create');
            
            $this->name = preg_replace("/(\.\w+)?$/isu",'',$name);
            $this->name = Yii::app()->urlManager->translitUrl($this->name);
            $this->type = $type;
            $this->extension = $this->getFileExtension($filename);
            $this->source_url = $source_url;
            $this->mime_type = mime_content_type($filename);
            $this->body_hash = md5_file($filename);
            
            $size = getimagesize($filename);
            $this->width = $size[0];
            $this->height = $size[1];
            
            
            $this->filesize = filesize($filename);
            
            if ($this->validate() && $this->save()) {
                $this->setScenario('update');
                
                if (rename($filename, $this->getNewFilename($filename)) && $this->validate() && $this->save()) {
                    if ($this->createSizes()) {
                        $transaction->commit();
                        return $this->getPrimaryKey();
                    }
                }
            }
            
            $transaction->rollback();
            return false;
        } catch (Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
        
        $transaction->rollback();
        return false;
    }
    
    public static function AccelRedirect($id, $size) 
    {

        $model = self::model()->findByPk($id);
        
        if (!$model)
            return false;

        if (empty(self::$sizes[$size]))
            return false;

        if (preg_match("/(?P<width>\d+)x(?P<height>\d+)/isu", $size ,$matches)) {
            $size = $matches['width']."x".$matches['height'];
            $options = $model->getSizeOptions($size);
            $options->format = "image/".$options->format;
            if ($model->width < $matches['width'] && $model->height < $matches['height']) {
                $size = 'source';
                $options->format = $model->mime_type;
            }
        }
       
        $filename = $model->getFilename($size);
        
        $filesize = ($size == 'source') ? $model->filesize : filesize($filename);
        $filename = str_replace("/inktomia/db/analogindex", '', $filename);
        
        header("Content-Type: {$options->format}");
        header("Content-Length: ".$filesize);
        header("Content-Disposition: inline; filename=\"{$filename}\""); 
        header('Content-Transfer-Encoding: binary');
        header("X-Accel-Redirect: {$filename}");
        return true;
    }
    
}
