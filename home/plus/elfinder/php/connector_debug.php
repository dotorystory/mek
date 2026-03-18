<?php
// 디버깅용 파일
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== 디버깅 정보 ===<br><br>";

echo "1. 현재 경로: " . __FILE__ . "<br>";
echo "2. 현재 디렉토리: " . __DIR__ . "<br><br>";

echo "3. _common.php 경로 테스트:<br>";
$test_path = '../../_common.php';
echo "   시도: {$test_path}<br>";
echo "   실제 경로: " . realpath($test_path) . "<br>";
echo "   존재: " . (file_exists($test_path) ? 'YES' : 'NO') . "<br><br>";

// _common.php 로드
include_once('../../_common.php');

echo "4. 로그인 상태:<br>";
echo "   \$is_member: " . ($is_member ? 'TRUE' : 'FALSE') . "<br>";
echo "   \$member['mb_id']: " . ($member['mb_id'] ?? 'NULL') . "<br>";
echo "   \$member['mb_level']: " . ($member['mb_level'] ?? 'NULL') . "<br>";
echo "   \$is_admin: " . ($is_admin ?? 'NULL') . "<br><br>";

echo "5. 세션 정보:<br>";
echo "   Session ID: " . session_id() . "<br>";
echo "   Session 변수: " . (isset($_SESSION['ss_mb_id']) ? $_SESSION['ss_mb_id'] : 'NULL') . "<br><br>";

echo "6. G5 상수:<br>";
echo "   G5_PATH: " . (defined('G5_PATH') ? G5_PATH : 'NOT DEFINED') . "<br>";
echo "   G5_LIB_PATH: " . (defined('G5_LIB_PATH') ? G5_LIB_PATH : 'NOT DEFINED') . "<br>";
?>
