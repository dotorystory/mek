<?php
if (!defined('_GNUBOARD_')) exit;

include_once(G5_PHPMAILER_PATH.'/PHPMailerAutoload.php');

// 메일 보내기 (파일 여러개 첨부 가능)
// type : text=0, html=1, text+html=2
function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="")
{
    global $config;
    global $g5;

    // 메일발송 사용을 하지 않는다면
    if (!$config['cf_email_use']) return;

    if ($type != 1)
        $content = nl2br($content);

    $result = run_replace('mailer', $fname, $fmail, $to, $subject, $content, $type, $file, $cc, $bcc);
    
    if( is_array($result) && isset($result['return']) ){
        return $result['return'];
    }

    $mail_send_result = false;
    $mail = null;

    try {
        $mail = new PHPMailer(); // defaults to using php "mail()"
        if (defined('G5_SMTP') && G5_SMTP) {
            $mail->IsSMTP();
            $mail->Host = G5_SMTP;
            $mail->Port = (defined('G5_SMTP_PORT') && G5_SMTP_PORT) ? (int)G5_SMTP_PORT : 25;

            // Postfix submission 587: STARTTLS 사용. 로컬에서 TLS 미사용 시 config에서 G5_SMTP_SECURE 비우기
            $use_secure = defined('G5_SMTP_SECURE') ? G5_SMTP_SECURE : '';
            if ($use_secure === 'tls' || $use_secure === 'ssl') {
                $mail->SMTPSecure = $use_secure;
                $mail->SMTPAutoTLS = true;
            } else {
                $mail->SMTPAuth = false;
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            }
            $mail->SMTPAuth = false; // Postfix local/virtual 보통 인증 없음
            $mail->Timeout = 30;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
        }
        
        $mail->CharSet = 'UTF-8';

        // Virtual 메일: config 발신자 사용. 미설정 시 기존 $fmail/$fname 사용
        if (defined('G5_MAIL_FROM') && G5_MAIL_FROM) {
            $mail->From = G5_MAIL_FROM;
            $mail->FromName = (defined('G5_MAIL_FROM_NAME') && G5_MAIL_FROM_NAME) ? G5_MAIL_FROM_NAME : $fname;
        } else {
            $mail->From = $fmail;
            $mail->FromName = $fname;
        }

        $mail->Subject = $subject;
        $mail->AltBody = ""; // optional, comment out and test
        $mail->msgHTML($content);
        $mail->addAddress($to);
        if ($cc)
            $mail->addCC($cc);
        if ($bcc)
            $mail->addBCC($bcc);
        //print_r2($file); exit;
        if ($file != "") {
            foreach ($file as $f) {
                $mail->addAttachment($f['path'], $f['name']);
            }
        }

        $mail = run_replace('mail_options', $mail, $fname, $fmail, $to, $subject, $content, $type, $file, $cc, $bcc);

        if (!($mail_send_result = $mail->send())) {
            throw new Exception($mail->ErrorInfo);
        }
        
    } catch (Exception $e) {
        error_log("Mail sending error: " . $e->getMessage());
        if (defined('G5_DEBUG') && G5_DEBUG && $mail !== null && isset($mail->ErrorInfo)) {
            error_log("PHPMailer Debug Info: " . print_r($mail->ErrorInfo, true));
        }
        mailer_log('FAIL', $to, $subject, $e->getMessage() . ($mail !== null && isset($mail->ErrorInfo) ? ' | ' . $mail->ErrorInfo : ''));
    }
    if ($mail_send_result) {
        mailer_log('OK', $to, $subject, '');
    }

    run_event('mail_send_result', $mail_send_result, $mail, $to, $cc, $bcc);

    return $mail_send_result;
}

// 메일 발송 결과 로그 (data/log/mail.log)
function mailer_log($status, $to, $subject, $error_msg) {
    if (!defined('G5_DATA_PATH') || !G5_DATA_PATH) return;
    $subject = is_string($subject) ? $subject : '';
    $to = is_string($to) ? $to : '';
    $error_msg = is_string($error_msg) ? $error_msg : '';
    $log_dir = G5_DATA_PATH.'/log';
    if (!is_dir($log_dir)) @mkdir($log_dir, 0755, true);
    $log_file = $log_dir.'/mail.log';
    $line = date('Y-m-d H:i:s').' ['.$status.'] to='.$to.' subject='.str_replace(["\r","\n"], ' ', $subject);
    if ($error_msg !== '') $line .= ' error='.str_replace(["\r","\n"], ' ', $error_msg);
    @file_put_contents($log_file, $line.PHP_EOL, FILE_APPEND | LOCK_EX);
}

// 파일을 첨부함
function attach_file($filename, $tmp_name)
{
    // 서버에 업로드 되는 파일은 확장자를 주지 않는다. (보안 취약점)
    $dest_file = G5_DATA_PATH.'/tmp/'.str_replace('/', '_', $tmp_name);
    move_uploaded_file($tmp_name, $dest_file);
    $tmpfile = array("name" => $filename, "path" => $dest_file);
    return $tmpfile;
}
