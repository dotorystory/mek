<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/**
 * 메일 발송 로그 테이블 생성 및 관리
 */

/**
 * 메일 발송 로그 테이블 생성
 */
function create_mail_send_log_table() {
    global $g5;
    
    $sql = "CREATE TABLE IF NOT EXISTS g5_mail_send_log (
        msl_id int(11) NOT NULL AUTO_INCREMENT,
        msl_email varchar(255) NOT NULL COMMENT '수신자 이메일',
        msl_send_type varchar(20) NOT NULL COMMENT '발송 유형: direct, csv, member, subscribe',
        msl_subject varchar(255) DEFAULT NULL COMMENT '메일 제목',
        msl_success tinyint(1) NOT NULL DEFAULT 0 COMMENT '발송 성공 여부',
        msl_send_date datetime NOT NULL COMMENT '발송일시',
        msl_unsubscribe tinyint(1) NOT NULL DEFAULT 0 COMMENT '수신 거부 여부',
        msl_unsubscribe_date datetime DEFAULT NULL COMMENT '수신 거부일시',
        msl_mb_id varchar(255) DEFAULT NULL COMMENT '회원 ID (회원인 경우)',
        msl_ip varchar(45) DEFAULT NULL COMMENT '발송자 IP',
        msl_error_message text DEFAULT NULL COMMENT '오류 메시지 (실패한 경우)',
        msl_created_at datetime NOT NULL COMMENT '생성일시',
        msl_updated_at datetime DEFAULT NULL COMMENT '수정일시',
        PRIMARY KEY (msl_id),
        KEY idx_email (msl_email),
        KEY idx_send_type (msl_send_type),
        KEY idx_send_date (msl_send_date),
        KEY idx_unsubscribe (msl_unsubscribe),
        KEY idx_mb_id (msl_mb_id),
        KEY idx_success (msl_success)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='메일 발송 로그 테이블'";
    
    sql_query($sql);
}

/**
 * 메일 발송 로그 기록
 * @param string $email 수신자 이메일
 * @param string $send_type 발송 유형 (direct, csv, member, subscribe)
 * @param string $subject 메일 제목
 * @param bool $success 발송 성공 여부
 * @param string $error_message 오류 메시지 (실패한 경우)
 * @return int|false 삽입된 레코드 ID 또는 false
 */
