<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');
include_once(G5_PATH.'/plus/mail_log_manager.php'); // 메일 발송 로그 관리

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 관리자 및 회원등급 5등급 이상 체크
if (!$is_admin && $member['mb_level'] < 5) {
    $return_url = urlencode(G5_URL.'/plus/mailer/');
    goto_url(G5_BBS_URL.'/login.php?url='.$return_url);
    exit;
}

// CSV 다운로드 처리
if (isset($_GET['action']) && $_GET['action'] === 'download_csv') {
    // 출력 버퍼 정리 (이전 출력 제거)
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // 날짜 필터
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d', strtotime('-30 days'));
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
    $send_type = isset($_GET['send_type']) ? $_GET['send_type'] : '';
    $success_filter = isset($_GET['success']) ? intval($_GET['success']) : -1;
    
    // 로그 조회
    $options = array(
        'date_from' => $date_from . ' 00:00:00',
        'date_to' => $date_to . ' 23:59:59',
        'limit' => 10000 // 최대 1만 건
    );
    
    if (!empty($send_type)) {
        $options['send_type'] = $send_type;
    }
    
    if ($success_filter >= 0) {
        $options['success'] = $success_filter;
    }
    
    $logs = array();
    if (function_exists('get_mail_send_logs')) {
        $logs = get_mail_send_logs($options);
    }
    
    // CSV 파일 생성
    $filename = 'mail_send_log_' . date('Ymd_His') . '.csv';
    
    // 헤더 설정 (UTF-8 BOM 포함)
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');
    
    // UTF-8 BOM 출력
    echo "\xEF\xBB\xBF";
    
    // CSV 필드 이스케이프 함수
    function csv_escape($field) {
        if (is_null($field)) {
            return '';
        }
        // 큰따옴표가 있으면 이스케이프하고 전체를 큰따옴표로 감싸기
        if (strpos($field, '"') !== false || strpos($field, ',') !== false || strpos($field, "\n") !== false) {
            return '"' . str_replace('"', '""', $field) . '"';
        }
        return $field;
    }
    
    // CSV 헤더
    $headers = array(
        '번호',
        '이메일 주소',
        '발송 유형',
        '메일 제목',
        '발송 성공 여부',
        '발송일시',
        '수신 거부 여부',
        '수신 거부일시',
        '회원 ID',
        '발송자 IP',
        '오류 메시지'
    );
    
    // 헤더 출력 (직접 CSV 형식으로)
    $header_line = '';
    foreach ($headers as $i => $header) {
        if ($i > 0) $header_line .= ',';
        $header_line .= csv_escape($header);
    }
    echo $header_line . "\n";
    
    // 데이터 출력
    $type_names = array(
        'direct' => '직접입력',
        'csv' => 'CSV',
        'member' => '회원',
        'subscribe' => '구독자'
    );
    
    foreach ($logs as $index => $log) {
        $row_data = array(
            $index + 1,
            $log['msl_email'] ?? '',
            $type_names[$log['msl_send_type'] ?? ''] ?? ($log['msl_send_type'] ?? ''),
            $log['msl_subject'] ?? '',
            ($log['msl_success'] ?? 0) == 1 ? '성공' : '실패',
            $log['msl_send_date'] ?? '',
            ($log['msl_unsubscribe'] ?? 0) == 1 ? '거부' : '허용',
            $log['msl_unsubscribe_date'] ?? '',
            $log['msl_mb_id'] ?? '',
            $log['msl_ip'] ?? '',
            $log['msl_error_message'] ?? ''
        );
        
        // CSV 행 출력 (직접 CSV 형식으로)
        $row_line = '';
        foreach ($row_data as $i => $field) {
            if ($i > 0) $row_line .= ',';
            $row_line .= csv_escape($field);
        }
        echo $row_line . "\n";
    }
    
    exit;
}

// 수신 거부 처리
if (isset($_GET['action']) && $_GET['action'] === 'unsubscribe') {
    $key = $_GET['key'] ?? '';
    $email = $_GET['email'] ?? '';
    
    if (empty($key) || empty($email)) {
        alert('잘못된 요청입니다.', './');
        exit;
    }
    
    // 키 검증 (mail_sender.php의 generate_unsubscribe_key와 호환)
    $decoded = base64_decode($key);
    if ($decoded) {
        $parts = explode('|', $decoded);
        if (isset($parts[0]) && $parts[0] === $email) {
            // 수신 거부 처리
            if (function_exists('unsubscribe_email')) {
                if (unsubscribe_email($email)) {
                    alert('메일 수신이 거부되었습니다. 앞으로 발송되지 않습니다.', './');
                } else {
                    alert('수신 거부 처리 중 오류가 발생했습니다.', './');
                }
            } else {
                alert('수신 거부 기능을 사용할 수 없습니다.', './');
            }
        } else {
            alert('잘못된 구독 취소 링크입니다.', './');
        }
    } else {
        alert('잘못된 구독 취소 링크입니다.', './');
    }
    exit;
}

// 테스트 메일 발송
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_mail') {
    $test_email = sql_escape_string(trim($_POST['test_email'] ?? ''));
    
    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        alert('유효한 이메일 주소를 입력해주세요.', './');
        exit;
    }
    
    // 연하장 스타일 테스트 메일 내용
    $test_content = '<p style="font-size: 15px; line-height: 1.8; color: #555555;">이 메일은 연하장 스타일 테스트를 위해 발송되었습니다.</p>';
    $test_content .= '<p style="font-size: 15px; line-height: 1.8; color: #555555;">발송 시간: ' . date('Y-m-d H:i:s') . '</p>';
    $test_content .= '<p style="font-size: 15px; line-height: 1.8; color: #555555;">네이버, 구글, 아웃룩 등 다양한 메일 클라이언트에서 예쁘게 보이는지 확인해주세요.</p>';
    
    // 테스트용 기본 인사말
    $test_greeting = "새해 복 많이 받으세요!\n" . date('Y') . "년 새해를 맞이하여\n건강과 행복이 가득하시기를 기원합니다.";
    $test_header_title = "🎊 " . date('Y') . "년 새해 인사 🎊";
    
    // 연하장 템플릿 사용
    if (function_exists('get_newyear_card_template')) {
        $test_html = get_newyear_card_template('테스트 메일', $test_content, '', '', $test_greeting, $test_header_title);
    } else {
        $test_html = '<html><body><h2>테스트 메일</h2>' . $test_content . '</body></html>';
    }
    
    // 문의폼(mekeng_form_send_html_mail)과 동일: g5_smtp_config 외부 SMTP 분기 없이 config의 로컬 Postfix만 사용
    $result = send_mail_via_smtp(
        $test_email,
        '테스트 메일 - 연하장 스타일',
        $test_html,
        false
    );
    
    if ($result['success']) {
        alert('테스트 메일이 성공적으로 발송되었습니다. 수신함을 확인해주세요.', './');
    } else {
        alert('테스트 메일 발송 실패: ' . $result['error'], './');
    }
}

