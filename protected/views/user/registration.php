<div class="login-form">
    <center><h1><?php echo Yii::t("models", "Регистрация")?></h1></center>
    <?php echo CHtml::beginForm(); ?>

    <?php echo CHtml::errorSummary($model, Yii::t("models", "<p>Вход не выполнен:</p>")); ?>

    <div class="row">
        <?php echo CHtml::activeLabel($model,'password2', array("autocomlete"=>"off", "placeholder"=>"Имя")); ?><br />
        <?php echo CHtml::activeTextField($model,'password2'); ?>
    </div>
    
    <div class="row">
        <?php echo CHtml::activeLabel($model,'email'); ?><br />
        <?php echo CHtml::activeEmailField($model,'email', array("autocomlete"=>"off", "placeholder"=>"your@email.com")); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabel($model,'password', array("autocomlete"=>"off", "placeholder"=>"Пароль")); ?><br />
        <?php echo CHtml::activePasswordField($model,'password'); ?>
    </div>
    
    <div class="row">
        <?php echo CHtml::activeLabel($model,'password2', array("autocomlete"=>"off", "placeholder"=>"Пароль еще раз")); ?><br />
        <?php echo CHtml::activePasswordField($model,'password2'); ?>
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton(Yii::t("models", "Войти")); ?>
    </div>
    <div class="footer">
        <?php echo Yii::t("models", "Впервые у нас?")?> 
        <a href="<?php echo Yii::app()->createUrl('user/registration', array("language"=>Language::getCurrentZone()))?>"><?php echo Yii::t("models", "Зарегистироваться")?></a>
    </div>
    <?php echo CHtml::endForm(); ?>
</div><!-- form -->