function log_mail_send($email, $send_type, $subject = '', $success = true, $error_message = '') {
    global $g5, $member;
    
    // 테이블 생성 확인
    create_mail_send_log_table();
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    $email_escaped = sql_escape_string($email);
    
    // 회원 ID 확인 (이메일로 회원 찾기)
    $mb_id = null;
    $member_check = sql_fetch("SELECT mb_id FROM {$g5['member_table']} WHERE mb_email = '{$email_escaped}' LIMIT 1");
    if ($member_check) {
        $mb_id = $member_check['mb_id'];
    }
    
    // 직접 입력(direct) 또는 CSV(csv) 발송 시 g5_subscribe 테이블에 자동 등록
    if (in_array($send_type, array('direct', 'csv'))) {
        // g5_subscribe 테이블에 이미 있는지 확인
        $subscribe_check = sql_fetch("SELECT sb_id, sb_subscribe FROM g5_subscribe WHERE sb_email = '{$email_escaped}' LIMIT 1");
        
        if ($subscribe_check) {
            // 이미 있는 경우, 수신 거부 상태가 아니면 수신 허용 상태로 유지
            if ($subscribe_check['sb_subscribe'] == 0) {
                // 수신 거부 상태였던 경우, 수신 허용으로 변경 (최초 발송 시 수신 허용)
                sql_query("UPDATE g5_subscribe SET sb_subscribe = 1, sb_updatedate = '".G5_TIME_YMDHIS."' WHERE sb_id = '{$subscribe_check['sb_id']}'");
            }
        } else {
            // 없는 경우 새로 추가 (수신 허용 상태로)
            sql_query("INSERT INTO g5_subscribe (sb_email, sb_subscribe, sb_regdate, sb_updatedate) 
                      VALUES ('{$email_escaped}', 1, '".G5_TIME_YMDHIS."', '".G5_TIME_YMDHIS."')");
        }
        
        // 회원인 경우 mb_mailling도 업데이트
        if ($mb_id) {
            sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 1 WHERE mb_id = '".sql_escape_string($mb_id)."'");
        }
    }
    
    // 발송자 정보
    $sender_mb_id = isset($member['mb_id']) && !empty($member['mb_id']) ? sql_escape_string($member['mb_id']) : null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $send_type_escaped = sql_escape_string($send_type);
    $subject_escaped = sql_escape_string($subject);
    $error_escaped = sql_escape_string($error_message);
    $ip_escaped = sql_escape_string($ip);
    $success_val = $success ? 1 : 0;
    
    $sql = "INSERT INTO g5_mail_send_log 
            (msl_email, msl_send_type, msl_subject, msl_success, msl_send_date, 
             msl_mb_id, msl_ip, msl_error_message, msl_created_at, msl_updated_at) 
            VALUES ('{$email_escaped}', '{$send_type_escaped}', '{$subject_escaped}', {$success_val}, NOW(),
                    " . ($mb_id ? "'".sql_escape_string($mb_id)."'" : "NULL") . ",
                    '{$ip_escaped}', " . ($error_escaped ? "'{$error_escaped}'" : "NULL") . ", NOW(), NOW())";
    
    sql_query($sql);
    
    return sql_insert_id();
}

/**
 * 수신 거부 처리
 * @param string $email 수신자 이메일
 * @return bool 성공 여부
 */
function unsubscribe_email($email) {
    global $g5;
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    $email_escaped = sql_escape_string($email);
    
    // 메일 발송 로그에서 수신 거부 업데이트
    $sql = "UPDATE g5_mail_send_log 
            SET msl_unsubscribe = 1, 
                msl_unsubscribe_date = NOW(), 
                msl_updated_at = NOW() 
            WHERE msl_email = '{$email_escaped}' 
            AND msl_unsubscribe = 0";
    sql_query($sql);
    
    // g5_subscribe 테이블도 업데이트 (있는 경우)
    $subscribe = sql_fetch("SELECT sb_id FROM g5_subscribe WHERE sb_email = '{$email_escaped}' LIMIT 1");
    if ($subscribe) {
        sql_query("UPDATE g5_subscribe SET sb_subscribe = 0, sb_updatedate = '".G5_TIME_YMDHIS."' WHERE sb_id = '{$subscribe['sb_id']}'");
    }
    
    // 회원인 경우 mb_mailling도 업데이트
    $member_check = sql_fetch("SELECT mb_id FROM {$g5['member_table']} WHERE mb_email = '{$email_escaped}' LIMIT 1");
    if ($member_check) {
        sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 0 WHERE mb_id = '{$member_check['mb_id']}'");
    }
    
    return true;
}

/**
 * 수신 거부 여부 확인
 * @param string $email 수신자 이메일
 * @return bool 수신 거부 여부
 */
function is_unsubscribed($email) {
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    $email_escaped = sql_escape_string($email);
    
    // 메일 발송 로그에서 확인
    $result = sql_fetch("SELECT msl_unsubscribe FROM g5_mail_send_log 
                         WHERE msl_email = '{$email_escaped}' 
                         AND msl_unsubscribe = 1 
                         ORDER BY msl_unsubscribe_date DESC 
                         LIMIT 1");
    
    if ($result && $result['msl_unsubscribe'] == 1) {
        return true;
    }
    
    // g5_subscribe 테이블에서도 확인
    $subscribe = sql_fetch("SELECT sb_subscribe FROM g5_subscribe WHERE sb_email = '{$email_escaped}' LIMIT 1");
    if ($subscribe && $subscribe['sb_subscribe'] == 0) {
        return true;
    }
    
    return false;
}

/**
 * 발송 이력 조회
 * @param array $options 조회 옵션 (email, send_type, success, date_from, date_to, limit, offset)
 * @return array 발송 이력 배열
 */
function get_mail_send_logs($options = array()) {
    $email = isset($options['email']) ? sql_escape_string($options['email']) : '';
    $send_type = isset($options['send_type']) ? sql_escape_string($options['send_type']) : '';
    $success = isset($options['success']) ? intval($options['success']) : -1;
    $date_from = isset($options['date_from']) ? sql_escape_string($options['date_from']) : '';
    $date_to = isset($options['date_to']) ? sql_escape_string($options['date_to']) : '';
    $limit = isset($options['limit']) ? intval($options['limit']) : 100;
    $offset = isset($options['offset']) ? intval($options['offset']) : 0;
    
    $where = array();
    
    if (!empty($email)) {
        $where[] = "msl_email = '{$email}'";
    }
    
    if (!empty($send_type)) {
        $where[] = "msl_send_type = '{$send_type}'";
    }
    
    if ($success >= 0) {
        $where[] = "msl_success = {$success}";
    }
    
    if (!empty($date_from)) {
        $where[] = "msl_send_date >= '{$date_from}'";
    }
    
    if (!empty($date_to)) {
        $where[] = "msl_send_date <= '{$date_to} 23:59:59'";
    }
    
    $where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    $sql = "SELECT * FROM g5_mail_send_log 
            {$where_sql}
            ORDER BY msl_send_date DESC 
            LIMIT {$limit} OFFSET {$offset}";
    
    $result = sql_query($sql);
    $logs = array();
    
    while ($row = sql_fetch_array($result)) {
        $logs[] = $row;
    }
    
    return $logs;
}

/**
 * 발송 통계 조회
 * @param array $options 조회 옵션 (date_from, date_to, send_type)
 * @return array 통계 배열
 */
function get_mail_send_stats($options = array()) {
    $date_from = isset($options['date_from']) ? sql_escape_string($options['date_from']) : '';
    $date_to = isset($options['date_to']) ? sql_escape_string($options['date_to']) : '';
    $send_type = isset($options['send_type']) ? sql_escape_string($options['send_type']) : '';
    
    $where = array();
    
    if (!empty($date_from)) {
        $where[] = "msl_send_date >= '{$date_from}'";
    }
    
    if (!empty($date_to)) {
        $where[] = "msl_send_date <= '{$date_to} 23:59:59'";
    }
    
    if (!empty($send_type)) {
        $where[] = "msl_send_type = '{$send_type}'";
    }
    
    $where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN msl_success = 1 THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN msl_success = 0 THEN 1 ELSE 0 END) as fail,
                SUM(CASE WHEN msl_unsubscribe = 1 THEN 1 ELSE 0 END) as unsubscribe,
                COUNT(DISTINCT msl_email) as unique_emails
            FROM g5_mail_send_log 
            {$where_sql}";
    
    return sql_fetch($sql);
}

/**
 * 영구 실패(반송)된 이메일 주소 목록 조회 — 재발송 제외용
 * msl_success=0 이면서 오류 메시지가 User unknown, 550, 505 등 영구 실패 패턴인 주소
 * @param int $days 최근 N일 이내 실패 건만 (기본 90일)
 * @return array 이메일 주소 배열
 */
function get_bounced_emails($days = 90) {
    global $g5;
    create_mail_send_log_table();
    $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));
    $sql = "SELECT DISTINCT msl_email FROM g5_mail_send_log 
            WHERE msl_success = 0 
            AND msl_send_date >= '{$since}'
            AND (
                msl_error_message LIKE '%User unknown%'
                OR msl_error_message LIKE '%550%'
                OR msl_error_message LIKE '%505%'
                OR msl_error_message LIKE '%unknown user%'
                OR msl_error_message LIKE '%Recipient address rejected%'
                OR msl_error_message LIKE '%this account is unknown%'
            )";
    $result = sql_query($sql);
    $emails = array();
    while ($row = sql_fetch_array($result)) {
        if (!empty($row['msl_email']) && filter_var($row['msl_email'], FILTER_VALIDATE_EMAIL)) {
            $emails[] = $row['msl_email'];
        }
    }
    return $emails;
}
?>

