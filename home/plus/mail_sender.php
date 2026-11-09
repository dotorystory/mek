<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 로컬 Postfix 제출: `lib/mailer.lib.php` + `config.php`의 G5_SMTP_PORT / G5_SMTP_SECURE 와 동일.
 * 샘플(mail_sender_sample → mailer)은 587+TLS, 기존 폴백은 25만 사용해 동작이 갈리던 것을 맞춤.
 *
 * @param PHPMailer $mail
 */
function mekeng_phpmailer_apply_local_postfix($mail) {
    $mail->Host = '127.0.0.1';
    $mail->Port = (defined('G5_SMTP_PORT') && G5_SMTP_PORT !== '' && (int) G5_SMTP_PORT > 0)
        ? (int) G5_SMTP_PORT
        : 25;
    $use_secure = defined('G5_SMTP_SECURE') ? G5_SMTP_SECURE : '';
    if ($use_secure === 'tls' || $use_secure === 'ssl') {
        $mail->SMTPSecure = $use_secure;
        $mail->SMTPAutoTLS = true;
    } else {
        $mail->SMTPSecure = false;
        $mail->SMTPAutoTLS = false;
    }
    $mail->SMTPAuth = false;
}

/**
 * PHPMailer를 사용하여 SMTP로 메일 발송
 * @param string $to 수신 이메일
 * @param string $subject 제목
 * @param string $content 내용 (HTML)
 * @param bool $use_smtp_config SMTP 설정 테이블 사용 여부
 * @param array $attachments 첨부파일 [ ['path' => 파일경로, 'name' => 메일에 표시할 파일명], ... ]
 * @param bool $add_list_unsubscribe 뉴스레터 발송 시 List-Unsubscribe 헤더 추가 여부
 * @return array ['success' => bool, 'error' => string]
 */
