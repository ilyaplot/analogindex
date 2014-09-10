<?php
class UserController extends Controller
{
    public function actionLogin()
    {
        $model=new Users;
        if(isset($_POST['Users']))
        {

            $model->attributes=$_POST['Users'];

            if($model->validate())
            {
                Yii::app()->user->login($model->getIdentity(), ($model->rememberMe) ? (3600*24*7) : null);
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
        
        
        $this->render('login', array(
            "model"=>$model,
        ));
    }
    
    public function actionLogout()
    {
        Yii::app()->user->logout();
        Yii::app()->request->redirect(Yii::app()->request->urlReferrer);
        exit();
    }
    
    public function actionRegistration()
    {
        $model = new Users;
        $this->render('registration', array('model'=>$model));
    }
    
    public function actionProfile()
    {
        
    }
    
    protected function performAjaxValidation($model)
    {
        var_dump($_POST);
        if(isset($_POST['ajax']) && $_POST['ajax']==='test-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }        
}