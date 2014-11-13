<?php

return array(
    'urlFormat' => 'path',
    'showScriptName' => false,
    'urlSuffix' => '.html',
    'class' => 'UrlManager',
    'rules' => array(
        'http://analogindex.<language:\w+>/<url:.*>' => [
            'class' => 'application.components.Redirect',
        ],
        
        
        'yml/manager/index'=>'yml/manager/index',
        'yml/manager/search'=>'yml/manager/search',
        'http://search.analogindex.<language:\w+>' => array('site/search', 'urlSuffix' => ''),
        'http://search.analogindex.<language:\w+>/lang' => 'site/language',
        'http://analogindex.<language:\w+>/lang' => 'site/language',
        'http://analogindex.<language:\w+>/user/login' => 'user/login',
        'http://analogindex.<language:\w+>/user/registration' => 'user/registration',
        'http://analogindex.<language:\w+>/user/confirm' => 'user/confirm',
        'http://analogindex.<language:\w+>/type/<type:[\d\w\-_]*>/<add:.*>' =>
        array(
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
        'http://analogindex.<language:\w+>/brand/<link:[\d\w\-_]*>/page<page:\d+>' =>
        array('site/brand', 'urlSuffix' => '.html'),
        'http://analogindex.<language:\w+>/brand/<link:[\d\w\-_]*>/<type:[\d\w\-_]*>' =>
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
);
