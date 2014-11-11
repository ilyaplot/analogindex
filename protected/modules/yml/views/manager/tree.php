<script type="text/javascript">
    $(document).ready(function(){
        $("#tree-catalog input[type=checkbox]").change(function(){
            var checked = $(this).attr("checked");
            if (typeof(checked) == 'undefined') {
                checked = false;
            }
            $(this).parent().find("input[type=checkbox]").attr("checked", checked);
        });
        
        $("#catalog-select").change(function(){
            $(this).parent("form").trigger("submit");
        });
    });
</script>
<?php echo CHtml::beginForm(Yii::app()->createUrl("/yml/manager/index"), 'get')?>
<?php echo CHtml::label("Выбор каталога: ", "sources");?>
<?php echo CHtml::dropDownList("catalog", $catalog, $sources, ["id"=>"catalog-select"]);?>
<?php echo CHtml::endForm();?>

<?php echo CHtml::beginForm('', "post");?>
<?php $this->widget('CTreeView', array('data' => $data, 'htmlOptions'=>['id'=>"tree-catalog"])); ?>
<?php echo CHtml::submitButton('Сохранить', ['name'=>'catalogs-save'])?>
<?php echo CHtml::endForm();?>