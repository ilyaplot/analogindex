<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>Администрирование</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/assets/admin/js/jquery.js"></script>
    <script async="true" src="/assets/admin/js/bootstrap.min.js"></script>
    <!-- Le styles -->
    <link href="/assets/admin/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
      
      .ui-autocomplete-loading {
        background: white url("/css/images/ui-anim_basic_16x16.gif") right center no-repeat;
      }
    </style>
    <link href="/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="./js/html5shiv.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
            <a class="brand" href="<?php echo Yii::app()->createUrl("admin/index")?>">Администрирование</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
                <li><a href="<?php echo Yii::app()->createUrl("admin/goods")?>">Товары</a></li>
                <li><a href="<?php echo Yii::app()->createUrl("admin/brands")?>">Производители</a></li>
                <li><a href="<?php echo Yii::app()->createUrl("admin/processes")?>">Процессы</a></li>
                <li><a href="<?php echo Yii::app()->createUrl("admin/users")?>">Пользователи</a></li>
                <li><a href="<?php echo Yii::app()->createUrl("admin/logout")?>">Выйти (<?php echo Yii::app()->user->name?>)</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
        <?php echo $content ?>
    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>