$g5['title'] = '메일 발송';
$contact_css = G5_URL.'/css/contact.css';
add_stylesheet('<link rel="stylesheet" href="'.$contact_css.'">', 0);

$menuCodeParent = 1;
$menuCodeChild = 5;
include_once(G5_PATH.'/head_simple.php');

// 새글 목록 가져오기
$sql_common = " from {$g5['board_new_table']} a, {$g5['board_table']} b, {$g5['group_table']} c 
                where a.bo_table = b.bo_table 
                and b.gr_id = c.gr_id 
                and b.bo_use_search = 1 
                and a.wr_parent = a.wr_id "; // 댓글 제외
$sql_order = " order by a.bn_id desc ";
$sql = " select a.*, b.bo_subject, c.gr_subject, c.gr_id {$sql_common} {$sql_order} limit 0, 30 ";
$result = sql_query($sql);
$new_articles = array();
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $tmp_write_table = $g5['write_prefix'].$row['bo_table'];
    $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$row['wr_id']}' ");
    
    // 게시판 이름 가져오기
    $board_name = $row['bo_subject'];
    
    $new_articles[] = array(
        'wr_id' => $row['wr_id'],
        'bo_table' => $row['bo_table'],
        'bo_subject' => $board_name,
        'wr_subject' => $row2['wr_subject'],
        'wr_content' => $row2['wr_content'],
        'href' => get_pretty_url($row['bo_table'], $row2['wr_id'])
    );
}

