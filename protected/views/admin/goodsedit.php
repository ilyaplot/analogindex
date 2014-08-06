<script type="text/javascript">
    $(document).ready(function(){
        $("button.show").click(function(){
            if (!$(this).hasClass("already"))
            {
                $("#"+$(this).attr("data-target")).show();
                $(this).addClass("already");
                $(this).html("<i class='icon-list'></i> Скрыть");
            } else {
                $("#"+$(this).attr("data-target")).hide();
                $(this).html("<i class='icon-list'></i> Показать");
                $(this).removeClass("already");
            }
        });
    });
</script>
<h4>Основное (<?php echo $data['brand']['name']." ".$data['goods']['name']?>) язык <?php echo Yii::app()->language?></h4>
<table class="table table-bordered table-striped">
    <tr>
        <td>ID товара</td>
        <td><?php echo $data['goods']['id']?></td>
    </tr>
    <tr>
        <td>Отображаемое имя</td>
        <td>
            <input type="text" value="<?php echo $data['goods']['name']?>" />
            <button disabled class="btn btn-success"><i class="icon-check"></i> Сохранить</button>
        </td>
    </tr>
    <tr>
        <td>Модель</td>
        <td>
            <input type="text" value="<?php echo $data['goods']['model']?>" />
            <button disabled class="btn btn-success"><i class="icon-check"></i> Сохранить</button>
        </td>
    </tr>
    <tr>
        <td>Ссылка</td>
        <td>
            <input type="text" value="<?php echo $data['goods']['link']?>" />
            <button disabled class="btn btn-success"><i class="icon-check"></i> Сохранить</button>
        </td>
    </tr>
    <tr>
        <td>ID производителя</td>
        <td><?php echo $data['brand']['id']?></td>
    </tr>
    <tr>
        <td>Отображаемое имя производителя</td>
        <td>
            <?php echo $data['brand']['name']?>
        </td>
    </tr>
    <tr>
        <td>Ссылка на производителя</td>
        <td><?php echo $data['brand']['link']?></td>
    </tr>
</table>

<h4>Синонимы</h4>
<table class="table table-bordered table-striped">
    <?php foreach ($data['synonims'] as $synonim):?>
    <tr data-id="<?php echo $synonim['id']?>">
        <td><?php echo $synonim['name']?></td>
        <td>
            <button class="btn btn-mini btn-warning"><i class="icon-remove"></i> Не отображать</button>
            <button class="btn btn-mini btn-danger"><i class="icon-remove"></i> Удалить</button>
        </td>
    </tr>
    <?php endforeach;?>
    <tr data-id="0">
        <td colspan="2">
            <input type="text" name="synonims[]" class="input" /> 
            <button class="btn btn-success"><i class="icon-check"></i> Добавить</button>
        </td>
    </tr>
</table>

<h4>Связанные модели (модификации)</h4>
<table class="table table-bordered table-striped">
    <tr data-id="0">
        <td colspan="2">
            <input type="text" name="synonims[]" class="input" /> 
            <button class="btn btn-success"><i class="icon-check"></i> Добавить</button>
        </td>
    </tr>
</table>

<h4>Картинки</h4><button class="show btn" data-target="images"><i class="icon-list"></i> Показать</button>
<div id="images" style="display: none">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Preview</th>
                <th>ID</th>
                <th>Link</th>
                <th>Priority</th>
                <th>Mime type</th>
                <th>Filesize</th>
                <th>Ext</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['images'] as $image):?>
            <tr data-id="<?php echo $image['id']?>">
                <td>
                    <img style="max-width: 100px; height: auto;" src="<?php
                                            echo Yii::app()->createUrl("site/download", array(
                                                'id'=>$image['file'],
                                                'filename'=>$image['link'],
                                                'link'=>$data['goods']['link'],
                                                'language'=>  Language::getCurrentZone(),
                                            ));
                                            ?>" />
                </td>
                <td><?php echo $image['id']?></td>
                <td><?php echo $image['link']?></td>
                <td><?php echo $image['priority']?></td>
                <td><?php echo $image['mime_type']?></td>
                <td><?php echo $image['filesize']?></td>
                <td><?php echo $image['ext']?></td>
                <td>
                    <button class="btn btn-mini btn-warning"><i class="icon-remove"></i> Не показывать</button>
                </td>
            </tr>
            <?php endforeach;?>
            <tr data-id="0">
                <td colspan="8">
                    URL: <input type="text" name="synonims[]" class="input-large" />
                    <button class="btn btn-success"><i class="icon-check"></i> Добавить</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<h4>Видео</h4>
