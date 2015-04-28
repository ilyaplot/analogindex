<?php
ini_set("memory_limit", "512M");
//ini_set("session.cookie_domain", ".analogindex.com");
$yii = '../framework/yii.php';
$config = dirname(__FILE__) . '/../protected/config/www.php';

// удалить следующую строку в режиме production
defined('YII_DEBUG') or define('YII_DEBUG', false);

date_default_timezone_set('Europe/Moscow');


require_once($yii);
Yii::createWebApplication($config)->run();
