<style>

    table.compare {
        border-collapse: collapse; 
    }
    table.compare td, table.compare th {
        padding: 3px; 
        border: 1px solid black; 
    }

</style>
<table class="compare">
    <thead>
        <tr>
            <th></th>
            <?php foreach($goods as $product):?>
                <th><?=$product?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $characteristic=>$values):?>
        <tr>
            <th><?=$characteristic?></th>
            <?php foreach ($values as $value): ?>
            <td><?=$value?></td>
            <?php endforeach;?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>