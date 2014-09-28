<div class="login-form">
    <center><h1><?php echo Yii::t("models", "Регистрация")?></h1></center>
    <?php echo CHtml::beginForm(); ?>

    <?php echo CHtml::errorSummary($model, Yii::t("models", "<p>Невозможно завершить регистрацию:</p>")); ?>

    <div class="row">
        <?php echo CHtml::activeLabel($model,'email'); ?><br />
        <?php echo CHtml::activeEmailField($model,'email', array("autocomlete"=>"off", "placeholder"=>"your@email.com")); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabel($model,'password'); ?><br />
        <?php echo CHtml::activePasswordField($model,'password', array("autocomlete"=>"off", "placeholder"=>"Пароль")); ?>
    </div>
    
    <div class="row">
        <?php echo CHtml::activeLabel($model,'password2'); ?><br />
        <?php echo CHtml::activePasswordField($model,'password2', array("autocomlete"=>"off", "placeholder"=>"Пароль еще раз")); ?>
    </div>
    
    <div class="row">
        <?php echo CHtml::activeLabel($model,'name'); ?><br />
        <?php echo CHtml::activeTextField($model,'name', array("autocomlete"=>"off", "placeholder"=>"Иван Иванов")); ?>
    </div>
    
    
    <div class="row">
        <?php if(CCaptcha::checkRequirements()):?>
            <?php echo CHtml::activeLabelEx($model, 'verifyCode') ?>
            <?php $this->widget('CCaptcha'); ?>
            <?php echo CHtml::activeTextField($model, 'verifyCode') ?>
        <?php endif; ?>
    </div>
    
    <div class="row submit">
        <?php echo CHtml::submitButton(Yii::t("models", "Регистрация")); ?>
    </div>
    <div class="footer">
        <?php echo Yii::t("models", "Вы уже зарегистрированы?")?> 
        <a href="<?php echo Yii::app()->createUrl('user/login', array("language"=>Language::getCurrentZone()))?>"><?php echo Yii::t("models", "Войти")?></a>
    </div>
    <?php echo CHtml::endForm(); ?>
</div><!-- form -->