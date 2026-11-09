<?php
$bo_table = $_GET['bo_table'];
$column = $_GET['column'];
$columns = mc_board($bo_table)->getColumns();

if (empty($columns[$column])) {
    return;
}
$item = $columns[$column];
echo '</pre>';
print_r($item);
echo '</pre>';
?>

<table>
    <tr>
        <th>title</th>
        <td><?php echo $item->title;?></td>
    </tr>
</table>