function send_mail_via_smtp($to, $subject, $content, $use_smtp_config = true, $attachments = array(), $add_list_unsubscribe = false) {
    global $config, $g5;
    
    if (!class_exists('PHPMailer')) {
        include_once(G5_PHPMAILER_PATH.'/PHPMailerAutoload.php');
    }
    
    try {
        $mail = new PHPMailer(true);
        
        // SMTP 설정 가져오기
        if ($use_smtp_config) {
            $smtp_config = sql_fetch("SELECT * FROM g5_smtp_config WHERE sc_active = 1 LIMIT 1");
            
            // 로컬 postfix: PHPMailer 127.0.0.1:25 폴백(첨부·List-Unsubscribe 포함)
            if ($smtp_config && isset($smtp_config['sc_use_local_postfix']) && $smtp_config['sc_use_local_postfix'] == 1) {
                return send_mail_via_smtp_fallback($to, $subject, $content, '', $attachments, $add_list_unsubscribe);
            }
            
            if ($smtp_config && !empty($smtp_config['sc_host'])) {
                // 데이터베이스의 SMTP 설정 사용
                $mail->isSMTP();
                $mail->Host = $smtp_config['sc_host'];
                $mail->Port = intval($smtp_config['sc_port']);
                
                if ($smtp_config['sc_secure'] === 'tls') {
                    $mail->SMTPSecure = 'tls';
                } elseif ($smtp_config['sc_secure'] === 'ssl') {
                    $mail->SMTPSecure = 'ssl';
                } else {
                    $mail->SMTPSecure = false;
                }
                
                $mail->SMTPAuth = (bool)$smtp_config['sc_auth'];
                
                if ($smtp_config['sc_auth']) {
                    $mail->Username = $smtp_config['sc_username'];
                    if (!empty($smtp_config['sc_password'])) {
                        $mail->Password = base64_decode($smtp_config['sc_password']);
                    }
                }
                
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                // 대용량 첨부(수 MB×다수)·느린 릴레이 시 기본 타임아웃(300초)으로 중단되는 경우 방지
                $mail->Timeout = 600;

                $from_email = $smtp_config['sc_from_email'];
                $from_name = !empty($smtp_config['sc_from_name']) ? $smtp_config['sc_from_name'] : $config['cf_title'];
                
                $mail->setFrom($from_email, $from_name);
                $mail->addReplyTo($from_email, $from_name);
                // 네이버 등: envelope(MAIL FROM)과 From 도메인 정렬·SPF 통과에 유리
                $mail->Sender = $from_email;
            } else {
                // SMTP 설정이 없으면 기본 설정 사용
                return send_mail_via_smtp_fallback($to, $subject, $content, '', $attachments, $add_list_unsubscribe);
            }
        } else {
            // 기본 설정 사용 (config.php의 G5_SMTP)
            if (defined('G5_SMTP') && G5_SMTP && G5_SMTP !== '127.0.0.1') {
                $mail->isSMTP();
                $mail->Host = G5_SMTP;
                $mail->Port = defined('G5_SMTP_PORT') ? intval(G5_SMTP_PORT) : 587;
                $mail->SMTPSecure = 'tls';
                $mail->SMTPAuth = true;
                $mail->Timeout = 600;
                // 기본 SMTP 인증 정보는 설정 파일에 없으므로 사용자 입력 필요
                // 포털 메일: 실제 릴레이 호스트(webmail)와 동일 도메인 권장(네이버 5.7.2 완화)
                $from_email = 'msk@mekeng.com';
                $from_name = $config['cf_title'] ?? '';
                $mail->setFrom($from_email, $from_name);
                $mail->Sender = $from_email;
            } else {
                return send_mail_via_smtp_fallback($to, $subject, $content, '', $attachments, $add_list_unsubscribe);
            }
        }
        
        // 메일 내용 설정
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;
        $mail->AltBody = strip_tags($content);
        $mail->addAddress($to);
        
        // 첨부파일 추가
        foreach ($attachments as $att) {
            if (!empty($att['path']) && is_readable($att['path'])) {
                $name = isset($att['name']) && $att['name'] !== '' ? $att['name'] : basename($att['path']);
                $mail->addAttachment($att['path'], $name);
            }
        }
        
        // SMTP 옵션 설정 (자체 서명 인증서 허용)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // 정상 메일로 인정받기 위한 헤더 (스팸 오인·수신 제한 완화)
        $msg_id_domain = (defined('G5_URL') && G5_URL) ? parse_url(G5_URL, PHP_URL_HOST) : 'mekeng.com';
        if (empty($msg_id_domain)) {
            $msg_id_domain = 'mekeng.com';
        }
        // PHPMailer가 기본 Message-ID 헤더를 생성하므로 addCustomHeader로 추가하면 중복 헤더가 되어
        // Gmail(5.7.1 RFC 5322)에서 차단될 수 있다.
        $mail->MessageID = '<' . uniqid('mek.', true) . '.' . time() . '@' . $msg_id_domain . '>';
        $mail->addCustomHeader('X-Mailer', 'MEK-WebMailer/1.0');
        if ($add_list_unsubscribe && function_exists('generate_unsubscribe_key')) {
            $base_url = defined('G5_URL') ? rtrim(G5_URL, '/') : '';
            $unsub_url = $base_url . '/plus/mailer/?action=unsubscribe&key=' . urlencode(generate_unsubscribe_key($to)) . '&email=' . urlencode($to);
            if (!empty(trim($base_url))) {
                $mail->addCustomHeader('List-Unsubscribe', '<' . $unsub_url . '>');
                $mail->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            }
        }
        
        // 디버그 모드 (필요시 활성화)
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'error_log';
        
        if (empty($mail->Sender) && !empty($mail->From)) {
            $mail->Sender = $mail->From;
        }
        if ($mail->send()) {
            return ['success' => true, 'error' => ''];
        } else {
            // SMTP 발송 실패 시 로컬 Postfix( mailer() 와 동일 포트/TLS )로 폴백
            $error_info = $mail->ErrorInfo;
            return send_mail_via_smtp_fallback($to, $subject, $content, $error_info, $attachments, $add_list_unsubscribe);
        }
        
    } catch (\Exception $e) {
        // 예외 시 동일 폴백
        return send_mail_via_smtp_fallback($to, $subject, $content, $e->getMessage(), $attachments, $add_list_unsubscribe);
    }
}

