<div class="row">		
    <div class="col s12 hide-on-large-only">
        <div class="nav-btn-mob">
            <i class="mdi-device-storage medium"></i>
        </div>
        <div class="nav-menu-mob">
            <ul class="collection">
                <?php foreach ($types as $key => $type): ?>
                    <li class="collection-item"><a href="<?php echo Yii::app()->createUrl("site/type", array("type" => $type->link, "language" => Language::getCurrentZone())) ?>"><?php echo $type->name->name ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col s12">
        <div class="right-align topString">
            <?php echo Yii::t('main', '<a href="http://analogindex.com/lang.html">English version</a>') ?>
        </div>
    </div>

    <div class="col s6">
        <a href="http://analogindex.<?= Language::getCurrentZone() ?>/" class="logo-link">
            <img src="/assets/img/logo.png" alt="Analog Index" />
        </a>
    </div>
    <div class="col s12 m12 l6">
        <div class="right-align search-boxer-alex">
            <div class="row">
                <div class="input-field col s12">
                    <form class="col s12" action="http://search.analogindex.<?php echo Language::getCurrentZone() ?>/" method="get">
                        <i class="mdi-action-search prefix"></i>
                        <input id="icon_prefix" autocomplete="off" type="text" name="keyword" class="validate" value="<?php echo htmlentities(isset($_GET['keyword']) ? $_GET['keyword'] : '') ?>">
                        <label for="icon_prefix"></label>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="row hide-on-med-and-down">
    <nav>
        <div class="nav-wrapper col s12 m12">
            <ul id="nav-mobile" class="left">
                <?php foreach ($types as $key => $type): ?>
                    <li><a href="<?php echo Yii::app()->createUrl("site/type", array("type" => $type->link, "language" => Language::getCurrentZone())) ?>"><?php echo $type->name->name ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</div>