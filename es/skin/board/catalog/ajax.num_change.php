<?php
$path = '';
for ($i = 0; $i < 7; $i++) {
    if (file_exists($path . 'common.php'))
        break;
    if (realpath($path) == '/')
        break;
    $path .= '../';
}
include_once($path . 'common.php');
if (!$bo_table || !$wr_num_org || !$wr_num)
    die('Error1');
if ($wr_num_org == $wr_num)
    die('Error2');
$write_table = $g5['write_prefix'] . $bo_table;
$wr_num = $wr_num * -1;
$wr_num_org = $wr_num_org * -1;
// 1) 이동 '고유번호(wr_num)' 존재 여부 체크
$sql = "select count(*) as cnt from {$write_table} where wr_num = '{$wr_num}' and wr_is_comment = 0";
$row = sql_fetch($sql);
if ($row['cnt'] == 0)
    die('Error3');
// 2) 원 게시글의 고유번호를 (임시) '0' 으로 업데이트
$sql = "update {$write_table} set wr_num = '0' where wr_num = '{$wr_num_org}'";
$result_1 = sql_query($sql);
// 3) 관련 이동 대상 게시글들 고유번호 업데이트
if ($wr_num > $wr_num_org) {
    $sql = "update {$write_table} set wr_num = wr_num - 1 where wr_num <= '{$wr_num}' and wr_num > '{$wr_num_org}'";
} else if ($wr_num < $wr_num_org) {
    $sql = "update {$write_table} set wr_num = wr_num + 1 where wr_num > '{$wr_num}' and wr_num < '{$wr_num_org}'";
}
$result_2 = sql_query($sql);
// 4) (임시) '0' 게시글 고유번호를 새 위치 고유번호로 업데이트
if ($wr_num < $wr_num_org)
    $wr_num = $wr_num + 1;
$sql = "update {$write_table} set wr_num = '{$wr_num}' where wr_num = '0'";
$result_3 = sql_query($sql);
$r = $result_1 * $result_2 * $result_3;
if ($r)
    echo 'OK';
else
    echo 'Error';