/**
 * SMTP 설정이 없을 때·로컬 Postfix 옵션·외부 SMTP 실패 시 127.0.0.1:25(PHPMailer)로 폴백
 * apache sendmail(mail()) 제한을 피하고 발신 도메인을 webmail과 맞춤
 * 
 * @param string $to 수신 이메일
 * @param string $subject 제목
 * @param string $content 내용 (HTML)
 * @param string $smtp_error SMTP 발송 실패 시 오류 메시지 (선택사항)
 * @param array $attachments 첨부파일 [ ['path' => 파일경로, 'name' => 메일에 표시할 파일명], ... ]
 * @param bool $add_list_unsubscribe List-Unsubscribe 헤더 추가 여부
 * @return array ['success' => bool, 'error' => string]
 */
function send_mail_via_smtp_fallback($to, $subject, $content, $smtp_error = '', $attachments = array(), $add_list_unsubscribe = false) {
    global $config;

    // 로컬 Postfix: mailer()와 동일하게 config 포트(예: 587)+TLS 사용.
    $from_email = (defined('G5_MAIL_FROM') && G5_MAIL_FROM) ? G5_MAIL_FROM : 'msk@mekeng.com';
    $from_name = (defined('G5_MAIL_FROM_NAME') && G5_MAIL_FROM_NAME) ? G5_MAIL_FROM_NAME : ($config['cf_title'] ?? '');

    if (!class_exists('PHPMailer')) {
        include_once(G5_PHPMAILER_PATH.'/PHPMailerAutoload.php');
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        mekeng_phpmailer_apply_local_postfix($mail);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Timeout = 600;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ),
        );

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;
        $mail->AltBody = strip_tags($content);

        $mail->setFrom($from_email, $from_name);
        $mail->Sender = $from_email;
        $mail->addReplyTo($from_email, $from_name);
        $mail->clearAddresses();
        $mail->addAddress($to);

        foreach ($attachments as $att) {
            if (!empty($att['path']) && is_readable($att['path'])) {
                $name = isset($att['name']) && $att['name'] !== '' ? $att['name'] : basename($att['path']);
                $mail->addAttachment($att['path'], $name);
            }
        }

        $msg_id_domain = (defined('G5_URL') && G5_URL) ? parse_url(G5_URL, PHP_URL_HOST) : 'webmail.mekeng.com';
        if (empty($msg_id_domain)) {
            $msg_id_domain = 'webmail.mekeng.com';
        }
        // 중복 Message-ID 헤더 방지 (Gmail RFC 5322 차단 대응)
        $mail->MessageID = '<' . uniqid('mek.', true) . '.' . time() . '@' . $msg_id_domain . '>';
        $mail->addCustomHeader('X-Mailer', 'MEK-WebMailer/1.0');
        if ($add_list_unsubscribe && function_exists('generate_unsubscribe_key')) {
            $base_url = defined('G5_URL') ? rtrim(G5_URL, '/') : '';
            $unsub_url = $base_url . '/plus/mailer/?action=unsubscribe&key=' . urlencode(generate_unsubscribe_key($to)) . '&email=' . urlencode($to);
            if (!empty(trim($base_url))) {
                $mail->addCustomHeader('List-Unsubscribe', '<' . $unsub_url . '>');
                $mail->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            }
        }

        $mail->send();
        return array(
            'success' => true,
            'error' => $smtp_error ? 'SMTP 실패 후 로컬 Postfix로 재발송 성공' : ''
        );
    } catch (\Exception $e) {
        $msg = $e->getMessage();
        if (isset($mail) && is_object($mail) && !empty($mail->ErrorInfo)) {
            $msg .= ' [' . $mail->ErrorInfo . ']';
        }
        if (function_exists('error_log')) {
            error_log('send_mail_via_smtp_fallback: ' . $msg);
        }
        return array(
            'success' => false,
            'error' => '로컬 Postfix 발송 실패' . ($smtp_error ? ' (이전 SMTP: ' . $smtp_error . ')' : '') . ': ' . $msg
        );
    }
}

/**
 * 문의 폼 등 웹(Apache)에서 sales@webmail 등으로 HTML 메일 발송.
 * Postfix authorized_submit_users 로 apache 의 sendmail(mail()) 경로가 막혀 있으므로
 * 로컬 Postfix로 제출한다. 포트/TLS는 config(G5_SMTP_PORT, G5_SMTP_SECURE)와 mailer() 동일.
 *
 * @param string $to      수신 주소
 * @param string $subject 제목 (MIME 인코딩된 문자열 가능)
 * @param string $html_body HTML 본문
 * @param string $from_email From / Reply-To (문의자 메일). envelope(반송)은 사내 주소로 고정.
 * @return bool
 */
