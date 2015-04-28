<?php
include_once(Yii::getPathOfAlias('ext') . '/cackle_comments.php');
$channel = Yii::app()->request->requestUri; 
$a = new Cackle(true,$channel);
