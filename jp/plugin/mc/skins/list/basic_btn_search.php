
<?php
/**
 * 기본 검색버튼형 목록보기 레이아웃 스킨
 */
/** @var \mc\Board $board */
/** @var array $write */


?>
<div class="filter_tit">
  <p>Filter By</p>
  <span></span>
</div>
<?php
foreach ($board->getColumns() as $column): ?>
    <?php if($column->searchable):?>

        <div class="mc-control-row" data-searchmode="btn">

            <span class="mc-control-label"><?php echo $column->title; ?><i class="ri-arrow-down-s-line"></i></span><?php $column->render(); ?>
        </div>
    <?php endif;?>
<?php endforeach; ?>

<div class="mc-control-row" style="border:0" >

    <div class="mc-checked-list">
        <button type="button" class="mc-btn-submit" onclick="mc_search(this.form);this.form.submit()">検索</button>
        <?php foreach ($board->getExistValues() as $item): ?>
            <span onclick="mc.removeCheckedItem('list', this)" class="mc-checked-item" data-name="<?php echo $item['name']; ?>" data-value="<?php echo $item['value']; ?>"><?php echo $item['value']; ?><em>✗</em></span>
        <?php endforeach; ?>
        <span class="mc-checked-reset" onclick="mc.resetCheckedItem()">検索をリセット</span>
    </div>
</div>
<script>
$("#bo_gall .mc-control-row .mc-controls").click(function(event){
  event.stopPropagation();
});

$("#bo_gall .mc-control-row").click(function(){
  $(this).find(".mc-control-label").toggleClass("on");
  $(this).find(".mc-controls").toggleClass("on");
});

</script>