function mekeng_form_send_html_mail($to, $subject, $html_body, $from_email) {
    if (!class_exists('PHPMailer')) {
        include_once(G5_PHPMAILER_PATH.'/PHPMailerAutoload.php');
    }
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        mekeng_phpmailer_apply_local_postfix($mail);
        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 60;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ),
        );
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html_body;
        $mail->clearAddresses();
        $mail->addAddress($to);
        $from_email = trim($from_email);
        if ($from_email === '') {
            $from_email = 'noreply@mekeng.com';
        }
        $mail->setFrom($from_email);
        $mail->addReplyTo($from_email);
        // setFrom만 쓰면 MAIL FROM=문의자가 되어 수신 거절 시 반송이 문의자에게 감
        $mail->Sender = 'msk@mekeng.com';
        $mail->send();
        return true;
    } catch (\Exception $e) {
        if (function_exists('error_log')) {
            error_log('mekeng_form_send_html_mail: '.$e->getMessage());
        }
        return false;
    }
}

/**
 * 구독 취소 키 생성
 */
function generate_unsubscribe_key($email) {
    return base64_encode($email . '|' . md5($email . (defined('G5_STRING_ENCRYPT_FUNCTION') ? G5_STRING_ENCRYPT_FUNCTION : 'create_hash')));
}

/**
 * 연하장 스타일의 이메일 템플릿 생성
 * 다양한 이메일 클라이언트(네이버, 구글, 아웃룩 등) 호환성을 위해 테이블 기반 레이아웃과 인라인 스타일 사용
 */
function get_newyear_card_template($subject, $content, $unsubscribe_key = '', $email = '', $greeting = '', $header_title = '') {
    global $config, $member;
    $site_url = G5_URL;
    $site_name = $config['cf_title'] ?? 'MEK+';
    $current_year = date('Y');
    $current_date = date('Y년 m월 d일');
    
    // 헤더 제목 (입력값이 없으면 기본값 사용)
    if (empty($header_title)) {
        $header_title = "🎊 {$current_year}년 새해 인사 🎊";
    } else {
        // 이모지가 포함되어 있을 수 있으므로 htmlspecialchars는 사용하지 않고 그대로 사용
        $header_title = trim($header_title);
    }
    
    // 이메일이 제공되지 않은 경우 unsubscribe_key에서 추출 시도
    if (empty($email) && !empty($unsubscribe_key)) {
        $decoded = base64_decode($unsubscribe_key);
        if ($decoded) {
            $parts = explode('|', $decoded);
            if (isset($parts[0])) {
                $email = $parts[0];
            }
        }
    }
    
    // 구독 취소 URL 생성 (새로운 로그 시스템과 연동)
    $unsubscribe_url = '';
    if (!empty($unsubscribe_key) && !empty($email)) {
        // 수신 거부 페이지로 연결 (mailer의 unsubscribe 기능 사용)
        $unsubscribe_url = $site_url . '/plus/mailer/?action=unsubscribe&key=' . urlencode($unsubscribe_key) . '&email=' . urlencode($email);
    }
    
    // 발신자 정보
    $sender_name = !empty($member['mb_name']) ? htmlspecialchars($member['mb_name']) : $site_name;
    $sender_tel = !empty($member['mb_tel']) ? htmlspecialchars($member['mb_tel']) : '';
    $sender_email = !empty($member['mb_email']) ? htmlspecialchars($member['mb_email']) : '';
    
    // 연하장 인사말 (입력값이 없으면 기본값 사용)
    if (empty($greeting)) {
        $greeting = "새해 복 많이 받으세요!\n{$current_year}년 새해를 맞이하여\n건강과 행복이 가득하시기를 기원합니다.";
    }
    // 줄바꿈을 <br>로 변환
    $greeting = nl2br(htmlspecialchars($greeting));

    // 발신자 정보 HTML 생성
    $sender_info_html = "성명: {$sender_name}<br>";
    if (!empty($sender_tel)) {
        $sender_info_html .= "연락처: {$sender_tel}<br>";
    }
    if (!empty($sender_email)) {
        $sender_info_html .= "이메일: {$sender_email}<br>";
    }
    
    // 구독 취소 링크 HTML 생성
    $unsubscribe_html = '';
    if (!empty($unsubscribe_url)) {
        $unsubscribe_html = '<br><br><a href="' . htmlspecialchars($unsubscribe_url) . '" style="color: #999999; text-decoration: underline;">뉴스레터 구독 취소</a>';
    }

    return <<<EOT
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{$subject}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Apple SD Gothic Neo', 'Malgun Gothic', '맑은 고딕', 'Nanum Gothic', Arial, sans-serif;">
    <!-- 전체 래퍼 테이블 -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <!-- 메인 컨테이너 테이블 (최대 600px) -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- 상단 헤더 (연하장 느낌의 따뜻한 색상) -->
                    <tr>
                        <td style="background-color: #ff6b6b; padding: 40px 30px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #ffffff; font-size: 28px; font-weight: bold; line-height: 1.4;">
                                        {$header_title}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #ffffff; font-size: 16px; padding-top: 15px;">
                                        MEK Inc. & MEK Engineering
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- 인사말 섹션 -->
                    <tr>
                        <td style="padding: 40px 30px; text-align: center; background-color: #fffaf0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #333333; font-size: 18px; line-height: 1.8; font-weight: 500;">
                                        {$greeting}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- 본문 내용 -->
                    <tr>
                        <td style="padding: 30px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #555555; font-size: 15px; line-height: 1.8;">
                                        {$content}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- 발신자 정보 -->
                    <tr>
                        <td style="padding: 25px 30px; background-color: #fafafa; border-top: 1px solid #eeeeee;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #777777; font-size: 13px; line-height: 1.6;">
                                        <strong style="color: #555555;">발신자 정보</strong><br>
                                        {$sender_info_html}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- 하단 푸터 -->
                    <tr>
                        <td style="padding: 30px; background-color: #f8f9fa; text-align: center; border-top: 2px solid #eeeeee;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding-bottom: 15px;">
                                        <a href="{$site_url}/" style="display: inline-block; padding: 12px 30px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 15px; font-weight: bold;">{$site_name} 바로가기</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #999999; font-size: 12px; line-height: 1.6; padding-top: 15px;">
                                        본 메일은 발신전용입니다.<br>
                                        문의사항이 있으신 경우 사이트를 방문해주세요.{$unsubscribe_html}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
EOT;
}

