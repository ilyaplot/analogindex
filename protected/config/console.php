<?php
return array(
    'language'=>'ru',
    'sourceLanguage'=>'ru',
    'import'=>array(
        'application.components.*',
        'application.models.*',
        'application.models.new.*',
        // Parsers only for console!
        'application.parsers.*',
        'application.helpers.*'
     ),
    //'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR ,
    'preload'=>array('log'),
    'components'=>array(
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
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=analogindex',
            'emulatePrepare' => true,
            'username' => 'analogindex',
            'password' => 'analogindex',
            'charset' => 'utf8',
        ),
        'newdb'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=analogindex',
            'emulatePrepare' => true,
            'username' => 'analogindex',
            'password' => 'analogindex',
            'tablePrefix' => 'ai_',
            'charset' => 'utf8',
            'class' => 'CDbConnection',
        ),
        'reviews'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=reviews',
            'emulatePrepare' => true,
            'username' => 'reviews',
            'password' => 'reviews',
            'charset' => 'utf8',
            'class' => 'CDbConnection',
        ),
        'mob'=>array(
            'path'=>'/inktomia/db/analogindex/mob',
            'class'=>'Storage',
        ),
    ),
);