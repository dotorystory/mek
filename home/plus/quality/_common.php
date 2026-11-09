<?php
// quality 폴더에서 home 폴더의 common.php까지
// /home/plus/quality -> /home/common.php
$_common_path = dirname(dirname(__DIR__)) . '/common.php';
if (file_exists($_common_path)) {
    include_once($_common_path);
} else {
    // 상대 경로로 재시도
    include_once('../../common.php');
}

// common.php 로드 확인
if (!defined('_GNUBOARD_')) {
    die('그누보드가 로드되지 않았습니다.');
}

// 권한 체크 (레벨 3 이상)
// 로그인하지 않은 사용자는 로그인 페이지로 리다이렉트
if (!isset($is_member) || !$is_member) {
    if (defined('G5_BBS_URL')) {
        $return_url = urlencode(G5_URL.'/plus/quality/');
        goto_url(G5_BBS_URL.'/login.php?url='.$return_url);
        exit;
    } else {
        die('로그인이 필요합니다.');
    }
}

// 로그인했지만 레벨이 3 미만인 경우
if (!isset($member['mb_level']) || $member['mb_level'] < 3) {
    if (defined('G5_URL')) {
        alert('품질 검사표 시스템은 회원 레벨 3 이상만 사용할 수 있습니다.', G5_URL);
    } else {
        die('품질 검사표 시스템은 회원 레벨 3 이상만 사용할 수 있습니다.');
    }
}

// 품질 검사표 관련 상수 정의 (common.php 로드 후)
if (defined('G5_PATH')) {
    define('G5_QUALITY_PATH', G5_PATH . '/plus/quality');
    define('G5_QUALITY_URL', G5_URL . '/plus/quality');
    define('G5_QUALITY_UPLOAD_PATH', G5_PATH . '/../upload/quality');
    define('G5_QUALITY_UPLOAD_URL', G5_URL . '/../upload/quality');
    
    // 품질 검사표 관련 라이브러리 로드
    $quality_lib_path = G5_QUALITY_PATH . '/lib';
    if (is_dir($quality_lib_path)) {
        $lib_files = glob($quality_lib_path . '/*.lib.php');
        foreach ($lib_files as $lib_file) {
            if (file_exists($lib_file)) {
                include_once($lib_file);
            }
        }
    }
}