// 메일 발송 처리 (뉴스레터 발송)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'test_mail')) {
    $attachments = array();
    $temp_attach_paths = array();
    try {
        // 이메일 발송 제한 체크
        if (function_exists('check_email_limit')) {
            $limit_check = check_email_limit('mail_sender');
            if (!$limit_check['allowed']) {
                alert($limit_check['message'], './');
                exit;
            }
        }
        // 허니팟 체크
        if (function_exists('check_honeypot') && !check_honeypot('address')) {
            alert('잘못된 접근입니다.', './');
            exit;
        }
        
        // sanitize_input 함수가 없을 경우를 대비
        if (!function_exists('sanitize_input')) {
            function sanitize_input($input) {
                return trim(strip_tags($input));
            }
        }
        
        $newsletter_title = sanitize_input(isset($_POST['newsletter_title']) ? trim($_POST['newsletter_title']) : '');
        $subject = $newsletter_title;
        // 본문 내용은 줄바꿈을 유지하기 위해 sanitize_input 대신 직접 처리
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $greeting_text = isset($_POST['greeting_text']) ? trim($_POST['greeting_text']) : '';
        $header_title = isset($_POST['header_title']) ? trim($_POST['header_title']) : '';
        $mail_type = sanitize_input($_POST['mail_type'] ?? '');
        $custom_emails = sanitize_input($_POST['custom_emails'] ?? '');
        $selected_articles = isset($_POST['selected_articles']) ? array_map('sanitize_input', $_POST['selected_articles']) : array();
        $ad_articles = isset($_POST['ad_articles']) ? array_map('sanitize_input', $_POST['ad_articles']) : array();
        $use_newyear_template = isset($_POST['use_newyear_template']) && $_POST['use_newyear_template'] == '1';
        
        // 첨부파일 처리 (업무용 확장자만 허용)
        $mail_attach_allowed = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip', 'hwp', 'xlsx', 'xls', 'bmp');
        $mail_attach_max_size = 10 * 1024 * 1024;  // 10MB
        $mail_attach_max_files = 10;
        
        if (!empty($_FILES['attachments']['name']) && is_array($_FILES['attachments']['name'])) {
            $tmp_dir = G5_PATH . '/data/tmp';
            if (!is_dir($tmp_dir)) {
                @mkdir($tmp_dir, 0755, true);
            }
            for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
                if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK || $_FILES['attachments']['size'][$i] <= 0) {
                    continue;
                }
                if (count($attachments) >= $mail_attach_max_files) {
                    break;
                }
                $ext = strtolower(pathinfo($_FILES['attachments']['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, $mail_attach_allowed)) {
                    alert('허용되지 않은 첨부 확장자입니다. (허용: ' . implode(', ', $mail_attach_allowed) . ')', './');
                    exit;
                }
                if ($_FILES['attachments']['size'][$i] > $mail_attach_max_size) {
                    alert('첨부파일 크기는 파일당 ' . ($mail_attach_max_size / 1024 / 1024) . 'MB 이하여야 합니다.', './');
                    exit;
                }
                $safe_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['attachments']['name'][$i]);
                $tmp_name = $tmp_dir . '/mail_attach_' . session_id() . '_' . $i . '_' . time() . '_' . $safe_name;
                if (move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $tmp_name)) {
                    $attachments[] = array('path' => $tmp_name, 'name' => $_FILES['attachments']['name'][$i]);
                    $temp_attach_paths[] = $tmp_name;
                }
            }
        }
        
        if (empty($newsletter_title)) {
            alert('뉴스레터 제목을 입력해주세요.', './');
            exit;
        }

        // 선택된 새글 내용 추가
        $newsletter_content = '';
        if (!empty($content)) {
            // 줄바꿈을 <br>로 변환하여 유지 (XSS 방지를 위해 htmlspecialchars 적용)
            $newsletter_content .= nl2br(htmlspecialchars($content)) . "<br><br>";
        }
        if (!empty($selected_articles)) {
            $newsletter_content .= "<h2>새로운 소식</h2>\n";
            foreach ($selected_articles as $article_id) {
                foreach ($new_articles as $article) {
                    if ($article['wr_id'] == $article_id) {
                        // 광고 여부 설정
                        $article['is_ad'] = in_array($article_id, $ad_articles);
                        if (function_exists('get_article_html')) {
                            $newsletter_content .= get_article_html($article);
                        } else {
                            $newsletter_content .= '<div><h3>' . htmlspecialchars($article['wr_subject']) . '</h3><p>' . htmlspecialchars($article['wr_content']) . '</p></div>';
                        }
                        break;
                    }
                }
            }
        }

        // 이메일 목록 수집
        $emails = array();

        switch ($mail_type) {
            case 'subscribe':
                // g5_subscribe 테이블에서 이메일 가져오기
                $sql = "SELECT sb_email FROM g5_subscribe WHERE sb_subscribe = 1";
                $result = sql_query($sql);
                while ($row = sql_fetch_array($result)) {
                    $emails[] = $row['sb_email'];
                }
                break;

            case 'member':
                // g5_member 테이블에서 메일 수신 동의한 회원 이메일 가져오기
                $sql = "SELECT mb_email FROM {$g5['member_table']} WHERE mb_mailling = 1";
                $result = sql_query($sql);
                while ($row = sql_fetch_array($result)) {
                    $emails[] = $row['mb_email'];
                }
                break;

            case 'custom':
                // 직접 입력한 이메일 처리
                $custom_emails = str_replace(array("\r\n", "\r", "\n"), ',', $custom_emails);
                $emails = array_filter(array_map('trim', explode(',', $custom_emails)));
                break;

            case 'csv':
                // CSV 파일 처리
                $csv_file = $_FILES['csv_file'] ?? null;
                if ($csv_file && $csv_file['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = G5_PATH . '/data/file/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_name = $upload_dir . basename($csv_file['name']);
                    move_uploaded_file($csv_file['tmp_name'], $file_name);
                    
                    if (($handle = fopen($file_name, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if (filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                                $emails[] = $data[0];
                            }
                        }
                        fclose($handle);
                    }
                    unlink($file_name); // 처리 후 파일 삭제
                }
                break;
        }

        // 영구 실패(반송) 주소 제외 — 수신측 평판·불필요 재발송 방지
        if (function_exists('get_bounced_emails')) {
            $bounced = get_bounced_emails(90);
            $emails = array_values(array_diff($emails, $bounced));
        }

        if (empty($emails)) {
            alert('발송 대상 이메일이 없습니다.', './');
            exit;
        }

        $total_emails = count($emails);

        // 배치당 최대 건수(다우오피스 IP Rate Control·스팸 완화). 전체는 여러 배치로 순차 발송·건별 로그 기록.
        $mailer_chunk_size = 50;
        $mailer_max_per_request = 5000;
        if ($total_emails > $mailer_max_per_request) {
            alert('한 번의 요청으로 발송 가능한 최대 건수는 ' . number_format($mailer_max_per_request) . '건입니다. CSV를 나누어 주세요.', './');
            exit;
        }
        $email_batches = array_chunk(array_values($emails), $mailer_chunk_size);
        $batch_count = count($email_batches);

        $use_iframe_modal = !empty($_POST['mail_modal']);

        @set_time_limit(0);
        if (function_exists('ignore_user_abort')) {
            @ignore_user_abort(true);
        }

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
            header('X-Accel-Buffering: no');
        }
        @ini_set('zlib.output_compression', '0');
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }

        if ($use_iframe_modal) {
            // 모달 모드: iframe에 로드되며 postMessage로 부모 창에 진행 상황 전달 (페이지 CSS 유지)
            echo '<!DOCTYPE html><html><body>';
            // 일부 프록시/버퍼가 소량 출력을 묶는 경우 대비(진행 스크립트가 바로 전달되도록)
            echo '<!--' . str_repeat(' ', 2048) . '-->';
            echo '<script>try{window.parent.postMessage(' . json_encode(array('type' => 'mail_start', 'total' => (int)$total_emails)) . ',"*");}catch(e){}</script>';
            if (ob_get_level()) {
                ob_end_flush();
            }
            flush();
        } else {
            // 기존 방식: 진행 페이지 직접 출력
            echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head><body style="font-family: sans-serif; padding: 2rem;">';
            echo '<p id="mail-progress" style="font-size: 1.1rem;">메일 발송 중입니다… (0/' . $total_emails . ')</p>';
            echo '<p style="color:#666;">완료될 때까지 이 창을 닫지 마세요.</p>';
            if (ob_get_level()) {
                ob_end_flush();
            }
            flush();
        }

        // 이메일 발송 (로컬 postfix 직접 사용)
        $success_count = 0;
        $fail_count = 0;
        $fail_emails = array();
        $sent_so_far = 0;
        $mail_line_index = 0;

        foreach ($email_batches as $batch_idx => $batch_emails) {
            foreach ($batch_emails as $email) {
            try {
                $mail_line_index++;
                // 이메일 유효성 검증
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                
                // 수신 거부 여부 확인
                if (function_exists('is_unsubscribed') && is_unsubscribed($email)) {
                    // 수신 거부한 이메일은 건너뛰기
                    continue;
                }
                
                // 구독 취소 키 생성 (mail_sender.php의 generate_unsubscribe_key 함수 사용)
                if (function_exists('generate_unsubscribe_key')) {
                    $unsubscribe_key = generate_unsubscribe_key($email);
                } else {
                    $unsubscribe_key = base64_encode($email . '|' . md5($email . (defined('G5_STRING_ENCRYPT_FUNCTION') ? G5_STRING_ENCRYPT_FUNCTION : 'create_hash')));
                }
                // 연하장 템플릿 사용 여부에 따라 템플릿 선택
                if ($use_newyear_template && function_exists('get_newyear_card_template')) {
                    $newsletter_html = get_newyear_card_template($subject, $newsletter_content, $unsubscribe_key, $email, $greeting_text, $header_title);
                } else if (function_exists('get_newsletter_template')) {
                    $newsletter_html = get_newsletter_template($subject, $newsletter_content, $unsubscribe_key, $email);
                } else {
                    $newsletter_html = '<html><body><h1>' . htmlspecialchars($subject) . '</h1>' . $newsletter_content . '</body></html>';
                }

                // 대용량 첨부 시 첫 통 전송만 수 분 걸릴 수 있음 → 전송 직전에 UI 갱신(0/N에서 멈춘 것처럼 보이지 않도록)
                $has_attach = !empty($attachments);
                if ($use_iframe_modal) {
                    echo '<script>try{window.parent.postMessage(' . json_encode(array(
                        'type' => 'mail_attempt',
                        'current' => $mail_line_index,
                        'total' => $total_emails,
                        'has_attach' => $has_attach,
                    )) . ',"*");}catch(e){}</script>';
                } else {
                    echo '<script>var el=document.getElementById("mail-progress");if(el)el.textContent="메일 전송 중… (' . $mail_line_index . '/' . $total_emails . ')' . ($has_attach ? ' 대용량 첨부로 1통당 수 분 걸릴 수 있습니다.' : '') . '";</script>';
                }
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
                
                // SMTP 설정이 있으면 SMTP 사용, 없으면 fallback 사용 (첨부파일 포함, 뉴스레터 시 List-Unsubscribe 헤더 추가)
                $result = send_mail_via_smtp($email, $subject, $newsletter_html, false, $attachments, true);
                
                // 메일 발송 로그 기록 (새로운 로그 시스템)
                // 직접 입력(direct) 또는 CSV(csv) 발송 시 g5_subscribe 테이블에 자동 등록됨
                if (function_exists('log_mail_send')) {
                    log_mail_send(
                        $email, 
                        $mail_type, // direct, csv, member, subscribe
                        $subject, 
                        $result['success'], 
                        $result['success'] ? '' : $result['error']
                    );
                }
                
                if ($result['success']) {
                    $success_count++;
                } else {
                    $fail_count++;
                    $fail_emails[] = $email . ' (' . htmlspecialchars($result['error']) . ')';
                }
                
                $sent_so_far++;
                if ($use_iframe_modal) {
                    echo '<script>try{window.parent.postMessage(' . json_encode(array('type' => 'mail_progress', 'sent' => $sent_so_far, 'total' => $total_emails)) . ',"*");}catch(e){}</script>';
                } else {
                    echo '<script>var el=document.getElementById("mail-progress");if(el)el.textContent="메일 발송 완료 ' . $sent_so_far . '/' . $total_emails . '";</script>';
                }
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
                // 발송 간격(Throttling): 다우오피스 IP Rate Control·수신측 제한 완화 (1건 발송 시에는 대기 없음)
                if ($total_emails > 1 && $sent_so_far < $total_emails) {
                    sleep(2);
                }
            } catch (Exception $e) {
                if (function_exists('log_mail_send')) {
                    log_mail_send($email, $mail_type, $subject, false, $e->getMessage());
                }
                $fail_count++;
                $fail_emails[] = $email . ' (예외: ' . htmlspecialchars($e->getMessage()) . ')';
                $sent_so_far++;
                if ($use_iframe_modal) {
                    echo '<script>try{window.parent.postMessage(' . json_encode(array('type' => 'mail_progress', 'sent' => $sent_so_far, 'total' => $total_emails)) . ',"*");}catch(e){}</script>';
                } else {
                    echo '<script>var el=document.getElementById("mail-progress");if(el)el.textContent="메일 발송 완료 ' . $sent_so_far . '/' . $total_emails . ' (일부 오류)";</script>';
                }
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
                if ($total_emails > 1 && $sent_so_far < $total_emails) {
                    sleep(2);
                }
            }
            }
            // 배치 사이 추가 대기(수신측·게이트웨이 부하 완화). 마지막 배치 뒤에는 생략.
            if ($batch_idx < $batch_count - 1) {
                sleep(4);
            }
        }

        // 발송 후 임시 첨부파일 삭제
        foreach ($temp_attach_paths as $p) {
            if (file_exists($p)) {
                @unlink($p);
            }
        }
        
        $result_message = "총 {$success_count}개의 메일이 성공적으로 발송되었습니다.";
        if ($fail_count > 0) {
            $result_message .= "\n{$fail_count}개의 메일 발송에 실패했습니다.";
            if (!empty($fail_emails)) {
                $result_message .= "\n\n실패한 이메일:\n" . implode("\n", array_slice($fail_emails, 0, 10));
                if (count($fail_emails) > 10) {
                    $result_message .= "\n... 외 " . (count($fail_emails) - 10) . "개";
                }
            }
        }
        if ($batch_count > 1) {
            $result_message .= "\n\n※ 발송을 {$batch_count}개 배치로 나누어 진행했습니다. (배치당 최대 {$mailer_chunk_size}건, 각 수신 건별 발송 로그 기록)";
        }

        $_SESSION['mailer_result_message'] = $result_message;
        $_SESSION['mailer_fail_emails'] = $fail_emails;
        $_SESSION['mailer_success_count'] = $success_count;
        $_SESSION['mailer_fail_count'] = $fail_count;
        
        if ($use_iframe_modal) {
            echo '<script>try{window.parent.postMessage(' . json_encode(array(
                'type' => 'mail_done',
                'message' => $result_message,
                'successCount' => $success_count,
                'failCount' => $fail_count
            )) . ',"*");}catch(e){}</script></body></html>';
        } else {
            echo '<script>alert(' . json_encode($result_message, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '); location.href="./";</script></body></html>';
        }
        exit;
        
    } catch (Exception $e) {
        if (!empty($temp_attach_paths)) {
            foreach ($temp_attach_paths as $p) {
                if (file_exists($p)) {
                    @unlink($p);
                }
            }
        }
        alert('뉴스레터 발송 중 오류가 발생했습니다: ' . htmlspecialchars($e->getMessage()), './');
        exit;
    }
}

