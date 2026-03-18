<?php
// 에러 로그 활성화
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/elfinder_error.log');

// 모든 과정 로그
file_put_contents('/tmp/elfinder_debug.log', "=== Connector 시작 ===\n", FILE_APPEND);
file_put_contents('/tmp/elfinder_debug.log', date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// _common.php 로드
include_once('../../_common.php');
file_put_contents('/tmp/elfinder_debug.log', "1. _common.php 로드 완료\n", FILE_APPEND);
file_put_contents('/tmp/elfinder_debug.log', "2. \$is_member: " . ($is_member ? "TRUE" : "FALSE") . "\n", FILE_APPEND);
file_put_contents('/tmp/elfinder_debug.log', "3. 회원 ID: " . $member['mb_id'] . "\n", FILE_APPEND);

// 로그인 확인
if (!$is_member) {
    file_put_contents('/tmp/elfinder_debug.log', "❌ 로그인 안됨\n", FILE_APPEND);
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => '로그인이 필요합니다.']);
    exit;
}

// 권한 확인
if ($member['mb_level'] < 3 && $is_admin != 'super') {
    file_put_contents('/tmp/elfinder_debug.log', "❌ 권한 부족: 레벨 " . $member['mb_level'] . "\n", FILE_APPEND);
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => '레벨 3 이상 필요']);
    exit;
}

file_put_contents('/tmp/elfinder_debug.log', "4. 권한 확인 통과\n", FILE_APPEND);

// file_log.lib.php 로드
if (file_exists(G5_LIB_PATH.'/file_log.lib.php')) {
    include_once(G5_LIB_PATH.'/file_log.lib.php');
    file_put_contents('/tmp/elfinder_debug.log', "5. file_log.lib.php 로드 성공\n", FILE_APPEND);
}

// elFinder 클래스 로드
file_put_contents('/tmp/elfinder_debug.log', "6. elFinder 클래스 로드 시작\n", FILE_APPEND);
require './elFinderConnector.class.php';
require './elFinder.class.php';
require './elFinderVolumeDriver.class.php';
require './elFinderVolumeLocalFileSystem.class.php';
file_put_contents('/tmp/elfinder_debug.log', "7. elFinder 클래스 로드 완료\n", FILE_APPEND);

// 업로드 경로
$upload_path = '/var/www/html/mekeng.com/upload/' . $member['mb_id'];
file_put_contents('/tmp/elfinder_debug.log', "8. 업로드 경로: {$upload_path}\n", FILE_APPEND);

if (!is_dir($upload_path)) {
    @mkdir($upload_path, 0775, true);
    @chmod($upload_path, 0775);
    file_put_contents('/tmp/elfinder_debug.log', "9. 폴더 생성 완료\n", FILE_APPEND);
}

// elFinder 설정
$opts = array(
    'roots' => array(
        array(
            'driver' => 'LocalFileSystem',
            'path'   => $upload_path,
            'URL'    => '/upload/' . $member['mb_id'] . '/',
            'alias'  => $member['mb_name'] . '님의 파일',
        )
    ),
    'debug' => true
);

file_put_contents('/tmp/elfinder_debug.log', "10. elFinder 설정 완료\n", FILE_APPEND);

try {
    $connector = new elFinderConnector(new elFinder($opts));
    file_put_contents('/tmp/elfinder_debug.log', "11. Connector 생성 완료\n", FILE_APPEND);
    $connector->run();
    file_put_contents('/tmp/elfinder_debug.log', "12. Connector 실행 완료\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents('/tmp/elfinder_debug.log', "❌ Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    file_put_contents('/tmp/elfinder_debug.log', "   File: " . $e->getFile() . ":" . $e->getLine() . "\n", FILE_APPEND);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>