function get_newsletter_template($subject, $content, $unsubscribe_key, $email = '') {
    global $config, $member;
    $site_url = G5_URL;
    $site_name = $config['cf_title'];
    $current_date = date('Y.m.d');

    // 이메일이 제공되지 않은 경우 unsubscribe_key에서 추출 시도
    if (empty($email) && !empty($unsubscribe_key)) {
        $decoded = base64_decode($unsubscribe_key);
        if ($decoded) {
            $parts = explode('|', $decoded);
            if (isset($parts[0])) {
                $email = $parts[0];
            }
        }
    }

    // 구독 취소 URL 생성 (mailer의 unsubscribe 기능 사용)
    $unsubscribe_url = '';
    if (!empty($unsubscribe_key) && !empty($email)) {
        $unsubscribe_url = $site_url . '/plus/mailer/?action=unsubscribe&key=' . urlencode($unsubscribe_key) . '&email=' . urlencode($email);
    }

    $subject_esc = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    $site_name_esc = htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8');
    $greeting_line = htmlspecialchars($current_date . ' 『' . $site_name . '』', ENT_QUOTES, 'UTF-8');

    $mb_name = isset($member['mb_name']) ? htmlspecialchars((string) $member['mb_name'], ENT_QUOTES, 'UTF-8') : '';
    $mb_tel = isset($member['mb_tel']) ? htmlspecialchars((string) $member['mb_tel'], ENT_QUOTES, 'UTF-8') : '';
    $mb_email = isset($member['mb_email']) ? htmlspecialchars((string) $member['mb_email'], ENT_QUOTES, 'UTF-8') : '';

    // 발신자 정보 (인라인 스타일 — 기본 템플릿 전용, 수신 메일 클라이언트 호환)
    $sender_info = <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;margin-top:28px;">
<tr>
<td style="padding:16px 0 0 0;border-top:1px solid #e2e8f0;font-family:'Malgun Gothic','맑은 고딕',AppleGothic,'Apple SD Gothic Neo',sans-serif;font-size:12px;line-height:1.65;color:#64748b;">
<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;border-left:3px solid #1565c0;padding-left:22px;">
<tr><td style="padding:0 0 4px 0;font-weight:600;color:#475569;">발신자 정보</td></tr>
<tr><td style="padding:0;">성명: {$mb_name}</td></tr>
<tr><td style="padding:0;">연락처: {$mb_tel}</td></tr>
<tr><td style="padding:0;">이메일: {$mb_email}</td></tr>
</table>
</td>
</tr>
</table>
HTML;

    // 기업용 심플 레이아웃: 테이블 + 인라인 스타일 (연하장 템플릿 미사용 시)
    return <<<EOT
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{$subject_esc}</title>
</head>
<body style="margin:0;padding:0;background-color:#eef2f6;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#eef2f6;">
<tr>
<td align="center" style="padding:32px 16px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;border-collapse:collapse;background-color:#ffffff;border:1px solid #d8dee6;box-shadow:0 1px 3px rgba(15,23,42,0.06);">
<tr>
<td style="height:6px;background-color:#1565c0;line-height:6px;font-size:0;">&nbsp;</td>
</tr>
<tr>
<td style="padding:28px 28px 8px 28px;font-family:'Malgun Gothic','맑은 고딕',AppleGothic,'Apple SD Gothic Neo',sans-serif;">
<p style="margin:0 0 6px 0;font-size:11px;letter-spacing:0.06em;color:#64748b;text-transform:uppercase;">{$site_name_esc}</p>
<h1 style="margin:0;font-size:19px;font-weight:600;line-height:1.4;color:#0f172a;letter-spacing:-0.02em;">{$subject_esc}</h1>
</td>
</tr>
<tr>
<td style="padding:8px 28px 28px 28px;font-family:'Malgun Gothic','맑은 고딕',AppleGothic,'Apple SD Gothic Neo',sans-serif;font-size:15px;line-height:1.65;color:#334155;">
<p style="margin:0 0 22px 0;color:#475569;font-size:14px;">{$greeting_line}</p>
{$content}
{$sender_info}
</td>
</tr>
<tr>
<td style="padding:22px 28px;background-color:#f8fafc;border-top:1px solid #e2e8f0;font-family:'Malgun Gothic','맑은 고딕',AppleGothic,'Apple SD Gothic Neo',sans-serif;font-size:12px;line-height:1.65;color:#64748b;text-align:center;">
<a href="{$site_url}/" style="color:#1565c0;text-decoration:none;font-weight:600;">{$site_name_esc} 웹사이트</a>
<p style="margin:14px 0 10px 0;">본 메일은 발신 전용입니다. 문의사항이 있으시면 홈페이지를 이용해 주세요.</p>
<p style="margin:0;"><a href="{$unsubscribe_url}" style="color:#94a3b8;text-decoration:underline;">메일 수신 거부</a></p>
</td>
</tr>
</table>
<p style="margin:16px 0 0 0;font-family:'Malgun Gothic','맑은 고딕',sans-serif;font-size:11px;color:#94a3b8;text-align:center;">&copy; {$site_name_esc}</p>
</td>
</tr>
</table>
</body>
</html>
EOT;
}

// 새글 정보를 HTML로 변환 (기본 뉴스레터 템플릿용 — 클래스 없이 인라인 스타일)
function get_article_html($article) {
    $title = $article['wr_subject'];
    if (isset($article['is_ad']) && $article['is_ad']) {
        $title = '[광고] ' . $title;
    }
    $title_esc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $href_esc = htmlspecialchars($article['href'], ENT_QUOTES, 'UTF-8');
    $excerpt = htmlspecialchars(cut_str(strip_tags($article['wr_content']), 200), ENT_QUOTES, 'UTF-8');

    return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;margin:0 0 18px 0;border-bottom:1px solid #e2e8f0;">'
        . '<tr><td style="padding:0 0 16px 0;">'
        . '<a href="' . $href_esc . '" style="display:block;font-size:15px;font-weight:600;color:#1565c0;text-decoration:none;line-height:1.4;margin-bottom:8px;">' . $title_esc . '</a>'
        . '<div style="font-size:14px;line-height:1.6;color:#64748b;">' . $excerpt . '</div>'
        . '</td></tr></table>';
}
?>
