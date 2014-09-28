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
                'maxLength'=>6,
                'offset'=>1,
            ),
        );
    }
    
    public function actionLogin()
    {
        $model=new Users("login");
        if($attributes = Yii::app()->request->getPost("Users"))
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
        
        if($attributes = Yii::app()->request->getPost("Users"))
        {
            $model->attributes=$attributes;
            $model->role = Users::ROLE_USER;
            
            if($model->validate() && $model->save())
            {
                $notification = new Notifications();
                $notification->email  = $model->email;
                $notification->subject = 'Email confirmation';
                $notification->message = "Code: {$model->confirmCode} <a href=".Yii::app()->createAbsoluteUrl("user/confirm", array("language"=>Language::getCurrentZone(), "code"=>$model->confirmCode, "email"=>$model->email)).">Confirm EMAIL</a>";
                $notification->save();
                $this->render("confirm");
                exit();
            }
        }
        
        $this->render('registration', array('model'=>$model));
    }
    
    public function actionProfile()
    {
        
    }
        
    public function actionConfirm($email)
    {
        $user = Users::model()->findByAttributes(array("email"=>$email, "confirmCode"=>Yii::app()->request->getParam("code")));
        if (!$user)
            echo "Не удалось подтвердить email.";
        else
        {
            if ($user->confirmed)
            {
                echo "Ваш ящик уже был подтвержден.";
            } else {
                $user->confirmed = 1;
                $user->save();
                echo "Ящик подтвержден";
            }
        }
    }
}