<div class="row">
        <div class="col s4 m4">
                <?php $this->widget('application.widgets.ListGoodsWidget', array("type"=>'tablet','limit'=>150)); ?>
        </div>
        <div class="col s4 m4">
                <?php $this->widget('application.widgets.ListGoodsWidget', array("type"=>'pda','limit'=>150)); ?>
                
        </div>
        <div class="col s4 m4">
                <?php $this->widget('application.widgets.ListGoodsWidget', array("type"=>'e-book','limit'=>150)); ?>
        </div>
</div>
<link rel="stylesheet" href="/assets/css/all.css" />