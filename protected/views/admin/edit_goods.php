<style>
    div.input-emulate {
        height: 20px; 
        width: 206px; 
        padding: 7px;
    }
    
    button.hidden {
        display: none;
    }
    
    #images table tr:first-child
    {
        background-color: #b2dba1;
    }
    
    #images table tr:first-child button.up {
        display: none;
    }
    
    #images table tr:last-child button.down {
        display: none;
    }
</style>

<script type="text/javascript">
    /**
     * Возвращает строку для подписи к фото на нужном языке
     * @returns string
     */
    function AnalogindexLightboxLabel(a,b){
        return"<?php echo Yii::t("main", "Фотография ")?>"+a+" <?php echo Yii::t("main", " из ")?> "+b
    };
</script>
<script src="/assets/js/lightbox.js"></script>
<link href="/assets/css/lightbox.css" rel="stylesheet" />
<div class="well"><h4><?php echo $data->brand_data->name." ".$data->name?></h4></div>
<ul class="nav nav-tabs" id="tabs">
  <li class="active"><a href="#home">Общее</a></li>
  <li><a href="#images">Фотографии</a></li>
  <li><a href="#characteristics">Характеристики</a></li>
  <?php if (!$data->is_modification):?>
  <li><a href="#modifications">Модификации</a></li>
  <?php endif; ?>
  <li><a href="#videos">Видео</a></li>
  <li><a href="#reviews">Обзоры</a></li>
  <li><a href="#faq">FAQ</a></li>
  <li><a href="#files">Файлы</a></li>
</ul>
<?php //echo "<pre>"; var_dump($_POST); echo "</pre>";?>

<form id="form-general" action="" method="post" class="form-horizontal" onkeypress="if(event.keyCode == 13) return false;">
    <input type="hidden" value="<?php echo $data->id?>" name="Goods[id]" />
    <div class="tab-content">
        <div class="tab-pane active" id="home">
            <div class="alert">
                <strong>Внимание!</strong> 
                При изменении типа товара, производителя или сслыки товар будет недоступен по старому адресу. <br />
            </div>
            
            <?php if (!empty($errors)):?>
            <div class="alert alert-danger">
            <?php foreach($errors as $message):?>
            
            <?php echo $message?><br />   
            
            <?php endforeach;?>
            </div>
            <?php endif;?>
            
            <?php if (!empty($success)):?>
            <div class="alert alert-success">
            <?php foreach($success as $message):?>
            
            <?php echo $message?><br />   
            
            <?php endforeach;?>
            </div>
            <?php endif;?>
            <div class="control-group">
                <label class="control-label" for="inputBrand">Производитель</label>
                <div class="controls">
                    <input type="hidden" name="Goods[brand]" id="inputBrand" value="<?php echo $data->brand ?>">
                    <div id="inputBrandSpan" class="input-emulate"><?php echo $data->brand_data->name ?></div>
                    <a href="#brandModal" role="button" data-toggle="modal" id="inputBrand" class="btn btn-default" >
                        <i class="icon-edit"></i> Изменить ...
                    </a>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputType">Тип товара</label>
                <div class="controls"> 
                    <select name="Goods[type]" id="inputType">
                        <?php foreach ($types as $type):?>
                        <option value="<?php echo $type->id?>" <?php echo ($type->id == $data->type) ? "selected " : ''?>>
                            <?php echo $type->name->name?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputLink">Ссылка</label>
                <div class="controls">
                    <input type="text" name="Goods[link]" id="inputLink" placeholder="link" value="<?php echo $data->link?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputName">Наименование</label>
                <div class="controls">
                    <input type="text" name='Goods[name]' id="inputName" placeholder="Наименование" value="<?php echo $data->name?>">
                </div>
            </div>
            <table class="table table-bordered table-striped synonims">
                <tr>
                    <td><b>Синонимы:</b></td>
                </tr>
                <?php foreach ($data->synonims as $synonim): ?>
                <tr>
                    <td data-id="<?php echo $synonim->id?>">
                                  
                        <input type="text" name="synonims[<?php echo $synonim->id?>][name]" value="<?php echo $synonim->name?>" placeholder="Синоним"/><br />
                        <input type="checkbox" value='1' name="synonims[<?php echo $synonim->id?>][visibled]" <?php echo ($synonim->visibled) ? "checked " : ''?>/> Отображать<br />
                        <input type="checkbox" name="synonims[<?php echo $synonim->id?>][remove]" value="1" /> Удалить
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td><b>Добавить синонимы:</b></td>
                </tr>
                <tr>
                    <td>
                        <input class="newsynonim" type="text" name="newsynonims[0]" value="" placeholder="Новый синоним"/> <br />
                        <input type="checkbox" name="newsynonimscheck[0]" checked/> Отображать
                    </td>
                </tr>
            </table>
        </div>
        <div class="tab-pane" id="images">
            <div class="alert alert-info">
                Для изменения порядка отображения изображений необходимо сохранять изменения товара.<br />
                Первое изображение в списке является основным и отображается как картинка товара.<br />
            </div>
            <table class="table table-bordered">
                <?php foreach($data->images as $image):?>
                <tr>
                    <td>
                        <input class="priority" type="hidden" name="images[<?php echo $image->id?>][priority]" value="<?php echo $image->priority?>" />
                        <a href="<?php echo Yii::app()->createUrl("files/image", array(
                            'id'=>$image->image_data->file_data->id,
                            'name'=>$image->image_data->file_data->name,
                            'language'=>Language::getCurrentZone(),
                            )); ?>" data-lightbox="item<?php echo $image->id?>">
                        <img style="border: 1px dashed #666;" src="<?php echo Yii::app()->createUrl("files/image", array(
                            'id'=>$image->image_data->resized_list->file_data->id,
                            'name'=>$image->image_data->resized_list->file_data->name,
                            'language'=>Language::getCurrentZone(),
                            )); ?>"></a>
                        <br />
                        Размер: <?php echo Yii::app()->format->formatSize($image->image_data->file_data->size)?><br />
                        Ширина: <?php echo $image->image_data->width?> px<br />
                        Высота: <?php echo $image->image_data->height?> px<br />
                        Mime type: <?php echo $image->image_data->file_data->mime_type?><br />
                    </a>
                    </td> 
                    <td>
                        <button class="btn btn-mini up"><i class="icon-chevron-up"></i></button>
                        <button class="btn btn-mini down"><i class="icon-chevron-down"></i></button>
                        <br />
                        <button class="btn btn-danger disable<?php echo ($image->disabled) ? ' hidden' : ''?>"><i class="icon-remove"></i> Скрыть</button>
                        <button class="btn btn-success enable<?php echo (!$image->disabled) ? ' hidden' : ''?>"><i class="icon-ok"></i> Показать</button>
                    </td>
                </tr>
                <?php endforeach;?>
            </table>
        </div>
        <div class="tab-pane" id="characteristics">...</div>
        <?php if (!$data->is_modification):?>
        <div class="tab-pane" id="modifications">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Производитель</th>
                        <th>Наименование</th>
                        <th>Синонимы</th>
                        <th>Комментарии</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data->modifications as $modification):?>
                    <tr>
                        <td><?php echo $modification->children->id?></td>
                        <td><?php echo $modification->children->brand_data->name?></td>
                        <td><?php echo $modification->children->name ?></td>
                        <td><?php 
                            $synonims = array();
                            foreach ($modification->children->synonims as $synonim)
                                $synonims[] = $synonim['name'];
                            echo implode(", <br>", $synonims);
                        ?></td>
                        <td>
                            <td>
                                Комментарий ru:<br />
                                <input type="text" name="ModificationsComments[<?php echo $modification->comment_ru->id?>]" value="<?php echo $modification->comment_ru->comment?>"/><br />
                                Комментарий en:<br />
                                <input type="text" name="ModificationsComments[<?php echo $modification->comment_en->id?>]" value="<?php echo $modification->comment_en->comment?>"/><br />
                            </td>
                        </td>
                        <td>
                            <input type="checkbox" name="DeleteModifications[<?php echo $modification->id?>]" value="1"  />Удалить связку модификаций
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
            <div class="well">
                <div class="control-group">
                    <input <?php echo ($data->is_modification) ? "checked " : ''; ?>type="checkbox" name='Goods[is_modification]' id="inputModification" value="1"> Использовать текущий товар как модификацию (не отображать в виджетах)
                </div>
                <div class="control-group">
                    <div class="input-append">
                        <input class="span9" id="modifications-search" value="<?php echo $data->brand_data->name." ".$data->name?>" placeholder="Товар для поиска" type="text">
                        <input class="btn" id="modifications-search-button" type="button" value="Найти" />
                        <br />
                    </div>
                    <div id="modifications-results">
                        
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="tab-pane" id="videos">...</div>
        <div class="tab-pane" id="reviews">...</div>
        <div class="tab-pane" id="faq">...</div>
        <div class="tab-pane" id="files">...</div>
    </div>
    <hr />
    <div class="well">
        <input disabled id="form-reset" class="btn btn-info" type="reset" value="Отменить изменения" />
        <input class="btn btn-danger" type="submit" name="delete" value="Удалить" />
        <input disabled id="form-save" class="btn btn-success" type="submit" name="save" value="Сохранить" />
    </div>
