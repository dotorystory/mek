<?php
include_once('./_common.php');

$qr_id = isset($_GET['qr_id']) ? (int)$_GET['qr_id'] : 0;

if (!$qr_id) {
    alert('결과 ID가 없습니다.', './result_list.php');
}

$result = get_quality_result($qr_id);
if (!$result) {
    alert('검사 결과를 찾을 수 없습니다.', './result_list.php');
}

// 권한 체크
if ($member['mb_level'] >= 5) {
    // 레벨 5 이상: 모든 검사/검수/결제 결과 다운로드 가능
    // 권한 체크 없음
} elseif ($member['mb_level'] >= 4) {
    // 레벨 4: 검사/검수 결과 다운로드 가능
    // 검사 완료(inspected) 또는 검수 완료(reviewed) 상태이거나 본인이 작성한 결과
    if (!in_array($result['qr_status'], array('inspected', 'reviewed')) && $result['qr_inspector'] != $member['mb_id']) {
        alert('검사 완료 또는 검수 완료된 결과만 다운로드할 수 있습니다.', './result_list.php');
    }
} else {
    // 레벨 3: 검사 결과 다운로드만 가능 (본인이 작성한 것만)
    if ($result['qr_inspector'] != $member['mb_id']) {
        alert('본인이 작성한 검사 결과만 다운로드할 수 있습니다.', './result_list.php');
    }
}

// 검사 결과 상세 조회
$details = get_quality_result_details($qr_id);

// CSV 파일명 생성
$filename_kr = '검사결과_' . preg_replace('/[^a-zA-Z0-9가-힣_]/', '_', $result['qr_title']) . '_' . date('YmdHis', strtotime($result['qr_created_at'])) . '.csv';
$filename_ascii = 'result_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $result['qr_title']) . '_' . date('YmdHis', strtotime($result['qr_created_at'])) . '.csv';

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
    $result['qr_doc_id'],
    '검사표제목',
    $result['qr_title'],
    '문서위치',
    $result['qr_location'] ?: '',
    '메모',
    $result['qr_memo'] ?: ''
);
fputcsv($output, $row1);

// 둘째 행: 회사명: / (공란) / 검사자: / (검사자 이름) / 검수자: / (검수자 이름) / 최종결제자: / (최종결제자 이름)
$row2 = array(
    '회사명:',
    $result['qr_company'] ?: '',
    '검사자:',
    $result['qr_inspector_name'] ?: '',
    '검수자:',
    $result['qr_reviewer_name'] ?: '',
    '최종결제자:',
    $result['qr_final_approver_name'] ?: ''
);
fputcsv($output, $row2);

// 셋째 행: 구분: / (공란) / 검사일: / (검사일) / 검수일: / (검수일) / 최종결제일: / (최종결제일)
$row3 = array(
    '구분:',
    $result['qr_division'] ?: '',
    '검사일:',
    $result['qr_inspection_date'] ? date('Y-m-d H:i', strtotime($result['qr_inspection_date'])) : '',
    '검수일:',
    $result['qr_review_date'] ? date('Y-m-d H:i', strtotime($result['qr_review_date'])) : '',
    '최종결제일:',
    $result['qr_approval_date'] ? date('Y-m-d H:i', strtotime($result['qr_approval_date'])) : ''
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
if (sql_num_rows($details) > 0) {
    while ($detail = sql_fetch_array($details)) {
        $row = array(
            $detail['qi_item_id'] ?: '',
            $detail['qi_procedure'] ?: '',
            $detail['qi_item'] ?: '',
            $detail['qi_method'] ?: '',
            $detail['qi_standard'] ?: '',
            $detail['qi_unit'] ?: '',
            $detail['qrd_result'] ?: '',
            $detail['qrd_reviewer_check'] == 'Y' ? '✓' : '',
            $detail['qrd_note'] ?: ''
        );
        fputcsv($output, $row);
    }
}

fclose($output);
exit;
?>

