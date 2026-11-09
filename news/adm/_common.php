<?php
define('G5_IS_ADMIN', true);
require_once '../common.php';
require_once G5_ADMIN_PATH . '/admin.lib.php';

// 관리자 영역 — 검색엔진 색인 방지
if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header('X-Robots-Tag: noindex, nofollow', false);
}


if (function_exists('g5_check_data_htaccess')) {
    g5_check_data_htaccess();
}

if (isset($token)) {
    $token = @htmlspecialchars(strip_tags($token), ENT_QUOTES);
}

run_event('admin_common');
