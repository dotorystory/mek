<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== 간단 테스트 ===<br><br>";

// 1. _common.php 로드
include_once('../../_common.php');

echo "1. 로그인 상태: " . ($is_member ? "TRUE" : "FALSE") . "<br>";
echo "2. 회원 ID: " . $member['mb_id'] . "<br>";
echo "3. 회원 레벨: " . $member['mb_level'] . "<br><br>";

// 2. file_log.lib.php 로드 테스트
echo "4. file_log.lib.php 로드 시도...<br>";
include_once(G5_LIB_PATH.'/file_log.lib.php');
echo "   ✅ 로드 성공<br><br>";

// 3. 함수 호출 테스트
echo "5. log_file_access 함수 호출 테스트...<br>";
try {
    log_file_access($member['mb_id'], 'ACCESS', 'TEST', $_SERVER['REMOTE_ADDR']);
    echo "   ✅ 함수 호출 성공<br><br>";
} catch (Exception $e) {
    echo "   ❌ 오류: " . $e->getMessage() . "<br><br>";
}

// 4. elFinder 클래스 로드 테스트
echo "6. elFinder 클래스 로드 시도...<br>";
try {
    require './elFinderConnector.class.php';
    require './elFinder.class.php';
    require './elFinderVolumeDriver.class.php';
    require './elFinderVolumeLocalFileSystem.class.php';
    echo "   ✅ 클래스 로드 성공<br><br>";
} catch (Exception $e) {
    echo "   ❌ 오류: " . $e->getMessage() . "<br><br>";
}

echo "=== 모든 테스트 완료 ===";
?>
