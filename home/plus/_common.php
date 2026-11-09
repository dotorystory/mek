<?php
// plus 폴더에서 home 폴더의 common.php까지
$_common_path = dirname(__DIR__) . '/common.php';
if (file_exists($_common_path)) {
    include_once($_common_path);
} else {
    // 상대 경로로 재시도
    include_once('../common.php');
}