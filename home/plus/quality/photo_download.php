<?php
include_once('./_common.php');

$qp_id = isset($_GET['qp_id']) ? (int)$_GET['qp_id'] : 0;

if (!$qp_id) {
    alert('사진 ID가 없습니다.', './result_list.php');
}

$photo = get_quality_photo($qp_id);
if (!$photo) {
    alert('사진을 찾을 수 없습니다.', './result_list.php');
}

// 권한 체크
$result = get_quality_result($photo['qr_id']);
if ($member['mb_level'] >= 5) {
    // 레벨 5 이상: 모든 검사/검수/결제 결과의 사진 다운로드 가능
    // 권한 체크 없음
} elseif ($member['mb_level'] >= 4) {
    // 레벨 4: 검사/검수 결과의 사진 다운로드 가능
    // 검사 완료(inspected) 또는 검수 완료(reviewed) 상태이거나 본인이 작성한 결과
    if (!in_array($result['qr_status'], array('inspected', 'reviewed')) && $result['qr_inspector'] != $member['mb_id']) {
        alert('검사 완료 또는 검수 완료된 결과의 사진만 다운로드할 수 있습니다.', './result_list.php');
    }
} else {
    // 레벨 3: 검사 결과의 사진 다운로드만 가능 (본인이 작성한 것만)
    if ($result['qr_inspector'] != $member['mb_id']) {
        alert('본인이 작성한 검사 결과의 사진만 다운로드할 수 있습니다.', './result_list.php');
    }
}

$file_path = $photo['qp_filepath'];

if (!file_exists($file_path)) {
    alert('파일을 찾을 수 없습니다.', './result_list.php');
}

// 파일 다운로드
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $photo['qp_filename'] . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: must-revalidate');
header('Pragma: public');

readfile($file_path);
exit;
?>

