<?php
/**
 * 기본 목록보기 레이아웃 스킨
 */
/** @var \mc\Board $board */
/** @var array $write */


?>
<div class="mc-control-row" style="border:0">
    <div class="mc-checked-list">
        <?php foreach ($board->getExistValues() as $item): ?>
            <span onclick="mc.removeCheckedItem('list', this)" class="mc-checked-item" data-name="<?php echo $item['name']; ?>" data-value="<?php echo $item['value']; ?>"><?php echo $item['value']; ?><em>✗</em></span>
        <?php endforeach; ?>
        <span class="mc-checked-reset" onclick="mc.resetCheckedItem()">초기화</span>
    </div>
</div>
<?php
foreach ($board->getColumns() as $column): ?>
    <?php if ($column->searchable): ?>
        <div class="mc-control-row mc-control-row-<?php echo $column->name; ?>">
            <span class="mc-control-label"><?php echo $column->title; ?></span><?php $column->render(); ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
<script>

    function mc_list_more(field, items, values) {

        var controls = $('.mc-control-row-' + field + ' .mc-controls');
        var inner = controls.wrapInner('<div class="mc-toggle"></div>');
        var total = controls.find(':input[data-name]').length - items.length;
        var html = '<div class="mc-' + field + '-top" style="padding-top:10px;padding-bottom:10px">';
        for (var i = 0; i < items.length; i++) {
            var v = items[i];
            var checked = $.inArray(v, values) > -1 ? ' checked' : '';
            html += '<label class="mc-control-radio"><input type="checkbox" value="' + v + '" ' + checked + '>' + v + '</label>';
        }
        html += '<span class="mc-control-radio mc-more">더보기 '+total+'</span>';
        html += '</div>';
        $(html).prependTo(controls);
        var toggle = controls.find('.mc-toggle');
        var toggle_btn = controls.find('.mc-more');
        toggle.hide();
        toggle_btn.on('click', function () {
            toggle.toggle();
        });
        $('.mc-' + field + '-top').find('label').on('click', function () {
            var val = $(this).find(':input').val();
            toggle.find(':checkbox[value="' + val + '"]').trigger('click');
        });
    }

    mc_list_more('company', ["LG전자", "삼성전자", "BenQ", "EIZO"], "<?php echo $board->values['company'];?>".split('|'));
    mc_list_more('size', ["68cm(27형)", "76cm(30형)", "107cm(42형)", "124cm(49형)"], "<?php echo $board->values['size'];?>".split('|'));

</script>


