<?php
return array(
    'language'=>'ru',
    'sourceLanguage'=>'ru',
    'import'=>array(
        'application.components.*',
        'application.models.*',
        // Parsers only for console!
        'application.parsers.*',
        'application.helpers.*'
     ),
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=analogindex',
            'emulatePrepare' => true,
            'username' => 'analogindex',
            'password' => 'analogindex',
            'charset' => 'utf8',
        ),
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'urlSuffix'=>'.html',
            'rules'=>array(
                '<language:\w+>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),
        
    ),
);