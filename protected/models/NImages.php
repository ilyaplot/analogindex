<?php

/**
 * @todo Допилить валидацию
 */
class NImages extends CActiveRecord
{

    public $copyExist = false;

    /**
     * Папка для хранения файлов
     * @var string
     */
    public $path = "/inktomia/db/analogindex/images";

    /**
     * Картинки для новостей
     */
    const SIZE_ARTICLE_BIG = '1024x1024';

    /**
     * Превьюшки новостей
     */
    const SIZE_ARTICLE_PREVIEW = '130x130';

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
    const SIZE_PRODUCT_BRAND = '130x130';

    /**
     * Превьюшки в галерее
     */
    const SIZE_PRODUCT_GALLERY = '130x130';
    const DEFAULT_FORMAT = 'png';

    /**
     * Ключ массива - width X height
     */
    public static $sizes = [
        '30x37' => [
            'format' => 'gif',
        ],
        '91x91' => [
            'format' => 'png',
            'compression' => 6,
        ],
        '130x130' => [
            'format' => 'png',
            'compression' => 6,
        ],
        '510x510' => [
            'format' => 'jpeg',
            'quality' => 80,
        ],
        '1024x1024' => [
            'format' => 'jpeg',
            'quality' => 90,
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
            ['name, extension, type, source_url, mime_type, width, height, body_hash', 'required', 'on' => 'create,update'],
                // on create
        ];
    }

    public function relations()
    {
        return [
            'article' => [self::HAS_ONE, 'ArticlesImagesCopy', 'image'],
        ];
    }

    /**
     * Возвращает параметры размера
     * @param type $size
     * @return boolean
     */
    public function getSizeOptions($size)
    {
        if (!$size) {
            return (object) [
                        'link' => 'source',
                        'notResize' => true,
            ];
        }

        if (preg_match("/^(?P<width>\d+)x(?P<height>\d+)$/isu", $size, $matches)) {
            $link = "{$matches['width']}x{$matches['height']}";

            if (!isset(self::$sizes[$link])) {
                return false;
            }


            $options = (object) array_merge([
                        'link' => $link,
                        'width' => $matches['width'],
                        'height' => $matches['height']
                            ], self::$sizes[$link]);



            if ($this->width > $options->width || $this->height > $this->width) {
                
            } else {
                $options->notResize = true;
            }

            $options->format = isset($options->format) ? $options->format : self::DEFAULT_FORMAT;


            $options->path = $this->getStoragePath($link) . $this->getPrimaryKey() . "." . $options->format;

            return $options;
        }
        return false;
    }

    /**
     * Возвращает путь к папке хранилища
     * @param type $size
     * @return boolean
     */
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

        $folder = ceil($this->getPrimaryKey() / 10000);

        // путь к хранилищу / ссылка на размер / номер папки /
        $path = "{$this->path}/{$size}/{$folder}/";

