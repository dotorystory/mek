<?php
/**
 * /public/new/ — 최신글(전체글) 목록 + 페이지네이션
 * bbs/new.php와 동일 데이터, 제목·목록 URL만 공개 경로에 맞춤
 */
include_once dirname(dirname(__DIR__)) . '/common.php';

if (!defined('_GNUBOARD_')) {
    exit;
}

if (defined('G5_COMMUNITY_USE') && G5_COMMUNITY_USE === false) {
    goto_url(G5_SHOP_URL . '/');
}

$g5['title'] = '최신글 보기';
include_once G5_PATH . '/head.php';

$new_base = G5_URL . '/public/new/';

// 검색 사용 여부와 무관하게 전체 게시판 새글 표시 (공개 웹진용)
$sql_common = " from {$g5['board_new_table']} a, {$g5['board_table']} b, {$g5['group_table']} c
                where a.bo_table = b.bo_table and b.gr_id = c.gr_id ";

$gr_id = isset($_GET['gr_id']) ? substr(preg_replace('#[^a-z0-9_]#i', '', $_GET['gr_id']), 0, 10) : '';
if ($gr_id) {
    $sql_common .= " and b.gr_id = '{$gr_id}' ";
}

$view = isset($_GET['view']) ? $_GET['view'] : '';
if ($view == 'w') {
    $sql_common .= " and a.wr_id = a.wr_parent ";
} else if ($view == 'c') {
    $sql_common .= " and a.wr_id <> a.wr_parent ";
} else {
    $view = '';
}

$mb_id = isset($_GET['mb_id']) ? $_GET['mb_id'] : '';
$mb_id = substr(preg_replace('#[^a-z0-9_]#i', '', $mb_id), 0, 20);
if ($mb_id) {
    $sql_common .= " and a.mb_id = '{$mb_id}' ";
}

$sql_order = " order by a.bn_id desc ";

$sql = " select count(*) as cnt {$sql_common} ";
$row = sql_fetch($sql);
$total_count = (int) $row['cnt'];

$rows = G5_IS_MOBILE ? $config['cf_mobile_page_rows'] : $config['cf_new_rows'];
if ($rows < 1) {
    $rows = 15;
}
$total_page = $rows > 0 ? (int) ceil($total_count / $rows) : 1;
if (!isset($page) || $page < 1) {
    $page = 1;
}
$from_record = ($page - 1) * $rows;

$group_select = '<label for="gr_id" class="sound_only">그룹</label><select name="gr_id" id="gr_id"><option value="">전체그룹';
$sql = " select gr_id, gr_subject from {$g5['group_table']} order by gr_id ";
$result = sql_query($sql);
for ($i = 0; $row = sql_fetch_array($result); $i++) {
    $group_select .= '<option value="' . $row['gr_id'] . '">' . get_text($row['gr_subject']);
}
$group_select .= '</select>';

$list = array();
$sql = " select a.*, b.bo_subject, b.bo_mobile_subject, c.gr_subject, c.gr_id {$sql_common} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);
for ($i = 0; $row = sql_fetch_array($result); $i++) {
    $tmp_write_table = $g5['write_prefix'] . $row['bo_table'];

    if ($row['wr_id'] == $row['wr_parent']) {
        $comment = '';
        $comment_link = '';
        $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$row['wr_id']}' ");
        if (!$row2) {
            continue;
        }
        $list[$i] = $row2;
        $name = get_sideview($row2['mb_id'], get_text(cut_str($row2['wr_name'], $config['cf_cut_name'])), $row2['wr_email'], $row2['wr_homepage']);
        $datetime = substr($row2['wr_datetime'], 0, 10);
        $datetime2 = $row2['wr_datetime'];
        if ($datetime == G5_TIME_YMD) {
            $datetime2 = substr($datetime2, 11, 5);
        } else {
            $datetime2 = substr($datetime2, 5, 5);
        }
    } else {
        $comment = '[코] ';
        $comment_link = '#c_' . $row['wr_id'];
        $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$row['wr_parent']}' ");
        $row3 = sql_fetch(" select mb_id, wr_name, wr_email, wr_homepage, wr_datetime from {$tmp_write_table} where wr_id = '{$row['wr_id']}' ");
        if (!$row2 || !$row3) {
            continue;
        }
        $list[$i] = $row2;
        $list[$i]['wr_id'] = $row['wr_id'];
        $list[$i]['mb_id'] = $row3['mb_id'];
        $list[$i]['wr_name'] = $row3['wr_name'];
        $list[$i]['wr_email'] = $row3['wr_email'];
        $list[$i]['wr_homepage'] = $row3['wr_homepage'];
        $name = get_sideview($row3['mb_id'], get_text(cut_str($row3['wr_name'], $config['cf_cut_name'])), $row3['wr_email'], $row3['wr_homepage']);
        $datetime = substr($row3['wr_datetime'], 0, 10);
        $datetime2 = $row3['wr_datetime'];
        if ($datetime == G5_TIME_YMD) {
            $datetime2 = substr($datetime2, 11, 5);
        } else {
            $datetime2 = substr($datetime2, 5, 5);
        }
    }

    $list[$i]['gr_id'] = $row['gr_id'];
    $list[$i]['bo_table'] = $row['bo_table'];
    $list[$i]['name'] = $name;
    $list[$i]['comment'] = $comment;
    $list[$i]['href'] = get_pretty_url($row['bo_table'], $row2['wr_id'], $comment_link);
    $list[$i]['datetime'] = $datetime;
    $list[$i]['datetime2'] = $datetime2;
    $list[$i]['gr_subject'] = $row['gr_subject'];
    $list[$i]['bo_subject'] = ((G5_IS_MOBILE && $row['bo_mobile_subject']) ? $row['bo_mobile_subject'] : $row['bo_subject']);
    $list[$i]['wr_subject'] = $row2['wr_subject'];
}

$list = array_values($list);

$write_pages = get_paging(
    G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'],
    $page,
    $total_page,
    $new_base . '?gr_id=' . urlencode($gr_id) . '&amp;view=' . urlencode($view) . '&amp;mb_id=' . urlencode($mb_id) . '&amp;page='
);

$new_skin_path = get_skin_path('new', (G5_IS_MOBILE ? $config['cf_mobile_new_skin'] : $config['cf_new_skin']));
$new_skin_url  = get_skin_url('new', (G5_IS_MOBILE ? $config['cf_mobile_new_skin'] : $config['cf_new_skin']));

// 스킨 그룹 링크용 (./new.php 상대경로 대체)
$ha_new_list_url = $new_base;

include_once $new_skin_path . '/new.skin.php';

include_once G5_PATH . '/tail.php';
