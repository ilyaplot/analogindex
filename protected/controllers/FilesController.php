<?php
class FilesController extends Controller
{
    
    public function actionImage($id, $name)
    {
        $id = intval(isset($_GET['id'])? $_GET['id'] : 0 );
        $image = Files::model()->findByPk($id);
        if (!$image || !$image->fileExists())
        {
            throw new CHttpException(404, Yii::t("errors", "Файл не найден"));
            exit();
        }
        $src = "/storage/".$image->getSubdirectory()."/".md5($image->getPrimaryKey()).".file";
        header("Content-Type: ".$image->mime_type);
        header("Content-Length: ".$image->filesize);
        header("Content-Disposition: inline; filename=\"{$image->name}\""); 
        header('Content-Transfer-Encoding: binary');
        header("X-Accel-Redirect: {$src}");
        exit();
    }
}

