<?php
class SystemCommand extends ConsoleCommand
{
    
    public function actionDeleteResize()
    {
        $resized = ImagesResized::model()->findAll();
        foreach ($resized as $image)
        {
            if (!$image->file_data)
            {
                $image->delete();
                continue;
            }
            echo $image->file_data->getFilename().PHP_EOL;
            $image->file_data->delete();
            $image->delete();
        }
    }
    
    public function actionDeleteNewResize()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "size6 > 0";
        $resized = Images::model()->findAll($criteria);
        foreach ($resized as $image)
        {
            if ($image->size6_data)
            {
                echo $image->size6_data->id." of 298520".PHP_EOL;
                echo $image->size6_data->getFilename().PHP_EOL;
                $image->size6_data->delete();
                $image->size6 = 0;
                $image->save();
            } else {
                echo ".";
                $image->size6 = 0;
                $image->save();
            }
            
        }
        echo PHP_EOL;
    }
}