<?php

ini_set('memory_limit', '8096M');
$yii = './framework/yii.php';
require_once($yii);
$config = './protected/config/console.php';

// удалить следующую строку в режиме production
defined('YII_DEBUG') or define('YII_DEBUG', true);
date_default_timezone_set('Europe/Moscow');
Yii::createConsoleApplication($config)->run();
