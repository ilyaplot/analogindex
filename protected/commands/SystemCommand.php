<?php
class SystemCommand extends ConsoleCommand
{
    public function actionSendEmails()
    {
        $to = "ilyaplot@gmail.com";
        $subject = "Подтверждение регистрации";
        $message = "Данунах";
        
        $mailer = Yii::app()->Smtpmail;
        $mailer->IsSMTP();
        $mailer->IsHTML(true);
        $mailer->Subject = $subject;
        $mailer->AddAddress($to);
        $mailer->Body = $message;
        
        if (!$mailer->Send())
        {
            echo $mailer->ErrorInfo.PHP_EOL;
        } else {
            Echo 'EMail OK'.PHP_EOL;
        }
    }
}