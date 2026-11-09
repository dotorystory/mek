<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/**
 * 이메일 발송 제한 체크
 * @param string $type 메일 타입 (contact, subscribe, support_request, mail_sender)
 * @return array ['allowed' => bool, 'message' => string]
 */
function check_email_limit($type) {
    global $g5;
    
    // IP별 발송 제한 (1시간에 5회)
    $ip = $_SERVER['REMOTE_ADDR'];
    $time_limit = date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    $sql = "SELECT COUNT(*) as cnt FROM g5_mail_log 
            WHERE ml_ip = '{$ip}' 
            AND ml_type = '{$type}' 
            AND ml_datetime > '{$time_limit}'";
    $result = sql_fetch($sql);
    
    if ($result['cnt'] >= 5) {
        return [
            'allowed' => false,
            'message' => '1시간에 5회까지만 메일을 발송할 수 있습니다. 잠시 후 다시 시도해주세요.'
        ];
    }
    
    // 회원별 발송 제한 (1시간에 3회) - 로그인한 회원인 경우
    if (isset($member['mb_id']) && !empty($member['mb_id'])) {
        $mb_id = $member['mb_id'];
        $sql = "SELECT COUNT(*) as cnt FROM g5_mail_log 
                WHERE ml_mb_id = '{$mb_id}' 
                AND ml_type = '{$type}' 
                AND ml_datetime > '{$time_limit}'";
        $result = sql_fetch($sql);
        
        if ($result['cnt'] >= 3) {
            return [
                'allowed' => false,
                'message' => '회원당 1시간에 3회까지만 메일을 발송할 수 있습니다. 잠시 후 다시 시도해주세요.'
            ];
        }
    }
    
    // 이메일별 발송 제한 (1시간에 3회) - 비회원인 경우
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = get_text($_POST['email']);
        $sql = "SELECT COUNT(*) as cnt FROM g5_mail_log 
                WHERE ml_email = '{$email}' 
                AND ml_type = '{$type}' 
                AND ml_datetime > '{$time_limit}'";
        $result = sql_fetch($sql);
        
        if ($result['cnt'] >= 3) {
            return [
                'allowed' => false,
                'message' => '이메일당 1시간에 3회까지만 메일을 발송할 수 있습니다. 잠시 후 다시 시도해주세요.'
            ];
        }
    }
    
    return ['allowed' => true, 'message' => ''];
}

/**
 * 허니팟 체크 (스팸 방지)
 * @param string $field_name 허니팟 필드명
 * @return bool
 */
function check_honeypot($field_name) {
    // 허니팟 필드가 비어있어야 정상 (스팸봇은 보통 모든 필드를 채움)
    return empty($_POST[$field_name]);
}

/**
 * 이메일 발송 로그 기록
 * @param string $type 메일 타입
 * @param string $email 이메일 주소
 * @param bool $success 성공 여부
 */
function log_email_attempt($type, $email, $success) {
    global $g5;
    
    // g5_mail_log 테이블이 없으면 생성
    $sql = "CREATE TABLE IF NOT EXISTS g5_mail_log (
        ml_id int(11) NOT NULL AUTO_INCREMENT,
        ml_type varchar(50) NOT NULL,
        ml_email varchar(100) NOT NULL,
        ml_mb_id varchar(255) DEFAULT NULL,
        ml_ip varchar(45) NOT NULL,
        ml_success tinyint(1) NOT NULL DEFAULT 0,
        ml_datetime datetime NOT NULL,
        PRIMARY KEY (ml_id),
        KEY ml_type (ml_type),
        KEY ml_email (ml_email),
        KEY ml_mb_id (ml_mb_id),
        KEY ml_ip (ml_ip),
        KEY ml_datetime (ml_datetime)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    sql_query($sql);
    
    // 로그 기록
    $ip = $_SERVER['REMOTE_ADDR'];
    $success_val = $success ? 1 : 0;
    $mb_id = isset($member['mb_id']) ? $member['mb_id'] : '';
    
    $sql = "INSERT INTO g5_mail_log 
            (ml_type, ml_email, ml_mb_id, ml_ip, ml_success, ml_datetime) 
            VALUES ('{$type}', '{$email}', '{$mb_id}', '{$ip}', {$success_val}, NOW())";
    sql_query($sql);
}

/**
 * 입력 데이터 정리
 * @param string $input 입력값
 * @return string
 */
function sanitize_input($input) {
    return trim(strip_tags($input));
}

/**
 * 이메일 유효성 검증
 * @param string $email 이메일 주소
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>
