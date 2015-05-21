<nav class="blue lighten-4" role="navigation">
    <div class="nav-wrapper">
        <a id="logo-container" href="/" class="brand-logo">
            AnalogIndex
        </a>

        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <?php foreach ($types as $key => $type): ?>
                <li>
                    <a href="<?php echo Yii::app()->createUrl("site/type", array("type" => $type->link, "language" => Language::getCurrentZone())) ?>"><?php echo $type->name->name ?></a>
                </li>
            <?php endforeach; ?>
            
        </ul>
        <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
    </div>
</nav>