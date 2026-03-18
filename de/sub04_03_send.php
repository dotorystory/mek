<meta charset="utf-8">
<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');

if(isset($_POST['email'])) {

  // 파일 업로드 처리
  $file = $_FILES['attachment'];
  $tmpName = $file['tmp_name'];
  $filename = basename($file['name']);
  $target_dir = G5_DATA_PATH . "/mail/"; // 파일 업로드 디렉토리
  $target_file = $target_dir . $filename; // 업로드할 파일 경로

  // Rename file if it already exists
  $i = 1;
  while (file_exists($target_file)) {
      $filename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $i . '.' . pathinfo($filename, PATHINFO_EXTENSION);
      $target_file = $target_dir . $filename;
      $i++;
  }

  // 디렉토리 생성
  if (!is_dir($target_dir)) {
    @mkdir($target_dir, 0707);
    @chmod($target_dir, 0707);
  }

  move_uploaded_file($tmpName, $target_file);

  function died($error) {
   // your error code can go here
   echo "<script> alert('Mailversand fehlgeschlagen.');";
   echo "history.go(-1);";
   echo "</script>";
   die();
  }

  function generateRandomString($length = 10) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
  }

  if(!isset($_POST['title']) ||
      !isset($_POST['group']) || //다중체크
      !isset($_POST['place']) ||
      !isset($_POST['name']) ||
      !isset($_POST['email']) ||
      !isset($_POST['cell']) ||
      !isset($_POST['date']) ||
      !isset($_POST['process']) ||
      !isset($_POST['thickness']) ||
      !isset($_POST['width']) ||
      !isset($_POST['comments']) ||
      !isset($_POST['agree'])) {
      died('Entschuldigung,\nEs gibt ein Problem mit dem von Ihnen eingereichten Formular.\nBitte überprüfen Sie das Formular erneut.');
  }

  $title = $_POST['title']; // required
  $group = implode(', ', $_POST['group']); // 필수항목
  $place = $_POST['place']; // required
  $name = $_POST['name']; // required
  $email_from = $_POST['email']; // required
  $phone = $_POST['phone']; // 선택
  $cell = $_POST['cell']; // required
  $date = $_POST['date'];
  $process = $_POST['process'];
  $thickness = $_POST['thickness'];
  $width = $_POST['width'];
  $comments = $_POST['comments']; // required
  // $file_name = $file['name'];
  $file_url = G5_DATA_URL . "/mail/" . $filename; // 파일 경로

  $email_to = "sales@webmail.mekeng.com";

  $email_subject = "[A/S Anfrage]";
  $email_subject = '=?UTF-8?B?'.base64_encode($email_subject).'?=';

  function clean_string($string) {
    $bad = array("content-type","bcc:","to:","cc:","href");
    return str_replace($bad,"",$string);
  }

  $email_message .= "Firmenname : ".clean_string($title)."<br><br>";
  $email_message .= "Anfragegerät : ".clean_string($group)."<br><br>"; //필수항목
  $email_message .= "Installationsbereich (oder Land) : ".clean_string($place)."<br><br>";
  $email_message .= "E-Mail : ".clean_string($email_from)."<br><br>";
  $email_message .= "Name (Kontaktperson) : ".clean_string($name)."<br><br>";
  $email_message .= "Telefonnummer (Festnetz) : ".clean_string($phone)."<br><br>";
  $email_message .= "Mobiltelefon : ".clean_string($cell)."<br><br>";
  $email_message .= "Kaufdatum : ".clean_string($date)."<br><br>";
  $email_message .= "Angewandter Prozess : ".clean_string($process)."<br><br>";
  $email_message .= "Messdicke : ".clean_string($thickness)."<br><br>";
  $email_message .= "Messbreite : ".clean_string($width)."<br><br>";
  $email_message .= "A/S Anfragenachricht : ".clean_string(nl2br($comments))."<br><br>";

  if(!empty($filename) && file_exists($target_file)) {
      $email_message .= "Anhang : <a target='_blank' href='" . $file_url . "' download>".$filename."</a><br><br>";
  } else {
      $email_message .= "Anhang : Keine<br><br>";
  }

// create email headers
$headers = 'From: '.$email_from."\r\n";
$headers .= 'Reply-to: '.$email_from."\r\n";
$headers .= 'Content-type: text/html'."\r\n";
// $headers .= 'Content-Disposition: attachment';
// $headers .= 'filename="example.txt"';

// 제목이 깨질경우 아래 캐릭터셋 적용

@mail($email_to, $email_subject, $email_message, $headers);

// Newsletter-Abonnement-Verarbeitung (automatisches Abonnement bei Datenschutz-Zustimmung)
if (isset($_POST['agree']) && $_POST['agree'] == 'on') {
    // E-Mail-Log-Aufzeichnungsfunktion (identisch mit subscribe/index.php)
    if (!function_exists('log_mail_subscribe')) {
        function log_mail_subscribe($type, $email, $mb_id = null, $success = true) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $mb_id = $mb_id ?? ($GLOBALS['member']['mb_id'] ?? null);
            
            sql_query("INSERT INTO g5_mail_log (ml_type, ml_email, ml_mb_id, ml_ip, ml_success, ml_datetime) 
                      VALUES ('".sql_escape_string($type)."', '".sql_escape_string($email)."', 
                              ".($mb_id ? "'".sql_escape_string($mb_id)."'" : "NULL").", 
                              '".sql_escape_string($ip)."', ".($success ? 1 : 0).", '".G5_TIME_YMDHIS."')");
        }
    }
    
    // E-Mail-Duplikatsprüfung und Abonnement-Verarbeitung
    $subscribe = sql_fetch("SELECT sb_id, sb_email, sb_subscribe FROM g5_subscribe WHERE sb_email = '".sql_escape_string($email_from)."'");
    
    if ($subscribe) {
        if ($subscribe['sb_subscribe'] != 1) {
            // Abonnement-Status aktualisieren
            sql_query("UPDATE g5_subscribe SET sb_subscribe = 1, sb_updatedate = '".G5_TIME_YMDHIS."' WHERE sb_id = '{$subscribe['sb_id']}'");
            log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
        }
    } else {
        // Neuen Abonnenten hinzufügen
        sql_query("INSERT INTO g5_subscribe (sb_email, sb_subscribe, sb_regdate, sb_updatedate) 
                  VALUES ('".sql_escape_string($email_from)."', 1, '".G5_TIME_YMDHIS."', '".G5_TIME_YMDHIS."')");
        log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
    }
    
    // Bei Mitgliedern mb_mailling aktualisieren
    if ($is_member) {
        sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 1 WHERE mb_id = '{$member['mb_id']}'");
    }
}
?>

<!-- include your own success html here -->

<script>
alert ("Vielen Dank für Ihre Anfrage.\nWir werden so schnell wie möglich antworten.");
location.href='/de/sub04_03.php';
</script>

<?php
}
?>
