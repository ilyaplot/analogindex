<?php
class UserController extends Controller
{
    public function actions(){
        return array(
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xFFFFFF,
                'transparent'=>true,
                'testLimit'=>1,
                'foreColor'=>0x999999,
                'minLength'=>5,
                'maxLength'=>8,
                'offset'=>1,
            ),
        );
    }
    
    public function actionLogin()
    {
        $model=new Users("login");
        if($attributes = Yii::app()->request->getParam("Users"))
        {

            $model->attributes=$attributes;

            if($model->validate())
            {
                Yii::app()->user->login($model->getIdentity(), 3600*24*7);
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
        $model = new Users("registration");
        $this->render('registration', array('model'=>$model));
    }
    
    public function actionProfile()
    {
        
    }
        
}