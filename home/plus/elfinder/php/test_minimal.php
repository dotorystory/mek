<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Minimal Connector Test ===<br><br>";

// _common.php 로드
include_once('../../_common.php');

echo "1. 로그인: " . ($is_member ? "YES ✅" : "NO ❌") . "<br>";
echo "2. 회원 ID: " . $member['mb_id'] . "<br>";
echo "3. 회원 레벨: " . $member['mb_level'] . "<br><br>";

// upload 폴더 경로
$upload_path = '/var/www/html/mekeng.com/upload/' . $member['mb_id'];
echo "4. 업로드 경로: {$upload_path}<br>";
echo "5. 폴더 존재: " . (is_dir($upload_path) ? "YES ✅" : "NO ❌") . "<br>";
echo "6. 폴더 쓰기 가능: " . (is_writable($upload_path) ? "YES ✅" : "NO ❌") . "<br><br>";

// 폴더 생성 테스트
if (!is_dir($upload_path)) {
    echo "7. 폴더 생성 시도...<br>";
    if (@mkdir($upload_path, 0775, true)) {
        echo "   ✅ 생성 성공<br>";
    } else {
        echo "   ❌ 생성 실패<br>";
    }
    @chmod($upload_path, 0775);
}

echo "8. 최종 권한: " . substr(sprintf('%o', fileperms($upload_path)), -4) . "<br><br>";

// file_log.lib.php 로드 테스트
echo "9. file_log.lib.php 로드...<br>";
if (file_exists(G5_LIB_PATH.'/file_log.lib.php')) {
    include_once(G5_LIB_PATH.'/file_log.lib.php');
    echo "   ✅ 로드 성공<br>";
    echo "   function_exists('log_file_upload'): " . (function_exists('log_file_upload') ? "YES" : "NO") . "<br>";
} else {
    echo "   ❌ 파일 없음: " . G5_LIB_PATH . "/file_log.lib.php<br>";
}

// elFinder 클래스 로드 테스트
echo "<br>10. elFinder 클래스 로드...<br>";
try {
    require './elFinderConnector.class.php';
    echo "   ✅ elFinderConnector.class.php<br>";
    
    require './elFinder.class.php';
    echo "   ✅ elFinder.class.php<br>";
    
    require './elFinderVolumeDriver.class.php';
    echo "   ✅ elFinderVolumeDriver.class.php<br>";
    
    require './elFinderVolumeLocalFileSystem.class.php';
    echo "   ✅ elFinderVolumeLocalFileSystem.class.php<br>";
    
    echo "   <strong>✅ 모든 클래스 로드 성공!</strong><br>";
} catch (Exception $e) {
    echo "   <strong>❌ 오류: " . $e->getMessage() . "</strong><br>";
} catch (Error $e) {
    echo "   <strong>❌ Error: " . $e->getMessage() . "</strong><br>";
}

echo "<br>11. elFinder 인스턴스 생성 테스트...<br>";
try {
    $opts = array(
        'roots' => array(
            array(
                'driver' => 'LocalFileSystem',
                'path'   => $upload_path,
                'URL'    => '/upload/' . $member['mb_id'] . '/',
            )
        ),
        'debug' => false
    );
    
    $elf = new elFinder($opts);
    echo "   ✅ elFinder 인스턴스 생성 성공!<br>";
    
    $connector = new elFinderConnector($elf);
    echo "   ✅ Connector 생성 성공!<br>";
    
} catch (Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "<br>";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "   ❌ Error: " . $e->getMessage() . "<br>";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}

echo "<br>=== 테스트 완료 ===";
?>
