<table>
    <thead>
        <tr>
            <th>Название из источника</th>
            <th>RU</th>
            <th>EN</th>
            
        </tr>
    </thead>
    <tbody>
        <form action="" method="post" >
        <?php foreach ($list as $item): ?>
        <tr class="item" data-id = '<?php echo $item['id']?>'>
            <td><?php echo $item['name']?></td>
            <td><input type="text" name="ru[<?php echo $item['id']?>]" value="<?php echo $item['ru']?>" /></td>
            <td><input type="text" name="en[<?php echo $item['id']?>]" value="<?php echo $item['en']?>" /></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2"></td>
            <td>
                <input type="submit" value="Сохранить" /> 
                <input type="reset" value="Отменить" />
            </td>
        </tr>
        </form>
    </tbody>
</table>