// 세션에서 결과 메시지 가져오기
if (isset($_SESSION['mailer_result_message'])) {
    $result_message = $_SESSION['mailer_result_message'];
    $fail_emails = $_SESSION['mailer_fail_emails'] ?? array();
    $success_count = $_SESSION['mailer_success_count'] ?? 0;
    $fail_count = $_SESSION['mailer_fail_count'] ?? 0;
    
    // 세션 변수 제거
    unset($_SESSION['mailer_result_message']);
    unset($_SESSION['mailer_fail_emails']);
    unset($_SESSION['mailer_success_count']);
    unset($_SESSION['mailer_fail_count']);
}

// 메일 로그 조회 (새로운 로그 시스템 사용)
$mail_logs = array();
$log_summary = array('total' => 0, 'success' => 0, 'fail' => 0);

// 최근 24시간 내 발송 로그 조회
if (function_exists('get_mail_send_logs')) {
    $mail_logs = get_mail_send_logs(array(
        'date_from' => date('Y-m-d H:i:s', strtotime('-24 hours')),
        'limit' => 1000
    ));
    
    foreach ($mail_logs as $log) {
        $log_summary['total']++;
        if ($log['msl_success']) {
            $log_summary['success']++;
        } else {
            $log_summary['fail']++;
        }
    }
}

// 오늘 발송된 전체 통계
if (function_exists('get_mail_send_stats')) {
    $today_stats = get_mail_send_stats(array(
        'date_from' => date('Y-m-d 00:00:00'),
        'date_to' => date('Y-m-d 23:59:59')
    ));
} else {
    $today_stats = array('total' => 0, 'success' => 0, 'fail' => 0);
}

