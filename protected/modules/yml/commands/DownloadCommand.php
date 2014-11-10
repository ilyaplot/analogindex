<?php
class DownloadCommand extends CConsoleCommand
{
    public function beforeAction($action, $params)
    {
        Yii::import("application.modules.yml.models.*");
        return parent::beforeAction($action, $params);
    }

    public function actionIndex()
    {
        $sources = YmlSources::model()->findAll();
        foreach ($sources as &$source) {
            $listUrl = $source->url;
            $listFile = "/inktomia/db/analogindex/yml/catalogs/{$source->id}.yml";
            echo "Downloading {$listUrl}...".PHP_EOL;
            $fp = fopen ($listFile, 'w');
            $ch = curl_init($listUrl);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
            curl_setopt($ch, CURLOPT_FILE, $fp); 
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch); 


            if (curl_errno($ch))
            {
                echo "Ошибка: ".  curl_error($ch).PHP_EOL;
                $source->status = 0;
                $source->status_message = "Не удалось скачать файл.";
                $source->save();
                continue;
            }

            curl_close($ch);
            fclose($fp);
            $source->status_message = "Файл успешно загружен.";
            $source->status = 1;
            $source->save();
        }
    }
    
    /**
     * Загружает список каталогов из topadvert
     */
    public function actionList()
    {
        $listUrl = "http://service.topadvert.ru/yml_list?feed_id=11111&access_key=c52cba5e47f4955a978cacf2887bc73a";
        $listFile = "/inktomia/db/analogindex/yml/lists/1.yml";
        
        $fp = fopen ($listFile, 'w');
        $ch = curl_init($listUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_FILE, $fp); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch); 
        
        
        if (curl_errno($ch))
        {
            echo "Ошибка: ".  curl_error($ch).PHP_EOL;
            exit();
        }
        
        curl_close($ch);
        fclose($fp);
    }
    
    
}