</form>
<?php
$this->renderPartial("_modal_brand_change");
?>
<script>
  $(function () {
    //$('#tabs a:first').tab('show');
    
  });  
  $("#form-general input, #form-general select, #form-general textarea").change(function(){
        $("#form-reset").attr("disabled", false);
        $("#form-save").attr("disabled", false);
  });
  
  $('#tabs a').click(function (e){
    e.preventDefault();
    $(this).tab('show');
  });
  
    $("#images .up, #images .down").click(function(){
        var row = $(this).parents("tr:first");
        if ($(this).is(".up")) {
            row.insertBefore(row.prev());
        } else {
            row.insertAfter(row.next());
        }

        $("#images table tr").each(function(index, Element){
            $("#images table tr:eq("+index+") .priority").val(255-index);
        });
        
        return false;
    });
    var synonimsCount = 1;
    $("#home").on("change", '.newsynonim', function (){
        $(this).removeClass("newsynonim");
        $("table.synonims").append('<tr>'+
            '<td><input class="newsynonim" type="text" name="newsynonims['+synonimsCount+']" value="" placeholder="Новый синоним"/> <br />'+
            '<input type="checkbox" checked name="newsynonimscheck['+synonimsCount+']" /> Отображать</td></tr>'
        );
        synonimsCount++;
    });
    
    $("#modifications-search").submit(function(){
        return false;
    });
    <?php 
        $exclude = array($data->id);
        foreach ($data->modifications as $modification)
            $exclude[] = $modification->goods_children;
        $exclude = implode(", ", $exclude);
    ?>
    $("#modifications-search-button").click(function(){
        var str = $("#modifications-search").val();
        $("#modifications-results").html("<strong>Подождите, выполняется поиск...</strong>");
        $.ajax({
            url:'<?php echo Yii::app()->createUrl("admin/ajaxmodifications")?>',
            data: {type:'<?php echo $data->type?>', exclude:'<?php echo $exclude?>', search:str},
            dataType: 'html',
            success: function(data) {
                $("#modifications-results").html(data);
            },
            error: function(e) {
                $("#modifications-results").html(e.responseText);
            }
        });
    });
</script>