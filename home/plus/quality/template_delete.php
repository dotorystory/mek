<?php
include_once('./_common.php');

// 레벨 5 이상만 접근 가능
if ($member['mb_level'] < 5) {
    alert('템플릿 삭제는 회원 레벨 5 이상만 사용할 수 있습니다.', G5_URL);
}

$qt_id = isset($_GET['qt_id']) ? (int)$_GET['qt_id'] : 0;

if (!$qt_id) {
    alert('템플릿 ID가 없습니다.', './template_list.php');
}

$template = get_quality_template($qt_id);
if (!$template) {
    alert('템플릿을 찾을 수 없습니다.', './template_list.php');
}

// 템플릿 삭제 (CASCADE로 관련 항목도 함께 삭제됨)
if (delete_quality_template($qt_id)) {
    alert('템플릿이 삭제되었습니다.', './template_list.php');
} else {
    alert('템플릿 삭제에 실패했습니다.', './template_list.php');
}
?>

