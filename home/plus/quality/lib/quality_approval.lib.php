<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 품질 검사표 결제 관련 함수
 */

/**
 * 결제 이력 등록
 */
function insert_quality_approval($data) {
    global $g5, $member;
    
    $sql = "INSERT INTO {$g5['quality_approval_table']} SET
            qr_id = '{$data['qr_id']}',
            qa_step = '{$data['qa_step']}',
            qa_approver = '{$member['mb_id']}',
            qa_approver_name = '{$member['mb_name']}',
            qa_status = '{$data['qa_status']}',
            qa_comment = '{$data['qa_comment']}',
            qa_approved_at = NOW()";
    
    sql_query($sql);
    
    return sql_insert_id();
}

/**
 * 검사 결과의 결제 이력 조회
 */
function get_quality_approvals($qr_id) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_approval_table']} 
            WHERE qr_id = '{$qr_id}' 
            ORDER BY qa_approved_at ASC";
    
    return sql_query($sql);
}

/**
 * 검수 대기 목록 조회
 */
function get_quality_review_list($limit = 0, $offset = 0) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_result_table']} 
            WHERE qr_status = 'inspected'
            ORDER BY qr_created_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    
    return sql_query($sql);
}

/**
 * 최종결제 대기 목록 조회
 */
function get_quality_approval_list($limit = 0, $offset = 0) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_result_table']} 
            WHERE qr_status = 'reviewed'
            ORDER BY qr_created_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    
    return sql_query($sql);
}

/**
 * 검사 결과 상태 업데이트
 */
function update_quality_result_status($qr_id, $status, $approver_info = array()) {
    global $g5;
    
    $sql = "UPDATE {$g5['quality_result_table']} SET
            qr_status = '{$status}'";
    
    if (!empty($approver_info['reviewer'])) {
        $sql .= ", qr_reviewer = '{$approver_info['reviewer']}'";
        $sql .= ", qr_reviewer_name = '{$approver_info['reviewer_name']}'";
    }
    
    if (!empty($approver_info['final_approver'])) {
        $sql .= ", qr_final_approver = '{$approver_info['final_approver']}'";
        $sql .= ", qr_final_approver_name = '{$approver_info['final_approver_name']}'";
    }
    
    $sql .= ", qr_updated_at = NOW()
            WHERE qr_id = '{$qr_id}'";
    
    return sql_query($sql);
}

