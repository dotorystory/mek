<?php
/**
 * elFinder Connector for Gnuboard
 *
 * 등급별 권한:
 * - 회원 2등급: 조회 불가 (접근 차단)
 * - 회원 3등급: 자신의 폴더만 열람/업로드/다운로드/수정/삭제
 * - 회원 4등급: 자신의 폴더 전체 권한, 3등급 이하 폴더는 열람/다운로드만 (쓰기/삭제 불가)
 * - 회원 5등급: 자신 + 하위 모든 등급 폴더에 대해 전체 권한 (관리자와 동일)
 *
 * @author AI Assistant
 * @version 1.1
 * @date 2026-03
 */

// 그누보드 공통 파일 로드
include_once('../../_common.php');

// 로그인 확인
if (!$is_member) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => '로그인이 필요합니다.']);
    exit;
}

// 권한 확인: 회원 3등급 미만(2등급 이하)은 접근 불가, 관리자(super)는 통과
if ($member['mb_level'] < 3 && $is_admin != 'super') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => '파일 관리자는 회원 3등급 이상만 이용 가능합니다.']);
    exit;
}

// 로그 라이브러리 로드
if (file_exists(G5_LIB_PATH.'/file_log.lib.php')) {
    include_once(G5_LIB_PATH.'/file_log.lib.php');
} else {
    function log_file_upload($mb_id, $filename, $filepath, $filesize, $ip) { return true; }
    function log_file_download($mb_id, $filename, $filepath, $filesize, $ip) { return true; }
    function log_file_delete($mb_id, $filename, $filepath, $filesize, $ip) { return true; }
}

require './elFinderConnector.class.php';
require './elFinder.class.php';
require './elFinderVolumeDriver.class.php';
require './elFinderVolumeLocalFileSystem.class.php';

$upload_base = '/var/www/html/mekeng.com/upload';
$current_mb_id = $member['mb_id'];
$current_level = (int)$member['mb_level'];
$is_super = ($is_admin == 'super');

// 본인 폴더 경로 및 생성
$my_path = $upload_base . '/' . $current_mb_id;
if (!is_dir($my_path)) {
    @mkdir($my_path, 0775, true);
    @chmod($my_path, 0775);
}

/**
 * 등급별 하위 회원 목록
 * - 4등급: 3등급 이하 (mb_level <= 3)
 * - 5등급 또는 super: 5등급 미만 전체 (mb_level < 5)
 */
$subordinate_members = array();
if ($current_level >= 4 || $is_super) {
    $level_condition = ($current_level >= 5 || $is_super) ? " AND mb_level < 5 " : " AND mb_level <= 3 ";
    $sql = "SELECT mb_id, mb_name, mb_level FROM {$g5['member_table']} 
            WHERE mb_id != '" . sql_escape_string($current_mb_id) . "' " . $level_condition . " 
            ORDER BY mb_level DESC, mb_id ASC";
    $res = sql_query($sql);
    while ($row = sql_fetch_array($res)) {
        $subordinate_members[] = $row;
    }
}

// 4등급은 하위 폴더 읽기 전용, 5등급/super는 하위 폴더도 전체 권한
$subordinate_readonly = ($current_level == 4 && !$is_super);

// 루트 목록 구성
$roots = array();

// 1) 본인 폴더 (항상 전체 권한)
$roots[] = array(
    'driver'        => 'LocalFileSystem',
    'path'          => $my_path,
    'URL'           => '/upload/' . $current_mb_id . '/',
    'alias'         => ($member['mb_name'] ? $member['mb_name'] . '님의 파일' : $current_mb_id),
    'uploadMaxSize' => '500M',
    'tmbPath'       => $my_path . '/.tmb',
    'attributes'    => array(
        array('pattern' => '/\.tmb/', 'read' => false, 'write' => false, 'locked' => true, 'hidden' => true)
    )
);

// 2) 하위 등급 회원 폴더 (4등급: 읽기전용, 5등급/super: 전체 권한)
foreach ($subordinate_members as $sub) {
    $sub_path = $upload_base . '/' . $sub['mb_id'];
    if (!is_dir($sub_path)) {
        @mkdir($sub_path, 0775, true);
        @chmod($sub_path, 0775);
    }
    $alias = ($sub['mb_name'] ? $sub['mb_name'] . '님의 파일' : $sub['mb_id']) . ' (Lv.' . $sub['mb_level'] . ')';
    $root_config = array(
        'driver'        => 'LocalFileSystem',
        'path'          => $sub_path,
        'URL'           => '/upload/' . $sub['mb_id'] . '/',
        'alias'         => $alias,
        'uploadMaxSize' => '500M',
        'tmbPath'       => $sub_path . '/.tmb',
        'attributes'    => array(
            array('pattern' => '/\.tmb/', 'read' => false, 'write' => false, 'locked' => true, 'hidden' => true)
        )
    );
    if ($subordinate_readonly) {
        $root_config['attributes'][] = array('pattern' => '/.*/', 'read' => true, 'write' => false, 'locked' => false);
    }
    $roots[] = $root_config;
}

/**
 * access 콜백: super는 모든 권한, 그 외는 volume 기본 권한(읽기전용 루트는 volume에서 write=false 처리됨)
 */
function access($attr, $path, $data, $volume) {
    global $is_admin;
    if ($is_admin == 'super') {
        return true;
    }
    if ($attr === 'read' || $attr === 'download') {
        return true;
    }
    return $volume->isAllowed($attr, $path);
}

$opts = array(
    'roots' => $roots,
    'debug' => false
);

$connector = new elFinderConnector(new elFinder($opts));
$connector->run();
