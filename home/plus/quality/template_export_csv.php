<?php
include_once('./_common.php');

// 레벨 3 이상만 접근 가능
if ($member['mb_level'] < 3) {
    alert('템플릿 다운로드는 회원 레벨 3 이상만 사용할 수 있습니다.', G5_URL);
}

$qt_id = isset($_GET['qt_id']) ? (int)$_GET['qt_id'] : 0;

if (!$qt_id) {
    alert('템플릿 ID가 없습니다.', './template_list.php');
}

$template = get_quality_template($qt_id);
if (!$template) {
    alert('템플릿을 찾을 수 없습니다.', './template_list.php');
}

// 검사 항목 목록 조회
$items = get_quality_items($qt_id);

// CSV 파일명 생성
$filename_kr = '템플릿_' . preg_replace('/[^a-zA-Z0-9가-힣_]/', '_', $template['qt_title']) . '_' . date('YmdHis') . '.csv';
$filename_ascii = 'template_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $template['qt_title']) . '_' . date('YmdHis') . '.csv';

// CSV 헤더 설정
header('Content-Type: text/csv; charset=UTF-8');
// 한글 파일명 지원을 위한 RFC 5987 형식 사용
header('Content-Disposition: attachment; filename="' . $filename_ascii . '"; filename*=UTF-8\'\'' . rawurlencode($filename_kr));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');

// BOM 추가 (Excel에서 한글 깨짐 방지)
echo "\xEF\xBB\xBF";

// 출력 버퍼 시작
$output = fopen('php://output', 'w');

// 첫째 행: 문서ID / (id 넘버) / 검사표제목 / (제목 내용) / 문서위치 / (문서 위치 경로) / 메모 / (메모 내용)
$row1 = array(
    '문서ID',
    $template['qt_doc_id'],
    '검사표제목',
    $template['qt_title'],
    '문서위치',
    $template['qt_location'],
    '메모',
    $template['qt_memo'] ?: ''
);
fputcsv($output, $row1);

// 둘째 행: 회사명: / (공란) / 검사자: / (공란) / 검수자: / (공란) / 최종결제자: / (공란)
$row2 = array(
    '회사명:',
    '',
    '검사자:',
    '',
    '검수자:',
    '',
    '최종결제자:',
    ''
);
fputcsv($output, $row2);

// 셋째 행: 구분: / (공란) / 검사일: / (공란) / 검수일: / (공란) / 최종결제일: / (공란)
$row3 = array(
    '구분:',
    '',
    '검사일:',
    '',
    '검수일:',
    '',
    '최종결제일:',
    ''
);
fputcsv($output, $row3);

// 네번째 행: ID, 검사절차명, 검사항목, 검사방법, 품질기준, 단위, 검사결과, 검수자확인, 비고
$row4 = array(
    'ID',
    '검사절차명',
    '검사항목',
    '검사방법',
    '품질기준',
    '단위',
    '검사결과',
    '검수자확인',
    '비고'
);
fputcsv($output, $row4);

// 다섯번째 행부터: 검사 항목 데이터
if (sql_num_rows($items) > 0) {
    while ($item = sql_fetch_array($items)) {
        $row = array(
            $item['qi_item_id'] ?: '',
            $item['qi_procedure'] ?: '',
            $item['qi_item'] ?: '',
            $item['qi_method'] ?: '',
            $item['qi_standard'] ?: '',
            $item['qi_unit'] ?: '',
            '', // 검사결과 (빈 값)
            '', // 검수자확인 (빈 값)
            ''  // 비고 (빈 값)
        );
        fputcsv($output, $row);
    }
}

fclose($output);
exit;
?>