// 오늘자 CSV 발송만 집계 (로그 기준: 발송 루틴을 탄 주소만 기록됨 — 형식 오류·수신거부 스킵은 미기록)
$today_csv_stats = array('total' => 0, 'success' => 0, 'fail' => 0, 'unique_emails' => 0);
$today_csv_failed_logs = array();
if (function_exists('get_mail_send_stats')) {
    $csv_row = get_mail_send_stats(array(
        'date_from' => date('Y-m-d 00:00:00'),
        'date_to' => date('Y-m-d 23:59:59'),
        'send_type' => 'csv',
    ));
    if (is_array($csv_row)) {
        $today_csv_stats['total'] = (int) ($csv_row['total'] ?? 0);
        $today_csv_stats['success'] = (int) ($csv_row['success'] ?? 0);
        $today_csv_stats['fail'] = (int) ($csv_row['fail'] ?? 0);
        $today_csv_stats['unique_emails'] = (int) ($csv_row['unique_emails'] ?? 0);
    }
}
if (function_exists('get_mail_send_logs')) {
    $today_csv_failed_logs = get_mail_send_logs(array(
        'send_type' => 'csv',
        'success' => 0,
        'date_from' => date('Y-m-d 00:00:00'),
        'date_to' => date('Y-m-d'),
        'limit' => 3000,
    ));
}
$today_csv_fail_emails_only = array();
foreach ($today_csv_failed_logs as $fl) {
    if (!empty($fl['msl_email'])) {
        $today_csv_fail_emails_only[] = $fl['msl_email'];
    }
}
$today_csv_fail_emails_only = array_values(array_unique($today_csv_fail_emails_only));
?>

<div class="mail-sender-container" style="max-width: 1200px; margin: 30px auto; padding: 20px;">
    <h1 class="sound_only">메일 발송</h1>

    <!-- 뉴MEK+ Mail 발송 섹션 -->
    <div class="mail-form-section" style="border: 1px solid #ddd;">
        <h2 style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">MEK+ Mail 발송 <button type="button" id="btn_mailer_notice_top" style="padding: 4px 10px; background:#b8860b; color: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-size: 12px;">주의사항</button></h2>
        <br>

    <?php if (isset($result_message)): ?>
    <div class="mail-result">
        <p><?php echo nl2br(htmlspecialchars($result_message)); ?></p>
        <?php if (!empty($fail_emails)): ?>
        <p>실패한 이메일 목록:</p>
        <ul>
            <?php foreach ($fail_emails as $email): ?>
            <li><?php echo htmlspecialchars($email); ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- 오늘자 CSV 발송 집계 · 재발송용 실패 목록 -->
    <?php if (($today_csv_stats['total'] ?? 0) > 0 || !empty($today_csv_failed_logs)): ?>
    <div class="mail-form-section" style="border: 1px solid #b0d4f1; margin-bottom: 20px; background: #f0f8ff;">
        <h2 style="margin-top: 0; color: #1565c0;">📊 오늘 날짜 · CSV 발송 집계 (로그 기준)</h2>
        <p style="color: #555; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">
            아래 숫자는 <strong>g5_mail_send_log</strong>에 <code>msl_send_type = csv</code>로 저장된 건만 해당합니다.<br>
            CSV에 있었지만 <strong>이메일 형식 오류·수신거부·빈 행</strong> 등으로 발송 시도 전에 건너뛴 주소는 <strong>로그에 남지 않습니다</strong>. (원본 CSV 행 수와 다를 수 있음)
        </p>
        <table style="width: 100%; max-width: 520px; border-collapse: collapse; font-size: 14px; margin-bottom: 16px;">
            <tr style="background: #e3f2fd;">
                <th style="padding: 10px; text-align: left; border: 1px solid #90caf9;">구분</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #90caf9;">건수</th>
            </tr>
            <tr>
                <td style="padding: 8px 10px; border: 1px solid #ddd;">시도(로그 기록) — 성공+실패 합계</td>
                <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right; font-weight: bold;"><?php echo number_format($today_csv_stats['total']); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px 10px; border: 1px solid #ddd;">실제 발송 성공</td>
                <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right; color: #2e7d32; font-weight: bold;"><?php echo number_format($today_csv_stats['success']); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px 10px; border: 1px solid #ddd;">실패 <span style="color:#c62828;">(재발송 필요)</span></td>
                <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right; color: #c62828; font-weight: bold;"><?php echo number_format($today_csv_stats['fail']); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px 10px; border: 1px solid #ddd;">고유 수신 이메일 수(당일 csv)</td>
                <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right;"><?php echo number_format($today_csv_stats['unique_emails']); ?></td>
            </tr>
        </table>
        <?php if (!empty($today_csv_fail_emails_only)): ?>
        <p style="font-weight: 600; margin: 0 0 8px 0;">실패한 주소만 (재발송 시 「직접 입력」에 붙여 넣기)</p>
        <textarea readonly rows="<?php echo min(12, max(3, count($today_csv_fail_emails_only))); ?>" style="width: 100%; max-width: 640px; font-family: monospace; font-size: 13px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><?php echo htmlspecialchars(implode("\n", $today_csv_fail_emails_only), ENT_QUOTES, 'UTF-8'); ?></textarea>
        <p style="font-size: 12px; color: #666; margin-top: 8px;">최대 3,000건까지 조회합니다. 더 많으면 아래 「메일 발송 로그 CSV 다운로드」에서 오늘·csv·실패만 필터해 받으세요.</p>
        <?php endif; ?>
        <?php if (!empty($today_csv_failed_logs)): ?>
        <details style="margin-top: 14px;">
            <summary style="cursor: pointer; color: #1565c0; font-weight: 600;">실패 건 상세 (오류 메시지)</summary>
            <div style="max-height: 240px; overflow-y: auto; margin-top: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead style="background: #f5f5f5; position: sticky; top: 0;">
                        <tr>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">이메일</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">시각</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">오류</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($today_csv_failed_logs as $fl): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 6px 8px;"><?php echo htmlspecialchars($fl['msl_email'] ?? ''); ?></td>
                            <td style="padding: 6px 8px; white-space: nowrap;"><?php echo htmlspecialchars($fl['msl_send_date'] ?? ''); ?></td>
                            <td style="padding: 6px 8px; color: #b71c1c;"><?php
                            $em = (string)($fl['msl_error_message'] ?? '');
                            $em_short = function_exists('mb_substr') ? mb_substr($em, 0, 200, 'UTF-8') : substr($em, 0, 200);
                            echo htmlspecialchars($em_short);
                            ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </details>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="mail-form-section" style="border: 1px dashed #ccc; margin-bottom: 16px; padding: 16px; background: #fafafa;">
        <strong>📊 오늘 날짜 · CSV 발송 로그</strong>
        <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">오늘 00:00 이후 <code>csv</code> 유형으로 기록된 발송이 없습니다. (원본 CSV 행 수와 비교하려면 발송 후 이 영역을 확인하세요.)</p>
    </div>
    <?php endif; ?>

    <!-- 메일 발송 진행 모달 (어두운 배경 + 진행 텍스트) -->
    <div id="mail-send-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; flex-direction: column;">
        <div style="background: #fff; padding: 2rem; border-radius: 8px; min-width: 280px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <p id="mail-send-progress" style="font-size: 1.1rem; margin: 0 0 1rem 0;">메일 발송 중입니다…</p>
            <p style="color: #666; font-size: 0.9rem; margin: 0;">완료될 때까지 이 창을 닫지 마세요.</p>
        </div>
    </div>

    <!-- 주의사항 안내 모달 -->
    <div id="mailer-notice-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9998; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
        <div style="background: #fff; max-width: 520px; width: 90%; max-height: 85vh; overflow-y: auto; padding: 24px; border-radius: 8px; box-shadow: 0 4px 24px rgba(0,0,0,0.2);">
            <h3 style="margin: 0 0 16px 0; font-size: 18px; color: #333; border-bottom: 2px solid #b8860b; padding-bottom: 8px;">⚠️ 메일 발송 주의사항</h3>
            <ol style="margin: 0 0 16px 0; padding-left: 20px; line-height: 1.7; color: #444;">
                <li><strong>메일발송명단을 엑셀파일로 첨부 시, 반드시 CSV UTF-8 형식</strong>을 사용하세요.</li>
                <li>시트의 <strong>제1행은 메뉴(헤더) 행</strong>으로 처리됩니다.</li>
                <li><span style="color: #c00;">[필수]</span> 시트의 <strong>제1열(제1행 제외)</strong>에 이메일 주소를 넣어주세요. (제2열 이후에 이름, 연락처 등 다른 정보가 있는 것은 상관 없음)</li>
            </ol>
            <p style="margin: 0 0 8px 0; font-size: 14px; color: #555;"><strong>그 밖에</strong></p>
            <ul style="margin: 0 0 20px 0; padding-left: 20px; line-height: 1.7; color: #444;">
                <li>대량 발송 시 <strong>배치당 최대 50건</strong>씩 나누어 한 요청 안에서 순차 발송합니다. (건별 로그 기록, 다우·스팸 완화) 한 번에 최대 <strong>5,000건</strong>까지 가능하며, 그 이상은 CSV를 나누어 주세요.</li>
                <li>주소 없는 메일, 반송된 메일은 다음 메일 전송 시 <strong>자동으로 메일 리스트에서 제외</strong>됩니다.</li> 
                <li>메일 발송 로그의 <strong>발송 결과 리포트(CSV)</strong>를 다운받아, 차후 메일 발송 리스트 작성 시 해당 주소를 제외하는 것을 권장합니다.</li>
            </ul>
            <button type="button" id="mailer-notice-close" style="display: block; margin: 0 auto; padding: 10px 24px; background: #b8860b; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">확인</button>
        </div>
    </div>
    <iframe name="mail_send_frame" id="mail_send_frame" style="display: none; position: absolute; left: -9999px; width: 1px; height: 1px;"></iframe>

    <form method="post" enctype="multipart/form-data" id="mailer-send-form">
        <!-- 허니팟 필드 (CSS로 숨김) -->
        <div style="display: none;">
            <input type="text" name="address" tabindex="-1" autocomplete="off">
        </div>
        
        <div class="mail-form-group">
            <label for="newsletter_title">📝 제목</label>
            <input type="text" id="newsletter_title" name="newsletter_title" required 
                   value="<?php echo htmlspecialchars($config['cf_title'] . ' ' . date('Y년 m월') . ' 뉴스레터'); ?>">
        </div>

        <div class="mail-form-group" id="header_title_group">
            <label for="header_title">🏷️ 헤더 제목</label>
            <input type="text" id="header_title" name="header_title" 
                   value="🎊 <?php echo date('Y'); ?>년 새해 인사 🎊"
                   placeholder="🎊 2025년 새해 인사 🎊"
                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            <p class="help-text">연하장 상단 헤더에 표시될 제목을 입력하세요.</p>
        </div>

        <div class="mail-form-group" id="greeting_group">
            <label for="greeting_text">💬 연하장 인사말</label>
            <textarea id="greeting_text" name="greeting_text" rows="4" placeholder="새해 복 많이 받으세요!&#10;2025년 새해를 맞이하여&#10;건강과 행복이 가득하시기를 기원합니다.">새해 복 많이 받으세요!
