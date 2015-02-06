<?php

return array(
    'language' => 'ru',
    'preload' => array('log'),
    'sourceLanguage' => 'ru',
    'import' => array(
        'application.components.*',
        'application.components.formatters.*',
        'application.models.*',
        'application.models.new.*',
        'application.parsers.*',
        'application.helpers.*',
    ),
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'modules' => array('yml'),
    'components' => array(
        'user' => array(
            'loginUrl' => array('user/login'),
            'class' => 'WebUser',
            'allowAutoLogin' => true,
        ),
        'db' => require dirname(__FILE__) . '/mysql.php',
        'teta'=>  require dirname(__FILE__).'/teta.php',
        'search' => array(
            'class' => 'SphinxSearch',
            'server' => '127.0.0.1',
            'port' => 9312,
            'maxQueryTime' => 3000,
            'enableProfiling' => 0,
            'enableResultTrace' => 0,
            'fieldWeights' => array(
                'name' => 10000,
            ),
        ),
        'GoogleApis' => array(
            'class' => 'ext.GoogleApis.GoogleApis',
            'developerKey' => 'AIzaSyBMrwCo6ilsyiAzJjhtXccjqke6eU-Pd3Q',
        ),
        'format' => array(
            'class' => 'application.components.Formatter',
        ),
        'urlManager' => require dirname(__FILE__) . '/url.php',
        'authManager' => array(
            'class' => 'PhpAuthManager',
            'defaultRoles' => array('guest'),
        ),
        'cache' => array(
            'class' => 'CFileCache',
            'cachePath' => '/inktomia/db/analogindex/cache/',
        ),
        /**
        'cache' => [
            'class'=>'CMemCache',
            'serializer'=>false,
            'servers'=>array(
                array('host'=>'127.0.0.1', 'port'=>11211, 'weight'=>60),
            ),
        ],
         * 
         */
        'storage' => array(
            'path' => '/inktomia/db/analogindex',
            'section' => 'newfiles',
            'class' => 'Storage',
        ),
        //'session' => array (
        //    'class'=>'CHttpSession',
        //    'savePath' => '/inktomia/db/analogindex/sessions',
        //    'autoStart'=>true,
        //    'timeout'=>1440,
        //),
        'request' => array(
        //'enableCsrfValidation'=>true,
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array( // -- CWebLogRoute ---------------------------
                    'class'=>'CWebLogRoute',
                    'levels'=>'error, warning, trace, profile, info',
                    'enabled'=>false,
                ),
                array( // -- CProfileLogRoute -----------------------
                    'class'=>'CProfileLogRoute',
                    'levels'=>'profile',
                    'enabled'=>false ,
                ),
            ),
        ),
    ),
);
