<?php
if (!defined('_GNUBOARD_')) exit;

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
            
            // 로컬 postfix 사용 옵션이 있으면 mail() 함수로 직접 발송
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
                
                $from_email = $smtp_config['sc_from_email'];
                $from_name = !empty($smtp_config['sc_from_name']) ? $smtp_config['sc_from_name'] : $config['cf_title'];
                
                $mail->setFrom($from_email, $from_name);
                $mail->addReplyTo($from_email, $from_name);
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
                // 기본 SMTP 인증 정보는 설정 파일에 없으므로 사용자 입력 필요
                // 포털 메일 발송을 위해 SPF가 설정된 webmail.mekeng.com 도메인 사용
                $from_email = 'sales@mekeng.com';
                $from_name = $config['cf_title'] ?? '';
                $mail->setFrom($from_email, $from_name);
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
        $mail->addCustomHeader('Message-ID', '<' . uniqid('mek.', true) . '.' . time() . '@' . $msg_id_domain . '>');
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
        
        if ($mail->send()) {
            return ['success' => true, 'error' => ''];
        } else {
            // SMTP 발송 실패 시 mail() 함수로 자동 폴백
            $error_info = $mail->ErrorInfo;
            return send_mail_via_smtp_fallback($to, $subject, $content, $error_info, $attachments, $add_list_unsubscribe);
        }
        
    } catch (\Exception $e) {
        // 예외 발생 시 mail() 함수로 자동 폴백
        return send_mail_via_smtp_fallback($to, $subject, $content, $e->getMessage(), $attachments, $add_list_unsubscribe);
    }
}

/**
 * SMTP 설정이 없을 때 또는 SMTP 발송 실패 시 PHP mail() 함수로 폴백
 * postfix가 제대로 설정되어 있으면 포털 메일로도 정상 발송됨
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
    
    $from_email = 'sales@mekeng.com';
    $from_name = $config['cf_title'] ?? '';
    
    if (preg_match('/[^\x00-\x7F]/', $subject)) {
        $encoded_subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
    } else {
        $encoded_subject = $subject;
    }
    
    $headers = 'From: ' . $from_email . "\r\n";
    $headers .= 'Reply-to: ' . $from_email . "\r\n";
    $msg_id_domain = (defined('G5_URL') && G5_URL) ? parse_url(G5_URL, PHP_URL_HOST) : 'mekeng.com';
    if (empty($msg_id_domain)) {
        $msg_id_domain = 'mekeng.com';
    }
    $headers .= 'Message-ID: <' . uniqid('mek.', true) . '.' . time() . '@' . $msg_id_domain . '>' . "\r\n";
    $headers .= 'X-Mailer: MEK-WebMailer/1.0' . "\r\n";
    if ($add_list_unsubscribe && function_exists('generate_unsubscribe_key')) {
        $base_url = defined('G5_URL') ? rtrim(G5_URL, '/') : '';
        $unsub_url = $base_url . '/plus/mailer/?action=unsubscribe&key=' . urlencode(generate_unsubscribe_key($to)) . '&email=' . urlencode($to);
        if (!empty(trim($base_url))) {
            $headers .= 'List-Unsubscribe: <' . $unsub_url . '>' . "\r\n";
            $headers .= 'List-Unsubscribe-Post: List-Unsubscribe=One-Click' . "\r\n";
        }
    }
    
    $body = $content;
    
    // 첨부파일이 있으면 multipart/mixed 로 본문 구성
    if (!empty($attachments)) {
        $boundary = '----=_Part_' . md5(uniqid((string)mt_rand(), true));
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . "\r\n";
        $body = '--' . $boundary . "\r\n";
        $body .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        $body .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
        $body .= chunk_split(base64_encode($content)) . "\r\n";
        
        foreach ($attachments as $att) {
            if (empty($att['path']) || !is_readable($att['path'])) {
                continue;
            }
            $name = isset($att['name']) && $att['name'] !== '' ? $att['name'] : basename($att['path']);
            $filename_safe = '=?UTF-8?B?' . base64_encode($name) . '?=';
            $body .= '--' . $boundary . "\r\n";
            $body .= 'Content-Type: application/octet-stream; name="' . $filename_safe . '"' . "\r\n";
            $body .= 'Content-Transfer-Encoding: base64' . "\r\n";
            $body .= 'Content-Disposition: attachment; filename="' . $filename_safe . '"' . "\r\n\r\n";
            $body .= chunk_split(base64_encode(file_get_contents($att['path']))) . "\r\n";
        }
        $body .= '--' . $boundary . '--';
    } else {
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    }
    
    $result = @mail($to, $encoded_subject, $body, $headers);
    
    if ($result) {
        return [
            'success' => true,
            'error' => $smtp_error ? 'SMTP 실패 후 mail() 함수로 발송 성공' : ''
        ];
    } else {
        $error_msg = 'PHP mail() 함수로 발송 실패';
        if ($smtp_error) {
            $error_msg .= ' (SMTP 오류: ' . $smtp_error . ')';
        }
        return [
            'success' => false,
            'error' => $error_msg
        ];
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
                <p><a href="{$unsubscribe_url}" class="unsubscribe">메일 수신 거부</a></p>           </div>
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
