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

    try {
        $mail = new PHPMailer(); // defaults to using php "mail()"
        if (defined('G5_SMTP') && G5_SMTP) {
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->Host = G5_SMTP; // SMTP server
            if(defined('G5_SMTP_PORT') && G5_SMTP_PORT)
                $mail->Port = G5_SMTP_PORT;
            else
                $mail->Port = 25; // 기본 포트 설정
            
            // // 로컬 SMTP 서버(127.0.0.1)를 사용할 때는 TLS와 인증 비활성화
            //if (G5_SMTP == '127.0.0.1' || G5_SMTP == 'localhost') {
            //    $mail->SMTPAuth = false;
            //    $mail->SMTPSecure = false;
            //    $mail->SMTPAutoTLS = false;
            //    $mail->SMTPOptions = array(
            //        'ssl' => array(
            //            'verify_peer' => false,
            //            'verify_peer_name' => false,
            //            'allow_self_signed' => true
            //        )
            //    );
            //} else {
            //    // 외부 SMTP 서버를 사용할 때는 TLS와 인증 활성화
            //    $mail->SMTPAuth = true;
            //    $mail->SMTPSecure = 'tls';
            //    $mail->SMTPAutoTLS = true;
            //    $mail->SMTPOptions = array(
            //        'ssl' => array(
            //            'verify_peer' => true,
            //            'verify_peer_name' => true,
            //            'allow_self_signed' => true
            //        )
            //    );
            //}

	    // submission 587 port - 안정적인 설정
	    $mail->SMTPAuth = false;
	    $mail->SMTPSecure = false;  // TLS 비활성화로 변경
	    $mail->SMTPAutoTLS = false; // 자동 TLS 비활성화
	    
	    // 타임아웃 설정 추가
	    $mail->Timeout = 30;
	    $mail->SMTPKeepAlive = true;
	    
	    $mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	    ); 
        }
        
        $mail->CharSet = 'UTF-8';

        // 기존 설정
        $mail->From = $fmail;
        $mail->FromName = $fname;
        
        // // 기본 발신자 주소 설정
        // if (defined('G5_MAIL_FROM') && G5_MAIL_FROM) {
        //     $mail->From = G5_MAIL_FROM;
        // } else {
        //     $mail->From = $fmail;
        // }
        // if (defined('G5_MAIL_FROM_NAME') && G5_MAIL_FROM_NAME) {
        //     $mail->FromName = G5_MAIL_FROM_NAME;
        // } else {
        //     $mail->FromName = $fname;
        // }

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
        // 디버깅을 위한 추가 로그
        if (defined('G5_DEBUG') && G5_DEBUG) {
            error_log("PHPMailer Debug Info: " . print_r($mail->ErrorInfo, true));
        }
    }

    run_event('mail_send_result', $mail_send_result, $mail, $to, $cc, $bcc);

    return $mail_send_result;
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
