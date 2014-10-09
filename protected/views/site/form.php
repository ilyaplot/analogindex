<script type="text/javascript">
    function formatNone(str)
    {
        return str;
    }

    function formatWeight(weight)
    {
        var i = 0, type = ['г','кг'];
        while((weight / 1000 | 0) && i < type.length - 1) {
            weight /= 1000;
            i++;
        }
        return weight.toFixed(2) + ' ' + type[i];
    }
    
    function formatFreq(freq)
    {
        var i = 0, type = ['Гц','Кгц','Мгц','Ггц'];
        while((freq / 1000 | 0) && i < type.length - 1) {
            freq /= 1000;
            i++;
        }
        return freq.toFixed(2) + ' ' + type[i];
    }
    
    function formatSize(size)
    {
        var i = 0, type = ['б','Кб','Мб','Гб','Тб'];
        while((size / 1024 | 0) && i < type.length - 1) {
            size /= 1024;
            i++;
        }
        return size.toFixed(2) + ' ' + type[i];
    }
    
    $(document).ready(function(){
        if ($(".brands:checked").length == 0)
            $("#select-all").text("Выбрать все").attr("data-selected", 0) ;

        $("#select-all").click(function(){
            if ($(this).attr("data-selected") == 0)
            {
                $(".brands").attr("checked", true);
                $(this).text("Снять все").attr("data-selected", 1) ;
            } else {
                $(".brands").attr("checked", false);
                $(this).text("Выбрать все").attr("data-selected", 0) ;
            }
            return false;
        });
    });
</script>
<?php
echo CHtml::beginForm(Yii::app()->createUrl("site/type", array("type"=>$type)), 'get');
echo CHtml::openTag("div")."Производители:".CHtml::closeTag("div");
echo CHtml::link("Снять все", "#", array("id"=>"select-all", "data-selected"=>1));
echo "<br />";
foreach ($brands as $brand)
{
    echo CHtml::label($brand->name, "brands-{$brand->id}");
    echo CHtml::checkBox("Brands[{$brand->id}]", $brand->checked, array("id"=>"brands-{$brand->id}", 'class'=>'brands'));
    echo "&nbsp;";
}
echo "<br />";
echo $selector->render(3);
echo $selector->render(5);
echo $selector->render(6);
echo $selector->render(8);
echo $selector->render(9);
echo $selector->render(13);
echo $selector->render(22);
echo CHtml::openTag("div")."Операционные системы:".CHtml::closeTag("div");
echo $selector->render(14);
echo CHtml::submitButton('Отправить');
echo CHtml::endForm();
?>
<ul class="search_result-bl clr">
<?php foreach ($goods as $key=>$product):?>
<li>
                        <div class="flLeft">
                            <div class="search_result-id"><?php echo $key+1+$pages->getCurrentPage()*10?>.</div>
                            <div class="search_result-photo">
                                <a href="<?php echo Yii::app()->createUrl("site/goods", array('link'=>$product->link, 'brand'=>$product->brand_data->link, 'type'=>$product->type_data->link, 'language'=>Language::getCurrentZone()))?>">
                                    <?php if (isset($product->primary_image->image_data->size3_data->id)):?>
                                        <img src="<?php echo Yii::app()->createUrl("files/image", array(
                                            'id'=>$product->primary_image->image_data->size3_data->id,
                                            'name'=>$product->primary_image->image_data->size3_data->name,
                                            'language'=>Language::getCurrentZone(),
                                            )); ?>" alt="<?php echo $product->brand_data->name." ".$product->name ?>" />
                                    <?php else :?>
                                        <img src="/assets/img/photo/informers/1.png" alt="<?php echo $product->brand_data->name." ".$product->name ?>" />
                                    <?php endif;?>
                                    
                                </a>
                            </div>
                            <div class="search_result-desc">
                                <h2 class="search_result-nameItem">
                                    <a href="<?php echo Yii::app()->createUrl("site/goods", array('link'=>$product->link, 'brand'=>$product->brand_data->link, 'type'=>$product->type_data->link, 'language'=>  Language::getCurrentZone()))?>">
                                        <?php echo $product->brand_data->name ." ". $product->name?>
                                    </a>
                                </h2>
                                <p class="search_result-text">
                                    <?php foreach ($product->getGeneralCharacteristics() as $characteristic):?>
                                        <?php echo $characteristic['characteristic_name'].": ".$characteristic['value'].PHP_EOL; ?><br />
                                    <?php endforeach;?>
                                </p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="flRight">
                            <div class="search_result-p_r">
                                <!--<div class="search_result-price">15 000 р.</div>
                                <div class="search_result-rating">
                                    <ul class="rating">
                                         <li class="full"><a href="#">1</a></li>
                                         <li class=""><a href="#">2</a></li>
                                         <li class=""><a href="#">3</a></li>
                                         <li class=""><a href="#">4</a></li>
                                         <li class=""><a href="#">5</a></li>
                                      </ul>
                                </div>-->
                            </div>
                        </div>
                    </li>

<?php endforeach; ?>
</ul>
<?php 
                    $this->widget('LinkPager', array(
                        'currentPage'=>$pages->getCurrentPage(),
                        'itemCount'=>$pages->getItemCount(),
                        'pageSize'=>10,
                        'maxButtonCount'=>8,
                        'header'=>'',
                        'htmlOptions'=>array('class'=>'pagination'),
                        'firstPageLabel'=>Yii::t("main", "Первая"),
                        'lastPageLabel'=>Yii::t("main", "Последняя")." (".ceil($pages->getItemCount()/10).")",
                        'nextPageLabel'=>Yii::t("main", "Следующая"),
                        'prevPageLabel'=>Yii::t("main", "Предыдущая"),
                    ));
                ?>