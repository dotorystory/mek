<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 로컬 Postfix 제출: `lib/mailer.lib.php` + `config.php`의 G5_SMTP_PORT / G5_SMTP_SECURE 와 동일.
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
 * @return array ['success' => bool, 'error' => string]
 */
function send_mail_via_smtp($to, $subject, $content, $use_smtp_config = true) {
    global $config, $g5;
    
    if (!class_exists('PHPMailer')) {
        include_once(G5_PHPMAILER_PATH.'/PHPMailerAutoload.php');
    }
    
    try {
        $mail = new PHPMailer(true);
        
        // SMTP 설정 가져오기
        if ($use_smtp_config) {
            $smtp_config = sql_fetch("SELECT * FROM g5_smtp_config WHERE sc_active = 1 LIMIT 1");
            
            // 로컬 Postfix: PHPMailer + config 포트(587 등)
            if ($smtp_config && isset($smtp_config['sc_use_local_postfix']) && $smtp_config['sc_use_local_postfix'] == 1) {
                return send_mail_via_smtp_fallback($to, $subject, $content);
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
                
                $from_email = $smtp_config['sc_from_email'];
                $from_name = !empty($smtp_config['sc_from_name']) ? $smtp_config['sc_from_name'] : $config['cf_title'];
                
                $mail->setFrom($from_email, $from_name);
                $mail->addReplyTo($from_email, $from_name);
                $mail->Sender = $from_email;
            } else {
                // SMTP 설정이 없으면 기본 설정 사용
                return send_mail_via_smtp_fallback($to, $subject, $content);
            }
        } else {
            // 기본 설정 사용 (config.php의 G5_SMTP)
            if (defined('G5_SMTP') && G5_SMTP && G5_SMTP !== '127.0.0.1') {
                $mail->isSMTP();
                $mail->Host = G5_SMTP;
                $mail->Port = defined('G5_SMTP_PORT') ? intval(G5_SMTP_PORT) : 587;
                $mail->SMTPSecure = 'tls';
                $mail->SMTPAuth = true;
                // 기본 SMTP 인증 정보는 설정 파일에 없으므로 사용자 입력 필요
                $from_email = 'msk@mekeng.com';
                $from_name = $config['cf_title'] ?? '';
                $mail->setFrom($from_email, $from_name);
                $mail->Sender = $from_email;
            } else {
                return send_mail_via_smtp_fallback($to, $subject, $content);
            }
        }
        
        // 메일 내용 설정
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;
        $mail->AltBody = strip_tags($content);
        $mail->addAddress($to);
        
        // SMTP 옵션 설정 (자체 서명 인증서 허용)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // 디버그 모드 (필요시 활성화)
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'error_log';

        if (empty($mail->Sender) && !empty($mail->From)) {
            $mail->Sender = $mail->From;
        }
        if ($mail->send()) {
            return ['success' => true, 'error' => ''];
        } else {
            // SMTP 실패 시 로컬 Postfix( mailer() 와 동일 포트/TLS )
            $error_info = $mail->ErrorInfo;
            return send_mail_via_smtp_fallback($to, $subject, $content, $error_info);
        }
        
    } catch (\Exception $e) {
        return send_mail_via_smtp_fallback($to, $subject, $content, $e->getMessage());
    }
}

/**
 * SMTP 설정이 없을 때·로컬 Postfix 옵션·외부 SMTP 실패 시 로컬 Postfix로 폴백 (mailer()와 동일 포트/TLS).
 *
 * @param string $to 수신 이메일
 * @param string $subject 제목
 * @param string $content 내용 (HTML)
 * @param string $smtp_error SMTP 발송 실패 시 오류 메시지 (선택사항)
 * @return array ['success' => bool, 'error' => string]
 */
function send_mail_via_smtp_fallback($to, $subject, $content, $smtp_error = '') {
    global $config;

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
 * 로컬 Postfix로 제출. 포트/TLS는 config(G5_SMTP_PORT, G5_SMTP_SECURE)와 mailer() 동일.
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
    
    // 구독 취소 URL 생성 (subscribe 폴더 사용)
    $unsubscribe_url = $site_url . '/plus/subscribe/?action=unsubscribe&key=' . urlencode($unsubscribe_key) . '&email=' . urlencode($email);
    
    // 기본 인사말
    $greeting = <<<EOT
{$current_date} 『{$site_name}』
EOT;

    // 발신자 정보
    $sender_info = <<<EOT
<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
    <p style="font-size: 12px; color: #777;">
        발신자 정보:<br>
        성명: {$member['mb_name']}<br>
        연락처: {$member['mb_tel']}<br>
        이메일: {$member['mb_email']}
    </p>
    <br>
</div>
EOT;

    return <<<EOT
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            .newsletter-container { max-width: 600px; margin: 0 auto; font-family: 'Apple SD Gothic Neo', 'Malgun Gothic', '맑은 고딕', sans-serif; }
            .header { background: #f8f9fa; padding: 20px; text-align: center; }
            .content { padding: 20px; line-height: 1.6; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; }
            .article { margin-bottom: 20px; padding: 15px; border: 1px solid #eee; }
            .article-title { color: #333; text-decoration: none; font-weight: bold; }
            .article-excerpt { color: #666; margin-top: 10px; }
            .unsubscribe { color: #999; text-decoration: none; }
            .greeting { margin-bottom: 30px; line-height: 1.8; }
        </style>
    </head>
    <body>
        <div class="newsletter-container">
            <div class="header">
                <h1>{$subject}</h1>
            </div>
            
            <div class="content">
                <div class="greeting">
                    {$greeting}
                </div>
                {$content}
                {$sender_info}
            </div>
            
            <div class="footer">
                <p><a href="{$site_url}/" style="font-size: 20px; font-weight: bold; text-decoration: none;">{$site_name} 바로가기</a></p>
                <p>본 메일은 발신전용입니다. 문의사항이 있으신 경우 사이트를 방문해주세요.</p>
                <br>
                <p><a href="{$unsubscribe_url}" class="unsubscribe">뉴스레터 구독 취소</a></p>
            </div>
        </div>
    </body>
    </html>
EOT;
}

// 새글 정보를 HTML로 변환
function get_article_html($article) {
    $title = $article['wr_subject'];
    if (isset($article['is_ad']) && $article['is_ad']) {
        $title = '[광고] ' . $title;
    }
    
    $html = '<div class="article">';
    $html .= '<a href="'.$article['href'].'" class="article-title">'.$title.'</a>';
    $html .= '<div class="article-excerpt">'.cut_str(strip_tags($article['wr_content']), 200).'</div>';
    $html .= '</div>';
    return $html;
}
?>
