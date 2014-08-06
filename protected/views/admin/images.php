<?php if (isset($_POST['url'])) : ?>
<h3>Url поставлен в очередь на скачивание </h3>
<?php endif; ?>
Осталось <?php echo count($list) ?> шт</br>
<script type='text/javascript'>
    function clc(form)
    {
        var fid = document.getElementById(form);
        var newInput = document.createElement('input');
        var br = document.createElement('br');
        newInput.name = 'url[]';
        newInput.type= 'text';
        fid.appendChild(newInput);
        fid.appendChild(br);
        return false;
    }
</script>
<table border=1>
    <?php foreach ($list as $item) : ?>
    <tr>
        <td><?php echo $item['name'] ?></td>
        <td>
            <form id="frm<?php echo $item['id']?>" action='' method='post'>
                <input type='hidden' name='id' value='<?php echo $item['id']?>' />
                <input type='submit' value='Отправить' />
                <button  onclick="return clc('frm<?php echo $item['id']?>');">+</button>
                <input type='text' name='url[]' placeholder='http://link.to/image' /></br>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
        