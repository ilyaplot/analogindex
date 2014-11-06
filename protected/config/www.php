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
    'components' => array(
        'user' => array(
            'loginUrl' => array('user/login'),
            'class' => 'WebUser',
            'allowAutoLogin' => true,
        ),
        'db' => require dirname(__FILE__) . '/mysql.php',
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
            'class' => 'CMemCache',
            'useMemcached' => false,
            'serializer' => false,
            'servers' => array(
                array('host' => 'localhost', 'port' => 11211, 'weight' => 60),
            )
        ),
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
        )
    ),
);