        if (!file_exists("{$this->path}/{$size}/{$folder}/")) {
            @mkdir($path, 0777, true);
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
                return isset($options->format) ? $options->format : $this->extension;
            } else {
                return $this->extension;
            }
        } elseif ($size == null) {
            return $this->extension;
        }
        return false;
    }

    /**
     * Возвращает extension файла
     * @param type $filename
     * @return boolean
     */
    public function getFileExtension($filename)
    {
        $mime_type = mime_content_type($filename);

        if (preg_match("/^image\/.*$/isu", $mime_type)) {
            return preg_replace("/^image\/(x\-ms\-)?/isu", '', $mime_type);
        }

        return false;
    }

    /**
     * Возвращает полный путь к файлу, учитывая размер
     * @param type $size
     * @return type
     */
    public function getFilename($size = null)
    {

        return $this->getStoragePath($size) . $this->getPrimaryKey() . "." . $this->getExtension($size);
    }

    /**
     * аналог getFilename, но для новых файлов
     * @param type $filename
     * @return type
     */
    public function getNewFilename($filename)
    {
        return $this->getStoragePath() . $this->getPrimaryKey() . "." . $this->getFileExtension($filename);
    }

    /**
     * Ресайзер
     * @return boolean
     */
    public function createSizes()
    {
        include_once 'WideImage/WideImage.php';
        foreach (self::$sizes as $size => $sizeParams) {


            // Если вернулись правильные параметры для размера
            $options = $this->getSizeOptions($size);
            if (isset($options->notResize) && $options->notResize) {
                continue;
            }

            $image = WideImage::load($this->getFilename());
            
            if ($image->getWidth() < 10 || $image->getHeight() < 10) {
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

    /**
     * Определяет, является ли файл картинкой
     * @param type $filename
     * @return boolean
     */
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
     * @param string $alt
     * @return boolean
     * @throws Exception
     * 
     * @todo Добавить нормальную обработку ошибок
     */
    public function create($filename, $type, $name, $source_url = null, $alt = '')
    {
        $this->copyExist = false;
        $hash = md5_file($filename);

        if ($model = self::model()->findByAttributes(['body_hash' => $hash, 'type' => $type])) {
            echo "Image extists" . PHP_EOL;
            echo $model->getHtml(self::SIZE_PRODUCT_BIG) . PHP_EOL;
            $this->copyExist = true;
            return $model->id;
        }
        //echo "BEGIN TRANSACTION".PHP_EOL;
        //$transaction = $this->getDbConnection()->beginTransaction();

        try {

            if (!file_exists($filename) || !$this->isImage($filename)) {
                if (!$this->isNewRecord) {
                    $this->delete();
                }
                //$transaction->rollback();
                return false;
            }

            $this->setScenario('create');

            $this->name = preg_replace("/(\.\w+)?$/isu", '', $name);
            $this->name = Yii::app()->urlManager->translitUrl($this->name);
            $this->alt = $alt;
            $this->title = $alt;
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
                        //$transaction->commit();
                        return $this->getPrimaryKey();
                    }
                }
            }
            if (!$this->isNewRecord) {
                $this->delete();
            }
            //$transaction->rollback();
            return false;
        } catch (Exception $ex) {
            //$transaction->rollback();
            if (!$this->isNewRecord) {
                $this->delete();
            }
            throw $ex;
        }

        //$transaction->rollback();
        $this->delete();
        return false;
    }

    /**
     * Возвращает абсолютный url для картинки, учитывая размер
     * @param type $lang
     * @return type
     * 
     * @todo Изменить путь к url
     */
    public function createUrl($size, $lang = null)
    {
        if (empty($lang)) {
            $lang = Yii::app()->language;
        }

        return Yii::app()->createAbsoluteUrl("files/newimage", [
                    'language' => Language::getZoneForLang($lang),
                    'id' => $this->id,
                    'size' => $size,
                    'name' => $this->name . "." . $this->getExtension($size),
        ]);
    }

    /**
     * Отдает файл через x-accel-redirect
     * @param type $id
     * @param type $size
     * @return boolean
     * 
     * @todo добавить проверку filename
     */
    public static function AccelRedirect($id, $size)
    {
        $key = md5($id . "-" . $size);

        if (!$headers = Yii::app()->cache->get($key)) {
            $model = self::model()->cache(60 * 60)->findByPk($id);

            if (!$model)
                return false;

            if (empty(self::$sizes[$size]))
                return false;

            if (preg_match("/(?P<width>\d+)x(?P<height>\d+)/isu", $size, $matches)) {
                $size = $matches['width'] . "x" . $matches['height'];

                $options = $model->getSizeOptions($size);
                $options->format = "image/" . $options->format;

                if ((isset($options->notResize) && $options->notResize == true) || ($model->width <= $matches['width'] && $model->height <= $matches['height'])) {
                    $size = 'source';
                    $options->format = $model->mime_type;
                }
            }

            $filename = $model->getFilename($size);

            $filesize = ($size == 'source') ? $model->filesize : filesize($filename);
            $extension = $model->getExtension($size);
            $filename = str_replace("/inktomia/db/analogindex", '', $filename);


            $headers = [
                "Content-Type: {$options->format}",
                "Content-Length: " . $filesize,
                "Content-Disposition: inline; filename=\"{$model->name}.{$extension}\"",
                "Content-Transfer-Encoding: binary",
                "Cache-control: public",
                "X-Accel-Redirect: {$filename}",
            ];
            Yii::app()->cache->set($key, $headers, 0);
        }


        foreach ($headers as $header) {
            header($header);
        }

        return true;
    }

    /**
     * Возвращает html тэг изображения
     * @param type $size
     * @param type $lang
     * @param type $htmlOptions
     * @return type
     */
    public function getHtml($size, $lang = null, $htmlOptions = [])
    {
        return CHtml::image($this->createUrl($size, $lang), $this->alt, array_merge(['title' => $this->title], $htmlOptions));
    }

}
