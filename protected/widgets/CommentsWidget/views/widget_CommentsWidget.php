<div class="view_read-bl">
    <?php echo CHtml::beginForm(Yii::app()->request->getUrl()."#write_comment"); ?>
        <a name="write_comment"></a>
        <div class="view_read-head">
            <div class="clr">
                <div class="view_read-avatar">
                    <img src="/assets/img/photo/avatar_view3.png" height="40" width="40">
                </div>
                <div class="view_read-h2">
                    <div class="view_r-name"><?php echo Yii::app()->user->getState("name")?></div>
                    <!--<div class="view_r-setRating">
                        <div>Оцените товар:</div>
                        <ul class="rating2">
                            <li><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#">5</a></li>
                        </ul>
                        <div class="clear"></div>
                    </div>-->
                </div>
            </div>
            <?php echo CHtml::errorSummary($model); ?>
        </div>
        <div class="view_read-text">
            <?php echo CHtml::activeTextArea($model, "text", array("class"=>"textarea-st3"))?>
        </div>
        <div class="view_read-bottom clr">
            <div class="view_read-bottom-left flLeft">
                <!--<div class="view_r_b-replytext">
                    Вы отвечаете на комментарий:<br>
                    «Открыл, взял в руки и понял - ОНО. Честно говоря  с...»
                </div>
                <div class="view_r_b-linkOff"><a href="#" class="link-st3">Отменить</a></div>-->
            </div>
            <div class="flRight">
                <?php echo CHtml::submitButton('Отправить', array('class'=>'btn_submit2')) ?>
            </div>
        </div>
    <?php echo CHtml::hiddenField("{$className}[".$className::$subject."]", $this->id)?>
    <?php echo CHtml::endForm(); ?>
</div>