<?php echo date('Y'); ?>년 새해를 맞이하여
건강과 행복이 가득하시기를 기원합니다.</textarea>
            <p class="help-text">연하장 상단에 표시될 인사말을 입력하세요. 줄바꿈이 그대로 반영됩니다.</p>
        </div>

        <div class="mail-form-group">
            <label for="content">✍️ 추가 메시지 (선택사항)</label>
            <textarea id="content" name="content" rows="5" placeholder="기본 인사말 외에 추가로 전달할 메시지가 있다면 입력해주세요."></textarea>
            <p class="help-text">입력한 줄바꿈이 그대로 유지됩니다.</p>
        </div>

        <div class="mail-form-group">
            <label>
                <input type="checkbox" name="use_newyear_template" value="1" style="margin-right: 8px;">
                <strong>🎨 연하장 스타일 템플릿 사용</strong> (네이버, 구글, 아웃룩 등에서 예쁘게 표시됩니다)
            </label>
            <p class="help-text">체크 해제 시 기본 뉴스레터 템플릿을 사용합니다.</p>
        </div>

        <div class="mail-form-group">
            <label for="attachments">📎 첨부파일 (선택)</label>
            <input type="file" id="attachments" name="attachments[]" multiple
                   accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip,.hwp,.xlsx,.xls,.bmp"
                   style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
            <p class="help-text">
                허용: JPG, PNG, GIF, PDF, DOC, DOCX, TXT, ZIP, HWP, XLSX, XLS, BMP (파일당 최대 10MB, 최대 10개).
                네이버·구글·다음 등 포털 메일도 표준 MIME 첨부로 수신 가능합니다. 단, 일부 메일함은 대용량 제한이 있을 수 있습니다.
            </p>
        </div>

        <div class="mail-form-group mail-form-group-mail-target">
            <label for="mail_type" class="mail-mail-target-label">
                <span class="mail-mail-target-label-text">👥 메일 발송 명단 (선택)</span>
            </label>
            <select id="mail_type" name="mail_type" required>
                <option value="custom">직접 입력</option>
                <option value="subscribe">구독자 (g5_subscribe)</option>
                <option value="member">회원 (g5_member)</option>
                <option value="csv">CSV 파일 업로드</option>
            </select>
        </div>

        <div class="mail-form-group" id="custom_emails_group">
            <label for="custom_emails">📋 이메일 목록 (쉼표 또는 줄바꿈으로 구분)</label>
            <textarea id="custom_emails" name="custom_emails" rows="5" style="background: #f5f5f5; color: royalblue; padding: 5px; border-radius: 5px;"></textarea>
        </div>

        <div class="mail-form-group" id="csv_file_group" style="display:none;">
            <label for="csv_file">📄 CSV 파일 업로드</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv">
            <div style="margin-top: 10px; padding: 12px 14px; background: #fff8e6; border: 1px solid #e6c84a; border-radius: 6px; font-size: 14px;">
                <strong style="color: #b8860b;">⚠ CSV 업로드 시:</strong> 제1행은 메뉴(헤더) 행이며, <strong>첫 번째 열에 이메일 주소</strong>를 넣어주세요.
                <button type="button" id="btn_mailer_notice" style="margin-left: 10px; padding: 4px 12px; background: #b8860b; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">주의사항 보기</button>
            </div>
        </div>

        <div class="mail-form-group">
            <label>📰 새글 목록</label>
            <div class="new-articles-list">
                <?php foreach ($new_articles as $article): ?>
                <div class="article-item" data-board-name="<?php echo htmlspecialchars($article['bo_subject']); ?>">
                    <input type="checkbox" name="selected_articles[]" value="<?php echo htmlspecialchars($article['wr_id']); ?>">
                    <input type="checkbox" name="ad_articles[]" value="<?php echo htmlspecialchars($article['wr_id']); ?>" class="ad-checkbox" title="광고 표시">
                    <span class="article-title">[<?php echo htmlspecialchars($article['bo_subject']); ?>] <?php echo htmlspecialchars($article['wr_subject']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="help-text">
                <span class="checkbox-legend">☐ 글 선택</span>
                <span class="checkbox-legend">🏷️ 광고 표시</span>
            </p>
        </div>

        <button type="submit" class="mail-submit-btn" style="width: 150px;">📧 메일 발송하기 </button>
    </form>
    </div>

    <br>


    <!-- 메일 발송 로그 섹션 -->
    <div class="mail-form-section" style="border: 1px solid #ddd; margin-bottom: 20px; background: #f8f9fa;">
        <h2>📊 메일 발송 로그</h2>
        
        <div style="padding: 20px; background: #fff; border-radius: 5px; margin-bottom: 15px;">
            <h3 style="margin-top: 0; color: #333;">📈 오늘 발송 통계</h3>
            <div style="display: flex; gap: 30px; margin-bottom: 15px; flex-wrap: wrap;">
                <div>
                    <strong style="color: #666;">전체 발송:</strong> 
                    <span style="font-size: 18px; font-weight: bold; color: #007bff;"><?php echo number_format($today_stats['total'] ?? 0); ?>건</span>
                </div>
                <div>
                    <strong style="color: #666;">성공:</strong> 
                    <span style="font-size: 18px; font-weight: bold; color: #28a745;"><?php echo number_format($today_stats['success'] ?? 0); ?>건</span>
                </div>
                <div>
                    <strong style="color: #666;">실패:</strong> 
                    <span style="font-size: 18px; font-weight: bold; color: #dc3545;"><?php echo number_format($today_stats['fail'] ?? 0); ?>건</span>
                </div>
            </div>
            
            <h3 style="margin-top: 20px; color: #333;">📜 최근 24시간 발송 내역 (최대 1000건)</h3>
            <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead style="background: #f5f5f5; position: sticky; top: 0;">
                        <tr>
                            <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">발송 시간</th>
                            <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">이메일 주소</th>
                            <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">상태</th>
                            <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mail_logs)): ?>
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: #999;">최근 24시간 내 발송된 메일이 없습니다.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($mail_logs as $log): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 8px 10px;"><?php echo htmlspecialchars($log['msl_send_date'] ?? $log['ml_datetime'] ?? ''); ?></td>
                            <td style="padding: 8px 10px;">
                                <?php echo htmlspecialchars($log['msl_email'] ?? $log['ml_email'] ?? ''); ?>
                                <br>
                                <small style="color: #999;">
                                    [<?php 
                                    $send_type = $log['msl_send_type'] ?? '';
                                    $type_names = array(
                                        'direct' => '직접입력',
                                        'csv' => 'CSV',
                                        'member' => '회원',
                                        'subscribe' => '구독자'
                                    );
                                    echo $type_names[$send_type] ?? $send_type;
                                    ?>]
                                    <?php if (!empty($log['msl_unsubscribe']) && $log['msl_unsubscribe'] == 1): ?>
                                        <span style="color: #dc3545;">[수신거부]</span>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td style="padding: 8px 10px; text-align: center;">
                                <?php 
                                $success = $log['msl_success'] ?? $log['ml_success'] ?? 0;
                                if ($success): ?>
                                    <span style="color: #28a745; font-weight: bold;">✓ 성공</span>
                                <?php else: ?>
                                    <span style="color: #dc3545; font-weight: bold;">✗ 실패</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 8px 10px; color: #666;"><?php echo htmlspecialchars($log['msl_ip'] ?? $log['ml_ip'] ?? ''); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p style="margin-top: 10px; font-size: 12px; color: #666;">
                * 최근 24시간 내 발송된 메일만 표시됩니다. (최대 1000건)
            </p>
            
            <!-- CSV 다운로드 섹션 -->
            <div style="margin-top: 20px; padding: 15px; background: #f0f8ff; border-radius: 5px; border: 1px solid #b0d4f1;">
                <h3 style="margin-top: 0; color: #333;">📥 메일 발송 로그 CSV 다운로드</h3>
                <form method="get" action="./" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                    <input type="hidden" name="action" value="download_csv">
                    
                    <div style="flex: 1; min-width: 120px;">
                        <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px;">시작일</label>
                        <input type="date" name="date_from" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>" 
                            style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="flex: 1; min-width: 120px;">
                        <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px;">종료일</label>
                        <input type="date" name="date_to" value="<?php echo date('Y-m-d'); ?>" 
                            style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="flex: 1; min-width: 100px;">
                        <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px;">발송 유형</label>
                        <select name="send_type" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="">전체</option>
                            <option value="direct">직접입력</option>
                            <option value="csv">CSV</option>
                            <option value="member">회원</option>
                            <option value="subscribe">구독자</option>
                        </select>
                    </div>
                    
                    <div style="flex: 1; min-width: 100px;">
                        <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px;">발송 상태</label>
                        <select name="success" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="-1">전체</option>
                            <option value="1">성공</option>
                            <option value="0">실패</option>
                        </select>
                    </div>
                    
                    <div>
                        <button type="submit" style="padding: 8px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            📥 CSV 다운로드
                        </button>
                    </div>
                </form>
                <p style="margin-top: 10px; font-size: 11px; color: #666;">
                    * 최대 10,000건까지 다운로드 가능합니다. (UTF-8 형식, 한글 지원)
                </p>
            </div>
        </div>
    </div>

    <!-- 테스트 메일 발송 섹션 -->
    <div class="mail-form-section" style="margin-top: 50px; background: #f5f5f5; border: 1px solid #ddd;">
        <h2>🧪 테스트 메일 발송</h2>
        <form method="post" id="testMailForm">
            <input type="hidden" name="action" value="test_mail">
            
            <div class="mail-form-group">
                <label for="test_email">✉️ 테스트 이메일 주소 <span class="required">*</span></label>
                <input type="email" id="test_email" name="test_email" required 
                    style="background: #fff; color: royalblue; padding: 5px; border-radius: 5px; border: 1px solid #ddd;"
                    placeholder="수신 이메일 주소 입력">
                <p class="help-text">(일반 메일 및 포털 메일 주소로 테스트해보세요.)</p>
            </div>

            <button type="submit" class="mail-submit-btn" style="background: #888; color: #fff; width: 150px;">Test Mail 발송</button>
        </form>
    </div>

