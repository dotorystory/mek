<?php
/**
 * 기본 글쓰기 레이아웃 스킨
 */
/** @var \mc\Board $board */
/** @var array $write */

?>

<?php
foreach ($board->getColumns() as $column): ?>
    <div class="mc-control-row">
        <span class="mc-control-label"><?php echo $column->title; ?></span><?php $column->render(); ?>
    </div>
<?php endforeach; ?>
