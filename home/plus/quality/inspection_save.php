<?php
include_once('./_common.php');

$qt_id = isset($_POST['qt_id']) ? (int)$_POST['qt_id'] : 0;
$qr_id = isset($_POST['qr_id']) ? (int)$_POST['qr_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'draft';

if (!$qt_id) {
    alert('템플릿 ID가 없습니다.', './inspection.php');
}

$template = get_quality_template($qt_id);
if (!$template) {
    alert('템플릿을 찾을 수 없습니다.', './inspection.php');
}

// 기존 검사 결과 수정 모드
if ($qr_id) {
    $existing_result = get_quality_result($qr_id);
    if (!$existing_result || $existing_result['qr_status'] != 'draft' || ($member['mb_level'] < 8 && $existing_result['qr_inspector'] != $member['mb_id'])) {
        alert('수정할 수 없는 검사 결과입니다.', './result_list.php');
    }
    // 기존 상세 항목 삭제
    delete_quality_result_details($qr_id);
    // 기존 사진 삭제 (파일은 유지, DB만 삭제)
    $sql = "DELETE FROM {$g5['quality_photo_table']} WHERE qr_id = '{$qr_id}'";
    sql_query($sql);
}

// 검사 정보
$qr_doc_id = isset($_POST['qr_doc_id']) ? trim($_POST['qr_doc_id']) : ($qr_id ? $existing_result['qr_doc_id'] : ($template['qt_doc_id'] . '_' . date('YmdHis')));
$qr_title = isset($_POST['qr_title']) ? trim($_POST['qr_title']) : $template['qt_title'];
$qr_location = isset($_POST['qr_location']) ? trim($_POST['qr_location']) : $template['qt_location'];
$qr_memo = isset($_POST['qr_memo']) ? trim($_POST['qr_memo']) : '';
$qr_company = isset($_POST['qr_company']) ? trim($_POST['qr_company']) : '';
$qr_division = isset($_POST['qr_division']) ? trim($_POST['qr_division']) : '';
$qr_inspection_date = isset($_POST['qr_inspection_date']) ? $_POST['qr_inspection_date'] : date('Y-m-d H:i:s');
$qr_status = $action == 'submit' ? 'inspected' : 'draft';

if ($qr_id) {
    // 기존 결과 수정
    $data = array(
        'qr_doc_id' => $qr_doc_id,
        'qr_title' => $qr_title,
        'qr_location' => $qr_location,
        'qr_memo' => $qr_memo,
        'qr_company' => $qr_company,
        'qr_division' => $qr_division,
        'qr_inspection_date' => $qr_inspection_date,
        'qr_status' => $qr_status
    );
    update_quality_result($qr_id, $data);
} else {
    // 신규 등록
    $data = array(
        'qt_id' => $qt_id,
        'qr_doc_id' => $qr_doc_id,
        'qr_title' => $qr_title,
        'qr_location' => $qr_location,
        'qr_memo' => $qr_memo,
        'qr_company' => $qr_company,
        'qr_division' => $qr_division,
        'qr_inspection_date' => $qr_inspection_date,
        'qr_status' => $qr_status
    );
    $qr_id = insert_quality_result($data);
    
    if (!$qr_id) {
        alert('검사 결과 저장에 실패했습니다.', './inspection.php?qt_id=' . $qt_id);
    }
}

// 검사 항목별 결과 저장
$items = get_quality_items($qt_id);
while ($item = sql_fetch_array($items)) {
    $qi_id = $item['qi_id'];
    
    $qrd_result = isset($_POST['result'][$qi_id]) ? trim($_POST['result'][$qi_id]) : '';
    $qrd_reviewer_check = isset($_POST['reviewer_check'][$qi_id]) ? 'Y' : 'N';
    $qrd_note = isset($_POST['note'][$qi_id]) ? trim($_POST['note'][$qi_id]) : '';
    $qrd_order = isset($_POST['item_order'][$qi_id]) ? (int)$_POST['item_order'][$qi_id] : 0;
    
    // 검사 결과 상세 등록
    $detail_data = array(
        'qr_id' => $qr_id,
        'qi_id' => $qi_id,
        'qrd_result' => $qrd_result,
        'qrd_reviewer_check' => $qrd_reviewer_check,
        'qrd_note' => $qrd_note,
        'qrd_order' => $qrd_order
    );
    
    $qrd_id = insert_quality_result_detail($detail_data);
    
    // 사진 업로드 처리
    // $_FILES['photo']['name'][$qi_id] 형태로 접근해야 함
    if (isset($_FILES['photo']['name'][$qi_id]) && is_array($_FILES['photo']['name'][$qi_id])) {
        $photo_count = 0;
        foreach ($_FILES['photo']['name'][$qi_id] as $key => $filename) {
            if ($photo_count >= 5) break; // 최대 5장
            
            if ($_FILES['photo']['error'][$qi_id][$key] == UPLOAD_ERR_OK) {
                $file = array(
                    'name' => $_FILES['photo']['name'][$qi_id][$key],
                    'type' => $_FILES['photo']['type'][$qi_id][$key],
                    'tmp_name' => $_FILES['photo']['tmp_name'][$qi_id][$key],
                    'size' => $_FILES['photo']['size'][$qi_id][$key]
                );
                
                // 파일 검증
                $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
                if (!in_array($file['type'], $allowed_types)) {
                    continue;
                }
                
                if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                    continue;
                }
                
                // 파일 정보
                $image_info = @getimagesize($file['tmp_name']);
                if (!$image_info) {
                    continue;
                }
                
                // 파일명 생성
                $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $new_filename = $qr_id . '_' . $qi_id . '_' . $photo_count . '_' . time() . '.' . $file_extension;
                
                // 원본 저장 경로
                $original_dir = G5_QUALITY_UPLOAD_PATH . '/original/';
                if (!is_dir($original_dir)) {
                    @mkdir($original_dir, 0755, true);
                    @chown($original_dir, 'apache');
                    @chgrp($original_dir, 'user');
                }
                $original_path = $original_dir . $new_filename;
                
                // 파일 이동
                if (move_uploaded_file($file['tmp_name'], $original_path)) {
                    // 파일 권한 설정
                    @chmod($original_path, 0644);
                    @chown($original_path, 'apache');
                    @chgrp($original_path, 'user');
                    
                    // 이미지 리사이징 및 썸네일 생성
                    $resize_result = resize_quality_photo($original_path);
                    
                    if ($resize_result && isset($resize_result['width']) && isset($resize_result['height'])) {
                        // 썸네일 파일 권한 설정
                        if (!empty($resize_result['thumbnail_path']) && file_exists($resize_result['thumbnail_path'])) {
                            @chmod($resize_result['thumbnail_path'], 0644);
                            @chown($resize_result['thumbnail_path'], 'apache');
                            @chgrp($resize_result['thumbnail_path'], 'user');
                        }
                        
                        // 사진 정보 저장
                        $photo_data = array(
                            'qrd_id' => $qrd_id,
                            'qr_id' => $qr_id,
                            'qi_id' => $qi_id,
                            'qp_filename' => $file['name'],
                            'qp_filepath' => $original_path,
                            'qp_filesize' => $file['size'],
                            'qp_width' => $resize_result['width'],
                            'qp_height' => $resize_result['height'],
                            'qp_thumbnail' => isset($resize_result['thumbnail_path']) ? $resize_result['thumbnail_path'] : '',
                            'qp_order' => $photo_count
                        );
                        
                        insert_quality_photo($photo_data);
                        $photo_count++;
                    } else {
                        // 리사이징 실패 시에도 원본 파일은 유지하고 저장
                        $photo_data = array(
                            'qrd_id' => $qrd_id,
                            'qr_id' => $qr_id,
                            'qi_id' => $qi_id,
                            'qp_filename' => $file['name'],
                            'qp_filepath' => $original_path,
                            'qp_filesize' => $file['size'],
                            'qp_width' => $image_info[0],
                            'qp_height' => $image_info[1],
                            'qp_thumbnail' => '',
                            'qp_order' => $photo_count
                        );
                        
                        insert_quality_photo($photo_data);
                        $photo_count++;
                    }
                }
            }
        }
    }
}

if ($action == 'submit') {
    alert('검사 결과가 제출되었습니다.', './result_list.php');
} else {
    alert('검사 결과가 임시 저장되었습니다.', './result_list.php');
}
?>