</div>

<style>
.mail-form-section {
    background: #fff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.mail-form-section h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #111;
    font-size: 20px;
    font-weight: 700;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}
.required {
    color: #dc3545;
}
.help-text {
    font-size: 0.9em;
    color: #666;
    margin-top: 5px;
    margin-bottom: 0;
}
.mail-result {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    border-left: 4px solid #007bff;
}
.mail-result ul {
    margin: 10px 0;
    padding-left: 20px;
}
.new-articles-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ddd;
    padding: 10px;
    margin-bottom: 10px;
}
.article-item {
    padding: 5px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 8px;
}
.article-item:last-child {
    border-bottom: none;
}
.ad-checkbox {
    width: 16px;
    height: 16px;
    position: relative;
}
.ad-checkbox::after {
    content: '🏷️';
    position: absolute;
    top: -2px;
    left: 0;
    opacity: 0.5;
}
.checkbox-legend {
    margin-right: 15px;
    font-size: 0.9em;
    color: #666;
}
/* 메일 발송 명단 (선택) — 강조 */
.mail-form-group-mail-target .mail-mail-target-label {
    display: block;
    margin-bottom: 10px;
}
.mail-mail-target-label-text {
    display: inline-block;
    font-size: 1.28rem;
    font-weight: 700;
    color: #1565c0;
    letter-spacing: -0.02em;
    line-height: 1.35;
    text-shadow: 0 1px 0 rgba(255,255,255,0.6);
}
</style>

