<menu id="nav-top">
    <ul class="clr">
        <?php foreach ($types as $key=>$type):?>
        <li class="item<?php echo $key+1?>"><a href="#"><?php echo $type->name->name?></a>
        <!--<ul>
            <li><a href="#">iOS</a></li>
            <li><a href="#">Windows</a></li>
            <li><a href="#">Android</a></li>
            <li><a href="#">Другое</a></li>
        </ul>-->
        </li>
        <?php endforeach;?>
        <li></li>
    </ul>
</menu>