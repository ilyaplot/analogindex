<div class="view_read-bl">
    <form action="#" method="post">
        <div class="view_read-head">
            <div class="clr">
                <div class="view_read-avatar">
                    <img src="/assets/img/photo/avatar_view3.png" height="40" width="40">
                </div>
                <div class="view_read-h2">
                    <div class="view_r-name"><?php echo Yii::app()->user->getState("name")?></div>
                    <div class="view_r-setRating">
                        <div>Оцените товар:</div>
                        <ul class="rating2">
                            <li><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#">5</a></li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="view_read-text">
            <textarea name="view_text" class="textarea-st3"></textarea>
        </div>
        <div class="view_read-bottom clr">
            <div class="view_read-bottom-left flLeft">
                <div class="view_r_b-replytext">
                    Вы отвечаете на комментарий:<br>
                    «Открыл, взял в руки и понял - ОНО. Честно говоря  с...»
                </div>
                <div class="view_r_b-linkOff"><a href="#" class="link-st3">Отменить</a></div>
            </div>
            <div class="flRight"><input type="submit" class="btn_submit2" value="Отправить" name="submit_readView"></div>
        </div>
        <input type="hidden" name="GoodItemSetRating" class="GoodItemSetRating" value="">
    </form>
</div>