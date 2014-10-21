<?php
/**
 * var $id
 * var $title
 * var $name
 * var $items
 * var $itemsSelected
 */
?>
        <div class="filter-select">
            <label for="filter-<?php echo $id?>"><?php echo $title?></label> 
            <select id="filer-<?php echo $id?>">
                <option selected="1" value="0">Все</option>
            <?php foreach ($items as $item):?>
                <?php 
                    $display = true;
                    if (in_array($item->link, $itemsSelected))
                    {
                        $selected[$id][] = $item;
                        $display = false;
                    }       
                ?>
                <option<?php echo ($display) ? '' : ' style="display:none;"'?> value="<?php echo $item->link?>"><?php echo $item->name?></option>
            <?php endforeach;?>
            </select>
            <ul class="list" data-name="<?php echo $name?>" data-title="Все">
                <?php if(empty($selected[$id])):?>
                <li>Все <input type="hidden" name="<?php echo $name?>" value="0" /></li>
                <?php else: ?>
                <?php foreach ($selected[$id] as $item):?>
                <li><?php echo $item->name ?> <input type="hidden" name="<?php echo $name?>" value="<?php echo $item->link?>" />
                    <span class="remove" title="Удалить">x</span>
                </li>
                <?php endforeach;?>
                <?php endif;?>
            </ul>
            <div class="clear"></div>
        </div>