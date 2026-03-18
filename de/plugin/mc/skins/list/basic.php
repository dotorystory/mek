<?php
/**
 * 기본 목록보기 레이아웃 스킨
 */
/** @var \mc\Board $board */
/** @var array $write */


?>
<div class="mc-control-row" style="border:0" >

    <div class="mc-checked-list">
        <?php foreach ($board->getExistValues() as $item): ?>
            <span onclick="mc.removeCheckedItem('list', this)" class="mc-checked-item" data-name="<?php echo $item['name']; ?>" data-value="<?php echo $item['value']; ?>"><?php echo $item['value']; ?><em>✗</em></span>
        <?php endforeach; ?>
        <span class="mc-checked-reset" onclick="mc.resetCheckedItem()">검색초기화</span>
    </div>
</div>
<?php
foreach ($board->getColumns() as $column): ?>
    <?php if($column->searchable):?>
    <div class="mc-control-row">
        <span class="mc-control-label"><?php echo $column->title; ?></span><?php $column->render(); ?>
    </div>
    <?php endif;?>
<?php endforeach; ?>
