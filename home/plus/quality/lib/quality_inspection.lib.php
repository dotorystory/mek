<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 품질 검사표 검사 관련 함수
 */

/**
 * 검사 결과 등록
 */
function insert_quality_result($data) {
    global $g5, $member;
    
    $qr_company = isset($data['qr_company']) ? addslashes($data['qr_company']) : '';
    $qr_division = isset($data['qr_division']) ? addslashes($data['qr_division']) : '';
    
    $sql = "INSERT INTO {$g5['quality_result_table']} SET
            qt_id = '{$data['qt_id']}',
            qr_doc_id = '{$data['qr_doc_id']}',
            qr_title = '{$data['qr_title']}',
            qr_location = '{$data['qr_location']}',
            qr_memo = '{$data['qr_memo']}',
            qr_company = '{$qr_company}',
            qr_division = '{$qr_division}',
            qr_inspection_date = '{$data['qr_inspection_date']}',
            qr_inspector = '{$member['mb_id']}',
            qr_inspector_name = '{$member['mb_name']}',
            qr_status = '{$data['qr_status']}',
            qr_created_at = NOW()";
    
    sql_query($sql);
    
    return sql_insert_id();
}

/**
 * 검사 결과 수정
 */
function update_quality_result($qr_id, $data) {
    global $g5;
    
    $qr_company = isset($data['qr_company']) ? addslashes($data['qr_company']) : '';
    $qr_division = isset($data['qr_division']) ? addslashes($data['qr_division']) : '';
    $qr_doc_id = isset($data['qr_doc_id']) ? addslashes($data['qr_doc_id']) : '';
    
    $sql = "UPDATE {$g5['quality_result_table']} SET
            qr_doc_id = '{$qr_doc_id}',
            qr_title = '{$data['qr_title']}',
            qr_location = '{$data['qr_location']}',
            qr_memo = '{$data['qr_memo']}',
            qr_company = '{$qr_company}',
            qr_division = '{$qr_division}',
            qr_inspection_date = '{$data['qr_inspection_date']}',
            qr_status = '{$data['qr_status']}',
            qr_updated_at = NOW()
            WHERE qr_id = '{$qr_id}'";
    
    return sql_query($sql);
}

/**
 * 검사 결과 조회
 */
function get_quality_result($qr_id) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_result_table']} WHERE qr_id = '{$qr_id}'";
    $row = sql_fetch($sql);
    
    return $row;
}

/**
 * 검사 결과 상세 등록
 */
function insert_quality_result_detail($data) {
    global $g5;
    
    $sql = "INSERT INTO {$g5['quality_result_detail_table']} SET
            qr_id = '{$data['qr_id']}',
            qi_id = '{$data['qi_id']}',
            qrd_result = '{$data['qrd_result']}',
            qrd_reviewer_check = '{$data['qrd_reviewer_check']}',
            qrd_note = '{$data['qrd_note']}',
            qrd_order = '{$data['qrd_order']}'";
    
    sql_query($sql);
    
    return sql_insert_id();
}

/**
 * 검사 결과 상세 수정
 */
function update_quality_result_detail($qrd_id, $data) {
    global $g5;
    
    $sql = "UPDATE {$g5['quality_result_detail_table']} SET
            qrd_result = '{$data['qrd_result']}',
            qrd_reviewer_check = '{$data['qrd_reviewer_check']}',
            qrd_note = '{$data['qrd_note']}'
            WHERE qrd_id = '{$qrd_id}'";
    
    return sql_query($sql);
}

/**
 * 검사 결과의 모든 상세 항목 조회
 */
function get_quality_result_details($qr_id) {
    global $g5;
    
    $sql = "SELECT qrd.*, qi.* 
            FROM {$g5['quality_result_detail_table']} qrd
            LEFT JOIN {$g5['quality_item_table']} qi ON qrd.qi_id = qi.qi_id
            WHERE qrd.qr_id = '{$qr_id}'
            ORDER BY qrd.qrd_order ASC, qrd.qrd_id ASC";
    
    return sql_query($sql);
}

/**
 * 검사 결과 상세 삭제
 */
function delete_quality_result_details($qr_id) {
    global $g5;
    
    $sql = "DELETE FROM {$g5['quality_result_detail_table']} WHERE qr_id = '{$qr_id}'";
    
    return sql_query($sql);
}

