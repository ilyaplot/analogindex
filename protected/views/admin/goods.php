<form action="<?php echo Yii::app()->createUrl("admin/goods")?>" method="get">
    <input type="text" name="search" value="<?php echo $search?>" />
    <input type="submit" value="Найти" />
</form>

<div class="pagination">
  <ul>
    <?php for ($i = 1; $i < $maxPages; $i++):?>
      <li<?php echo ($i == $currentPage) ? ' class="active"' : ''?>><a href="<?php echo Yii::app()->createUrl("admin/goods", array("page"=>$i, "search"=>$search))?>"><?php echo $i?></a></li>
    <?php endfor;?>
  </ul>
</div>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Производитель</th>
            <th>Наименование</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($goods as $item):?>
        <tr>
            <td><?php echo $item['id']?></td>
            <td><?php echo $item['brand']?></td>
            <td><?php echo $item['name']?></td>
            <td>
                <a target="_blank" href="<?php echo Yii::app()->createUrl("admin/goodsedit", array("id"=>$item['id']))?>" class="btn btn-mini"><i class="icon-edit"></i> Редактировать</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="pagination">
  <ul>
    <?php for ($i = 1; $i < $maxPages; $i++):?>
      <li<?php echo ($i == $currentPage) ? ' class="active"' : ''?>><a href="<?php echo Yii::app()->createUrl("admin/goods", array("page"=>$i, "search"=>$search))?>"><?php echo $i?></a></li>
    <?php endfor;?>
  </ul>
</div>