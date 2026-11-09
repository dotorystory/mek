<?php
/**
 * 푸터 — 전체 게시판 최신 원글 목록 (new.php /public/new/ 와 연동)
 */
if (!defined('_GNUBOARD_')) {
    exit;
}

global $g5;

$rows = 5;
$list = array();
$fetch_limit = 30;

// bo_use_search 조건은 사용하지 않음 (사이트 게시판이 전부 0인 경우 목록이 비게 됨)
$sql = " select a.bo_table, a.wr_id, a.wr_parent, b.bo_subject
           from {$g5['board_new_table']} a
           join {$g5['board_table']} b on a.bo_table = b.bo_table
          where a.wr_id = a.wr_parent
          order by a.bn_id desc
          limit {$fetch_limit} ";
$result = sql_query($sql);
while ($row = sql_fetch_array($result)) {
    if (count($list) >= $rows) {
        break;
    }
    $write_table = $g5['write_prefix'] . preg_replace('/[^a-z0-9_]/i', '', $row['bo_table']);
    $wr_id = (int) $row['wr_id'];
    $wr = sql_fetch(" select wr_id, wr_subject, wr_comment, wr_option from {$write_table} where wr_id = '{$wr_id}' ");
    if (!$wr || $wr['wr_subject'] === '') {
        continue;
    }
    if (isset($wr['wr_option']) && strstr($wr['wr_option'], 'secret')) {
        continue;
    }
    $list[] = array(
        'bo_table' => $row['bo_table'],
        'wr_id'    => $wr['wr_id'],
        'subject'  => get_text($wr['wr_subject']),
        'comment'  => (int) $wr['wr_comment'],
        'href'     => get_pretty_url($row['bo_table'], $wr['wr_id']),
    );
}

$new_href = G5_URL.'/public/new/';
$skin_css = G5_THEME_PATH.'/skin/latest/notice/style.css';
if (is_file($skin_css)) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/latest/notice/style.css?ver='.filemtime($skin_css).'">', 0);
}
?>
<div class="notice ft_cnt">
    <h2><a href="<?php echo $new_href; ?>">최신글 보기</a></h2>
    <ul>
    <?php
    $list_count = count($list);
    for ($i = 0; $i < $list_count; $i++) {
    ?>
        <li>
            <a href="<?php echo $list[$i]['href']; ?>"><?php echo $list[$i]['subject']; ?><?php
            if ($list[$i]['comment'] > 0) {
                echo '<span class="cnt_cm">'.$list[$i]['comment'].'</span>';
            }
            ?></a>
        </li>
    <?php } ?>
    <?php if ($list_count == 0) { ?>
        <li class="empty_li">게시물이 없습니다.</li>
    <?php } ?>
    </ul>
</div>