<script>
// 메일 발송: 모달로 진행 표시 (iframe + postMessage)
(function() {
    var form = document.getElementById('mailer-send-form');
    var modal = document.getElementById('mail-send-modal');
    var progressEl = document.getElementById('mail-send-progress');
    if (!form || !modal || !progressEl) return;

    form.addEventListener('submit', function(ev) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'mail_modal';
        input.value = '1';
        form.appendChild(input);
        form.target = 'mail_send_frame';
        modal.style.display = 'flex';
        progressEl.textContent = '메일 발송 중입니다…';
    });

    window.addEventListener('message', function(ev) {
        var d = ev.data;
        if (!d || typeof d !== 'object' || !d.type) return;
        if (d.type === 'mail_start') {
            modal.style.display = 'flex';
            progressEl.textContent = '메일 발송 준비 중… (0/' + (d.total || 0) + ')';
        } else if (d.type === 'mail_attempt') {
            var hint = (d.has_attach ? ' 대용량 첨부 전송 중(1통당 수 분 걸릴 수 있음)' : '');
            progressEl.textContent = '메일 전송 중… 목록 ' + (d.current || 0) + '/' + (d.total || 0) + hint;
        } else if (d.type === 'mail_progress') {
            progressEl.textContent = '메일 발송 완료 ' + (d.sent || 0) + '/' + (d.total || 0) + ' (다음 통 준비 중이면 잠시 대기)';
        } else if (d.type === 'mail_done') {
            modal.style.display = 'none';
            form.target = '';
            var inp = form.querySelector('input[name="mail_modal"]');
            if (inp) inp.remove();
            if (d.message) alert(d.message);
            location.href = './';
        }
    });
})();

// 메일 타입 변경 시 필드 표시/숨김
document.getElementById('mail_type').addEventListener('change', function() {
    const customEmailsGroup = document.getElementById('custom_emails_group');
    const csvFileGroup = document.getElementById('csv_file_group');
    
    if (this.value === 'custom') {
        customEmailsGroup.style.display = 'block';
        csvFileGroup.style.display = 'none';
    } else if (this.value === 'csv') {
        customEmailsGroup.style.display = 'none';
        csvFileGroup.style.display = 'block';
    } else {
        customEmailsGroup.style.display = 'none';
        csvFileGroup.style.display = 'none';
    }
});

// 주의사항 모달 열기/닫기
(function() {
    var modal = document.getElementById('mailer-notice-modal');
    var closeBtn = document.getElementById('mailer-notice-close');
    function openNotice() { if (modal) modal.style.display = 'flex'; }
    function closeNotice() { if (modal) modal.style.display = 'none'; }
    var btn = document.getElementById('btn_mailer_notice');
    var btnTop = document.getElementById('btn_mailer_notice_top');
    if (btn) btn.addEventListener('click', openNotice);
    if (btnTop) btnTop.addEventListener('click', openNotice);
    if (closeBtn) closeBtn.addEventListener('click', closeNotice);
    if (modal) modal.addEventListener('click', function(e) { if (e.target === modal) closeNotice(); });
})();

// 연하장 템플릿 체크박스 변경 시 인사말 필드 표시/숨김
document.addEventListener('DOMContentLoaded', function() {
    const newyearTemplateCheckbox = document.querySelector('input[name="use_newyear_template"]');
    const greetingGroup = document.getElementById('greeting_group');
    const headerTitleGroup = document.getElementById('header_title_group');
    
    function toggleNewyearFields() {
        if (newyearTemplateCheckbox) {
            const isChecked = newyearTemplateCheckbox.checked;
            if (greetingGroup) {
                greetingGroup.style.display = isChecked ? 'block' : 'none';
            }
            if (headerTitleGroup) {
                headerTitleGroup.style.display = isChecked ? 'block' : 'none';
            }
        }
    }
    
    if (newyearTemplateCheckbox) {
        newyearTemplateCheckbox.addEventListener('change', toggleNewyearFields);
        // 초기 상태 설정
        toggleNewyearFields();
    }
});
</script>

<!-- <div style="text-align: center; margin-top: 10px; padding: 20px;">
    <a href="<?php echo G5_URL; ?>/plus/" class="mail-submit-btn" style="display: inline-block; text-decoration: none; background: #555; color: #fff; padding: 12px 30px; border-radius: 5px; border: none; cursor: pointer;">
        Go back to MEK+ 홈
    </a>
</div> -->

<?php
include_once(G5_PATH.'/tail_simple.php');
?>