<button class="show btn" data-target="videos"><i class="icon-list"></i> Показать</button>
<div id="videos" style="display: none;">
    <table class="table table-bordered table-striped">
        <?php $exclude = array()?>
        <?php foreach ($data['videos'] as $video):?>
        <?php $exclude[$video['link']] = true?>
        <tr>
            <td>
                ID: <?php echo $video['link']?><br/>
                Priority: <?php echo $video['priority']?><br/>
                <iframe width="540" height="315" src="//www.youtube.com/embed/<?php echo $video['link']?>?rel=0" frameborder="0" allowfullscreen></iframe>
            <td>
                <button class="btn btn-mini btn-danger"><i class="icon-remove"></i> Удалить</button> 
                <button class="btn btn-mini btn-warning"><i class="icon-remove"></i> Не показывать</button>
            </td>
        </tr>
        <?php endforeach;?>
        <tr>
            <td><b>From youtube:</b></td>
            <td></td>
        </tr>
        <?php foreach ($data['youtube'] as $video):?>
        <?php if (isset($exclude[$video])) continue;?>
        <tr>
            <td>
                ID: <?php echo $video?><br/>
                <iframe width="540" height="315" src="//www.youtube.com/embed/<?php echo $video?>?rel=0" frameborder="0" allowfullscreen></iframe>
            <td>
                Priotity: <br />
                <input type="text" value="99" /> <br />
                <button class="btn btn-mini btn-success"><i class="icon-check"></i> Добавить</button>
            </td>
        </tr>
        <?php endforeach;?>
        <tr data-id="0">
            <td>
                Priotity: <br />
                <input type="text" value="99" /> <br />
                Link: <br />
                <input type="text" name="synonims[]" class="input-large" /> <br />
                <button class="btn btn-mini btn-success"><i class="icon-check"></i> Добавить</button>
            </td>
            <td>

            </td>
        </tr>
    </table>
</div>

<h4>FAQ</h4>
<button class="show btn" data-target="faq"><i class="icon-list"></i> Показать</button>
<div id="faq" style="display: none;">
    <table class="table table-bordered table-striped">
        <tr>
            <td>
                <b>FAQ, связанные с товаром:</b>
            </td>
        </tr>
        <?php foreach ($data['questions'] as $question): ?>
        <tr>
            <td>
                <input style="width: 500px;" type="text" value="<?php echo $question['question']?>" />
                <br />
                Соответствует модели <b><i><?php echo $question['name']?></i></b>
                <br />
                <textarea style="width: 500px; height: 250px;"><?php echo $question['answer'] ?></textarea>
                <br />
                <button disabled class="btn btn-success"><i class="icon-check"></i> Сохранить</button>
            </td>
        </tr>
        <?php endforeach;?>
        <tr>
            <td>
                <b>Добавить новый:</b><br/>
                <input style="width: 500px;" type="text" value="" />
                <br />
                <textarea style="width: 500px; height: 250px;"></textarea>
                <br />
                <button class="btn btn-success"><i class="icon-check"></i> Добавить</button>
            </td>
        </tr>
        <tr>
            <td>
                <b>FAQ, которые еще не были связаны с товаром:</b>
            </td>
        </tr>
        <?php foreach ($data['new_questions'] as $question): ?>
        <tr>
            <td>
                <input style="width: 500px;" type="text" value="<?php echo $question['question']?>" />
                <br />
                Соответствует модели <b><i><?php echo $question['name']?></i></b>
                <br />
                <textarea style="width: 500px; height: 250px;"><?php echo $question['answer'] ?></textarea>
                <br />
                <button disabled class="btn btn-success"><i class="icon-check"></i> Сохранить</button>
            </td>
        </tr>
        <?php endforeach;?>
    </table>
</div>

<h4>Обзоры</h4>
<button class="show btn" data-target="reviews"><i class="icon-list"></i> Показать</button>
<div id="reviews" style="display: none;">
    <table class="table table-bordered table-striped">
        <tr>
            <td>
                <b>Обзоры, связанные с товаром:</b>
            </td>
        </tr>
        <?php foreach ($data['reviews'] as $review): ?>
        <tr>
            <td>
                <input style="width: 500px;" type="text" value="<?php echo $review['title']?>" />
                <br />
                Соответствует модели <b><i><?php echo $review['name']?></i></b>
                <br />
                <textarea style="width: 500px; height: 250px;"><?php echo $review['content'] ?></textarea>
                <br />
                <button disabled class="btn btn-success"><i class="icon-check"></i> Сохранить</button>
            </td>
        </tr>
        <?php endforeach;?>
        <tr>
            <td>
                <b>Добавить новый:</b><br/>
                <input style="width: 500px;" type="text" value="" />
                <br />
                <textarea style="width: 500px; height: 250px;"></textarea>
                <br />
                <button class="btn btn-success"><i class="icon-check"></i> Добавить</button>
            </td>
        </tr>
        <tr>
            <td>
                <b>Обзоры, которые еще не были связаны с товаром:</b>
            </td>
        </tr>
        <?php foreach ($data['new_reviews'] as $review): ?>
        <tr>
            <td>
                <input style="width: 500px;" type="text" value="<?php echo $review['title']?>" />
                <br />
                Соответствует модели <b><i><?php echo $review['name']?></i></b>
                <br />
                <textarea style="width: 500px; height: 250px;"><?php echo $review['content'] ?></textarea>
                <br />
                <button disabled class="btn btn-success"><i class="icon-check"></i> Сохранить</button>
            </td>
        </tr>
        <?php endforeach;?>
    </table>
</div>
<br />
<br />