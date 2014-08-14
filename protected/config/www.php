<?php
return array(
    'language'=>'ru',
    'preload'=>array('log'),
    'sourceLanguage'=>'ru',
    'import'=>array(
        'application.components.*',
        'application.models.*',
        'application.models.new.*',
        // Parsers only for console!
        'application.parsers.*',
        'application.helpers.*',
        
     ),
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'components'=>array(
        'user'=>array(
            'loginUrl'=>array('admin/login'),
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
            'class' => 'CDBConnection',
        ),
        'search' => array(
                'class' => 'application.components.DGSphinxSearch.DGSphinxSearch',
                'server' => '127.0.0.1',
                'port' => 9312,
                'maxQueryTime' => 3000,
                'enableProfiling'=>0,
                'enableResultTrace'=>0,
                'fieldWeights' => array(
                    'fullname' => 10000,
                ),
        ),
        'GoogleApis' => array(
            'class' => 'ext.GoogleApis.GoogleApis',
            'developerKey' => 'AIzaSyBMrwCo6ilsyiAzJjhtXccjqke6eU-Pd3Q',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class' => 'CWebLogRoute',
                    'categories' => 'application',
                    'levels'=>'error, warning, trace, profile, info',
                ),
            ),
        ),
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'urlSuffix'=>'.html',
            'rules'=>array(
                'http://search.analogindex.<language:\w+>'=>array('site/search','urlSuffix'=>''),
                'http://search.analogindex.<language:\w+>/lang'=>'site/language',
                
                
                'http://analogindex.<language:\w+>/lang'=>'site/language',
                
                'http://analogindex.<language:\w+>/_image/id<id:\d+>/<name:.*>'=>
                    array('files/image', 'urlSuffix'=>''),
                
                'http://analogindex.<language:\w+>/<link:[\d\w\-_]*>/img/id<id:\d+>/<filename:.*>/<size:\d+>'=>
                    array('site/download', 'urlSuffix'=>''),
                'http://analogindex.<language:\w+>/<link:[\d\w\-_]*>/img/id<id:\d+>/<filename:.*>'=>
                    array('site/download', 'urlSuffix'=>''),
                'http://analogindex.<language:\w+>/<type:[\w\d\-_]+>/<brand:[\d\w\-_]*>/<link:[\d\w\-_]*>'=>
                    array('site/goods', 'urlSuffix'=>'.html'),
                'http://analogindex.<language:\w+>/'=>array('site/index', 'urlSuffix'=>''),
                

                // Дефолтные правила. 
                'http://analogindex.<language:\w+>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                
                'http://www.analogindex.<language:\w+>/_image/id<id:\d+>/<name:.*>'=>
                    array('files/image', 'urlSuffix'=>''),
                'http://www.analogindex.<language:\w+>/lang'=>'site/language',
                'http://www.analogindex.<language:\w+>/<link:[\d\w\-_]*>/img/id<id:\d+>/<filename:.*>/<size:\d+>'=>
                    array('site/download', 'urlSuffix'=>''),
                'http://www.analogindex.<language:\w+>/<link:[\d\w\-_]*>/img/id<id:\d+>/<filename:.*>'=>
                    array('site/download', 'urlSuffix'=>''),
                'http://www.analogindex.<language:\w+>/<type:[\w\d\-_]+>/<brand:[\d\w\-_]*>/<link:[\d\w\-_]*>'=>
                    array('site/goods', 'urlSuffix'=>'.html'),
                'http://www.analogindex.<language:\w+>/'=>array('site/index', 'urlSuffix'=>''),
                // Дефолтные правила. 
                'http://www.analogindex.<language:\w+>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',

            ),
        ),
        'cache'=>array(
            'class'=>'CMemCache',
            'useMemcached'=>false,
            'serializer' => false,
            'servers' => array(
                array('host' => 'localhost', 'port' => 11211, 'weight'=>60),
            )
        ),
        'storage'=>array(
            'path'=>'/inktomia/db/analogindex',
            'section'=>'newfiles',
            'class'=>'Storage',
        ),
        
    ),
);