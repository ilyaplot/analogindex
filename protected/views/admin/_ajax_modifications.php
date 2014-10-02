<br />
<?php if (empty($data)):?>
<h3>Ничего не найдено. Попробуйте повторить поиск с другими ключевыми словами.</h3>
<?php else:?>
<table class="table table-bordered table-striped" style="max-width: 600px;">
    <thead>
        <tr>
            <th>id</th>
            <th>Наименование</th>
            <th>Синонимы</th>
            <th>Комментарии</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $item): ?>
        <tr>
            <td><?php echo $item['id']?></td>
            <td><?php echo $item['brand'].' ' .$item['name']?></td>
            <td><?php echo $item['synonims']?></td>
            <td>
                Комментарий ru:<br />
                <input type="text" name="newmodifications[<?php echo $item['id']?>][ru]" /><br />
                Комментарий en:<br />
                <input type="text" name="newmodifications[<?php echo $item['id']?>][en]" />
            </td>
            <td>
                <input type="checkbox" name="newmodifications[<?php echo $item['id']?>][merge]" value="1" /> Выбрать
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif;?>