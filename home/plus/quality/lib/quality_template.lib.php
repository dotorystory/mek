<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 품질 검사표 템플릿 관련 함수
 */

/**
 * 템플릿 목록 조회
 */
function get_quality_templates($status = 'active', $limit = 0, $offset = 0) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_template_table']} WHERE qt_status = '{$status}' ORDER BY qt_created_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    
    return sql_query($sql);
}

/**
 * 템플릿 정보 조회
 */
function get_quality_template($qt_id) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_template_table']} WHERE qt_id = '{$qt_id}'";
    $row = sql_fetch($sql);
    
    return $row;
}

/**
 * 템플릿 등록
 */
function insert_quality_template($data) {
    global $g5;
    
    $qt_filepath = isset($data['qt_filepath']) ? addslashes($data['qt_filepath']) : '';
    $qt_filename = isset($data['qt_filename']) ? addslashes($data['qt_filename']) : '';
    
    $sql = "INSERT INTO {$g5['quality_template_table']} SET
            qt_doc_id = '{$data['qt_doc_id']}',
            qt_title = '{$data['qt_title']}',
            qt_location = '{$data['qt_location']}',
            qt_memo = '{$data['qt_memo']}',
            qt_filepath = '{$qt_filepath}',
            qt_filename = '{$qt_filename}',
            qt_status = '{$data['qt_status']}',
            qt_created_at = NOW()";
    
    sql_query($sql);
    
    return sql_insert_id();
}

/**
 * 템플릿 수정
 */
function update_quality_template($qt_id, $data) {
    global $g5;
    
    $qt_filepath = isset($data['qt_filepath']) ? addslashes($data['qt_filepath']) : '';
    $qt_filename = isset($data['qt_filename']) ? addslashes($data['qt_filename']) : '';
    
    $sql = "UPDATE {$g5['quality_template_table']} SET
            qt_title = '{$data['qt_title']}',
            qt_location = '{$data['qt_location']}',
            qt_memo = '{$data['qt_memo']}',
            qt_filepath = '{$qt_filepath}',
            qt_filename = '{$qt_filename}',
            qt_status = '{$data['qt_status']}',
            qt_updated_at = NOW()
            WHERE qt_id = '{$qt_id}'";
    
    return sql_query($sql);
}

/**
 * 템플릿 삭제
 */
function delete_quality_template($qt_id) {
    global $g5;
    
    // 템플릿 삭제 시 관련 항목도 함께 삭제됨 (CASCADE)
    $sql = "DELETE FROM {$g5['quality_template_table']} WHERE qt_id = '{$qt_id}'";
    
    return sql_query($sql);
}

/**
 * 템플릿의 검사 항목 목록 조회
 */
function get_quality_items($qt_id) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_item_table']} WHERE qt_id = '{$qt_id}' ORDER BY qi_order ASC, qi_id ASC";
    
    return sql_query($sql);
}

/**
 * 검사 항목 등록
 */
function insert_quality_item($data) {
    global $g5;
    
    $sql = "INSERT INTO {$g5['quality_item_table']} SET
            qt_id = '{$data['qt_id']}',
            qi_item_id = '{$data['qi_item_id']}',
            qi_procedure = '{$data['qi_procedure']}',
            qi_item = '{$data['qi_item']}',
            qi_method = '{$data['qi_method']}',
            qi_standard = '{$data['qi_standard']}',
            qi_unit = '{$data['qi_unit']}',
            qi_order = '{$data['qi_order']}'";
    
    sql_query($sql);
    
    return sql_insert_id();
}

/**
 * 템플릿의 모든 검사 항목 삭제
 */
function delete_quality_items($qt_id) {
    global $g5;
    
    $sql = "DELETE FROM {$g5['quality_item_table']} WHERE qt_id = '{$qt_id}'";
    
    return sql_query($sql);
}

