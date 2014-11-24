<?php
return array(
    'language'=>'ru',
    'sourceLanguage'=>'ru',
    'import'=>array(
        'application.components.*',
        'application.components.parsers.*',
        'application.extensions.*',
        'application.models.*',
        'application.models.sources.*',
        // Parsers only for console!
        'application.parsers.*',
        'application.helpers.*'
     ),
    //'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR ,
    'modules' => array('yml'),
    'commandMap' => array(
        'yml_download'=>array(
            'class'=>'application.modules.yml.commands.DownloadCommand',
        ),
        'yml_parse'=>array(
            'class'=>'application.modules.yml.commands.ParseCommand',
        ),
    ),
    'preload'=>array('log'),
    'components'=>array(
        'format'=>array(
            'class' => 'application.components.Formatter',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CProfileLogRoute',
                    'report'=>'summary',
                    // Показывает время выполнения каждого отмеченного блока кода.
                    // Значение "report" также можно указать как "callstack".
                ),
            ),
        ),
        'db'=>  require dirname(__FILE__).'/mysql.php',
        'teta'=>  require dirname(__FILE__).'/teta.php',
        'reviews'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=reviews',
            'emulatePrepare' => true,
            'username' => 'reviews',
            'password' => 'reviews',
            'charset' => 'utf8',
            'class' => 'CDbConnection',
        ),
        'Smtpmail'=>array(
            'class'=>'application.extensions.PHPMailer',
            'Host'=>'smtp.yandex.ru',
            'Username'=>'admin@ilyaplot.ru',
            'Password'=>'3qeruj',
            'From'=>'admin@ilyaplot.ru',
            'FromName'=>'AnalogIndex',
            'SMTPSecure' => 'ssl',
            'Mailer'=>'smtp',
            'Port'=>465,
            'CharSet'=>"utf-8",
            'SMTPAuth'=>true, 
        ),
        'urlManager' => require dirname(__FILE__) . '/url.php',
    ),
);