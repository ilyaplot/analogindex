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
            'class' => 'Formatter',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'urlSuffix' => '.html',
            'class' => 'UrlManager',
            'rules' => array(
                'http://search.analogindex.<language:\w+>' => array('site/search', 'urlSuffix' => ''),
                'http://search.analogindex.<language:\w+>/lang' => 'site/language',
                'http://analogindex.<language:\w+>/lang' => 'site/language',
                'http://analogindex.<language:\w+>/user/login' => 'user/login',
                'http://analogindex.<language:\w+>/user/registration' => 'user/registration',
                'http://analogindex.<language:\w+>/user/confirm' => 'user/confirm',
                'http://analogindex.<language:\w+>/type/<type:[\d\w\-_]*>/<add:.*>' =>
                array(
                    //'site/type', 
                    //'urlSuffix'=>'.html',
                    'class' => 'application.components.TypeRule',
                ),
                'http://analogindex.<language:\w+>/type/<type:[\d\w\-_]*>' =>
                array(
                    'urlSuffix' => '.html',
                    'class' => 'application.components.TypeRule',
                ),
                'http://analogindex.<language:\w+>/review/<goods:[\d\w\-_]*>/<link:[\d\w\-_]+>_<id:\d+>' =>
                array('site/review', 'urlSuffix' => '.html'),
                'http://analogindex.<language:\w+>/_image/id<id:\d+>/<name:.*>' =>
                array('files/image', 'urlSuffix' => ''),
                'http://analogindex.<language:\w+>/brand/<link:[\d\w\-_]*>/<type:[\d\w\-_]*>/page<page:\d+>' =>
                array('site/brand', 'urlSuffix' => '.html'),
                'http://analogindex.<language:\w+>/brand/<link:[\d\w\-_]*>/<type:[\d\w\-_]*>' =>
                array('site/brand', 'urlSuffix' => '.html'),
                'http://analogindex.<language:\w+>/brand/<link:[\d\w\-_]*>/page<page:\d+>' =>
                array('site/brand', 'urlSuffix' => '.html'),
                'http://analogindex.<language:\w+>/brand/<link:[\d\w\-_]*>' =>
                array('site/brand', 'urlSuffix' => '.html'),
                'http://analogindex.<language:\w+>/<link:[\d\w\-_]*>/img/id<id:\d+>/<filename:.*>/<size:\d+>' =>
                array('site/download', 'urlSuffix' => ''),
                'http://analogindex.<language:\w+>/<link:[\d\w\-_]*>/img/id<id:\d+>/<filename:.*>' =>
                array('site/download', 'urlSuffix' => ''),
                'http://analogindex.<language:\w+>/<type:[\w\d\-_]+>/<brand:[\d\w\-_\+]*>/<link:[\d\w\-_]*>' =>
                array('site/goods', 'urlSuffix' => '.html'),
                'http://analogindex.<language:\w+>/' => array('site/index', 'urlSuffix' => ''),
                // Дефолтные правила. 
                'http://analogindex.<language:\w+>/<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
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
